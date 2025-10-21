<?php

namespace App\Jobs;

use App\Models\Transaction;
use App\Services\PayPalService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessRefund implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public Transaction $transaction;
    public int $tries = 3;
    public int $timeout = 120;

    /**
     * Create a new job instance.
     */
    public function __construct(Transaction $transaction)
    {
        $this->transaction = $transaction;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            Log::info('Processing refund for transaction', [
                'transaction_id' => $this->transaction->id,
                'buyer_id' => $this->transaction->buyer_id,
                'amount' => $this->transaction->amount,
            ]);

            // Check if we have a provider transaction ID to refund
            if (!$this->transaction->provider_txn_id) {
                throw new \Exception('No provider transaction ID found for refund');
            }

            // Initialize PayPal service
            $paypalService = app(PayPalService::class);

            // Process refund
            $refundResult = $paypalService->createRefund([
                'transaction_id' => $this->transaction->provider_txn_id,
                'amount' => $this->transaction->amount,
                'currency' => $this->transaction->currency,
                'reason' => $this->transaction->refund_reason ?? 'Domain transfer issue',
            ]);

            // Update transaction with refund details
            $this->transaction->update([
                'escrow_metadata' => array_merge(
                    $this->transaction->escrow_metadata ?? [],
                    [
                        'refund_id' => $refundResult['refund_id'] ?? null,
                        'refund_status' => $refundResult['status'] ?? 'pending',
                        'refund_processed_at' => now()->toISOString(),
                    ]
                ),
            ]);

            Log::info('Refund processed successfully', [
                'transaction_id' => $this->transaction->id,
                'refund_id' => $refundResult['refund_id'] ?? null,
            ]);

        } catch (\Exception $e) {
            Log::error('Refund processing failed', [
                'transaction_id' => $this->transaction->id,
                'error' => $e->getMessage(),
            ]);

            // Update transaction with error details
            $this->transaction->update([
                'escrow_metadata' => array_merge(
                    $this->transaction->escrow_metadata ?? [],
                    [
                        'refund_error' => $e->getMessage(),
                        'refund_failed_at' => now()->toISOString(),
                    ]
                ),
            ]);

            throw $e;
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('Refund job failed permanently', [
            'transaction_id' => $this->transaction->id,
            'error' => $exception->getMessage(),
        ]);

        // Notify admin about failed refund
        // This could trigger an email notification or create a support ticket
    }
}