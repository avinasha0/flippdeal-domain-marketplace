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

class ProcessPayout implements ShouldQueue
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
            Log::info('Processing payout for transaction', [
                'transaction_id' => $this->transaction->id,
                'seller_id' => $this->transaction->seller_id,
                'amount' => $this->transaction->net_amount,
            ]);

            // Get seller's PayPal email
            $seller = $this->transaction->seller;
            if (!$seller->paypal_email) {
                throw new \Exception('Seller does not have PayPal email configured');
            }

            // Initialize PayPal service
            $paypalService = app(PayPalService::class);

            // Process payout
            $payoutResult = $paypalService->createPayout([
                'email' => $seller->paypal_email,
                'amount' => $this->transaction->net_amount,
                'currency' => $this->transaction->currency,
                'note' => "Payout for domain: {$this->transaction->domain->full_domain}",
            ]);

            // Update transaction with payout details
            $this->transaction->update([
                'escrow_metadata' => array_merge(
                    $this->transaction->escrow_metadata ?? [],
                    [
                        'payout_id' => $payoutResult['payout_id'] ?? null,
                        'payout_status' => $payoutResult['status'] ?? 'pending',
                        'payout_processed_at' => now()->toISOString(),
                    ]
                ),
            ]);

            Log::info('Payout processed successfully', [
                'transaction_id' => $this->transaction->id,
                'payout_id' => $payoutResult['payout_id'] ?? null,
            ]);

        } catch (\Exception $e) {
            Log::error('Payout processing failed', [
                'transaction_id' => $this->transaction->id,
                'error' => $e->getMessage(),
            ]);

            // Update transaction with error details
            $this->transaction->update([
                'escrow_metadata' => array_merge(
                    $this->transaction->escrow_metadata ?? [],
                    [
                        'payout_error' => $e->getMessage(),
                        'payout_failed_at' => now()->toISOString(),
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
        Log::error('Payout job failed permanently', [
            'transaction_id' => $this->transaction->id,
            'error' => $exception->getMessage(),
        ]);

        // Notify admin about failed payout
        // This could trigger an email notification or create a support ticket
    }
}