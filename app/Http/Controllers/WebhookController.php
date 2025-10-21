<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Services\EscrowService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class WebhookController extends Controller
{
    protected EscrowService $escrowService;

    public function __construct(EscrowService $escrowService)
    {
        $this->escrowService = $escrowService;
    }

    /**
     * Handle PayPal payment webhooks.
     */
    public function handlePayPalWebhook(Request $request): Response
    {
        try {
            // Verify webhook signature
            if (!$this->verifyPayPalWebhook($request)) {
                Log::warning('Invalid PayPal webhook signature', [
                    'headers' => $request->headers->all(),
                    'body' => $request->getContent(),
                ]);
                return response('Unauthorized', 401);
            }

            $data = $request->json()->all();
            $eventType = $data['event_type'] ?? '';

            Log::info('PayPal webhook received', [
                'event_type' => $eventType,
                'data' => $data,
            ]);

            // Handle different event types
            switch ($eventType) {
                case 'PAYMENT.SALE.COMPLETED':
                    $this->handlePaymentCompleted($data);
                    break;

                case 'PAYMENT.SALE.DENIED':
                case 'PAYMENT.SALE.REFUNDED':
                    $this->handlePaymentRefunded($data);
                    break;

                case 'PAYMENT.SALE.PENDING':
                    $this->handlePaymentPending($data);
                    break;

                default:
                    Log::info('Unhandled PayPal webhook event', [
                        'event_type' => $eventType,
                    ]);
            }

            return response('OK', 200);

        } catch (\Exception $e) {
            Log::error('PayPal webhook processing failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'data' => $request->json()->all(),
            ]);

            return response('Internal Server Error', 500);
        }
    }

    /**
     * Handle payment completed event.
     */
    private function handlePaymentCompleted(array $data): void
    {
        $resource = $data['resource'] ?? [];
        $transactionId = $resource['custom_id'] ?? null;
        $paypalTransactionId = $resource['id'] ?? null;

        if (!$transactionId || !$paypalTransactionId) {
            Log::warning('PayPal payment completed webhook missing required data', [
                'resource' => $resource,
            ]);
            return;
        }

        // Find the transaction
        $transaction = Transaction::find($transactionId);
        if (!$transaction) {
            Log::warning('Transaction not found for PayPal webhook', [
                'transaction_id' => $transactionId,
            ]);
            return;
        }

        // Check if already processed (idempotency)
        if ($transaction->escrow_state !== Transaction::STATE_PENDING) {
            Log::info('Transaction already processed', [
                'transaction_id' => $transactionId,
                'current_state' => $transaction->escrow_state,
            ]);
            return;
        }

        // Mark transaction as in escrow
        $this->escrowService->markInEscrow(
            $transaction,
            $paypalTransactionId,
            [
                'webhook_data' => $resource,
                'processed_at' => now()->toISOString(),
            ]
        );

        Log::info('Transaction marked as in escrow', [
            'transaction_id' => $transactionId,
            'paypal_transaction_id' => $paypalTransactionId,
        ]);
    }

    /**
     * Handle payment refunded event.
     */
    private function handlePaymentRefunded(array $data): void
    {
        $resource = $data['resource'] ?? [];
        $transactionId = $resource['custom_id'] ?? null;

        if (!$transactionId) {
            Log::warning('PayPal refund webhook missing transaction ID', [
                'resource' => $resource,
            ]);
            return;
        }

        // Find the transaction
        $transaction = Transaction::find($transactionId);
        if (!$transaction) {
            Log::warning('Transaction not found for PayPal refund webhook', [
                'transaction_id' => $transactionId,
            ]);
            return;
        }

        // Check if already processed
        if ($transaction->isRefunded()) {
            Log::info('Transaction already refunded', [
                'transaction_id' => $transactionId,
            ]);
            return;
        }

        // Mark transaction as refunded
        $this->escrowService->refundEscrow(
            $transaction,
            'Refunded via PayPal webhook',
            [
                'webhook_data' => $resource,
                'processed_at' => now()->toISOString(),
            ]
        );

        Log::info('Transaction marked as refunded', [
            'transaction_id' => $transactionId,
        ]);
    }

    /**
     * Handle payment pending event.
     */
    private function handlePaymentPending(array $data): void
    {
        $resource = $data['resource'] ?? [];
        $transactionId = $resource['custom_id'] ?? null;

        if (!$transactionId) {
            Log::warning('PayPal pending webhook missing transaction ID', [
                'resource' => $resource,
            ]);
            return;
        }

        Log::info('Payment pending for transaction', [
            'transaction_id' => $transactionId,
            'resource' => $resource,
        ]);
    }

    /**
     * Verify PayPal webhook signature.
     */
    private function verifyPayPalWebhook(Request $request): bool
    {
        // In a production environment, you should verify the webhook signature
        // using PayPal's webhook signature verification process
        
        // For now, we'll do basic validation
        $requiredHeaders = [
            'paypal-transmission-id',
            'paypal-cert-id',
            'paypal-transmission-sig',
            'paypal-transmission-time',
        ];

        foreach ($requiredHeaders as $header) {
            if (!$request->header($header)) {
                return false;
            }
        }

        // In production, you would:
        // 1. Get PayPal's public certificate
        // 2. Verify the signature using the certificate
        // 3. Check the transmission time to prevent replay attacks
        
        return true;
    }

    /**
     * Handle generic payment webhook (for testing).
     */
    public function handleTestWebhook(Request $request): Response
    {
        $validator = Validator::make($request->all(), [
            'transaction_id' => 'required|exists:transactions,id',
            'event_type' => 'required|in:payment_completed,payment_refunded',
            'provider_transaction_id' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => 'Validation failed',
                'messages' => $validator->errors(),
            ], 400);
        }

        $data = $request->all();
        $transaction = Transaction::find($data['transaction_id']);

        try {
            switch ($data['event_type']) {
                case 'payment_completed':
                    $this->escrowService->markInEscrow(
                        $transaction,
                        $data['provider_transaction_id'],
                        ['test_webhook' => true]
                    );
                    break;

                case 'payment_refunded':
                    $this->escrowService->refundEscrow(
                        $transaction,
                        'Test webhook refund',
                        ['test_webhook' => true]
                    );
                    break;
            }

            return response()->json([
                'success' => true,
                'message' => 'Webhook processed successfully',
                'transaction_state' => $transaction->fresh()->escrow_state,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Webhook processing failed',
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}