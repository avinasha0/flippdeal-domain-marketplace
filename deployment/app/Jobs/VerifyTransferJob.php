<?php

namespace App\Jobs;

use App\Models\Transaction;
use App\Models\Domain;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class VerifyTransferJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 600; // 10 minutes
    public $tries = 5;
    public $backoff = [300, 600, 900, 1800]; // Retry after 5, 10, 15, 30 minutes

    protected $transactionId;
    protected $transferEvidence;

    /**
     * Create a new job instance.
     */
    public function __construct(int $transactionId, array $transferEvidence = [])
    {
        $this->transactionId = $transactionId;
        $this->transferEvidence = $transferEvidence;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            $transaction = Transaction::find($this->transactionId);
            
            if (!$transaction) {
                Log::warning('Transaction not found for transfer verification', [
                    'transaction_id' => $this->transactionId,
                ]);
                return;
            }

            $domain = $transaction->domain;
            if (!$domain) {
                Log::warning('Domain not found for transfer verification', [
                    'transaction_id' => $this->transactionId,
                ]);
                return;
            }

            Log::info('Starting transfer verification', [
                'transaction_id' => $this->transactionId,
                'domain_id' => $domain->id,
                'domain_name' => $domain->full_domain,
            ]);

            $verificationResult = $this->verifyTransfer($domain, $transaction);

            if ($verificationResult['verified']) {
                $this->handleSuccessfulTransfer($transaction, $verificationResult);
            } else {
                $this->handleFailedTransfer($transaction, $verificationResult);
            }

        } catch (\Exception $e) {
            Log::error('Transfer verification job failed', [
                'transaction_id' => $this->transactionId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            throw $e;
        }
    }

    /**
     * Verify the domain transfer
     */
    protected function verifyTransfer(Domain $domain, Transaction $transaction): array
    {
        $verificationMethods = [
            'whois' => $this->verifyWhoisTransfer($domain),
            'dns' => $this->verifyDnsTransfer($domain),
            'registrar' => $this->verifyRegistrarTransfer($domain),
        ];

        $verifiedMethods = array_filter($verificationMethods, fn($result) => $result['verified']);
        $allVerified = count($verifiedMethods) >= 2; // Require at least 2 methods to verify

        return [
            'verified' => $allVerified,
            'methods' => $verificationMethods,
            'verified_count' => count($verifiedMethods),
            'total_methods' => count($verificationMethods),
        ];
    }

    /**
     * Verify transfer via WHOIS data
     */
    protected function verifyWhoisTransfer(Domain $domain): array
    {
        try {
            $whoisData = $domain->whois_data ?? [];
            $expectedOwner = $transaction->buyer->name ?? '';
            $expectedRegistrar = $this->transferEvidence['registrar'] ?? '';

            $ownerMatch = false;
            $registrarMatch = false;

            if (isset($whoisData['owner']) && $expectedOwner) {
                $ownerMatch = str_contains(strtolower($whoisData['owner']), strtolower($expectedOwner));
            }

            if (isset($whoisData['registrar']) && $expectedRegistrar) {
                $registrarMatch = str_contains(strtolower($whoisData['registrar']), strtolower($expectedRegistrar));
            }

            return [
                'verified' => $ownerMatch || $registrarMatch,
                'owner_match' => $ownerMatch,
                'registrar_match' => $registrarMatch,
                'whois_data' => $whoisData,
            ];

        } catch (\Exception $e) {
            Log::error('WHOIS transfer verification failed', [
                'domain_id' => $domain->id,
                'error' => $e->getMessage(),
            ]);

            return [
                'verified' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Verify transfer via DNS changes
     */
    protected function verifyDnsTransfer(Domain $domain): array
    {
        try {
            $expectedNameservers = $this->transferEvidence['nameservers'] ?? [];
            $currentNameservers = $this->getCurrentNameservers($domain->full_domain);

            if (empty($expectedNameservers) || empty($currentNameservers)) {
                return [
                    'verified' => false,
                    'reason' => 'Missing nameserver data',
                ];
            }

            $matches = 0;
            foreach ($expectedNameservers as $expectedNs) {
                foreach ($currentNameservers as $currentNs) {
                    if (str_contains(strtolower($currentNs), strtolower($expectedNs))) {
                        $matches++;
                        break;
                    }
                }
            }

            $verified = $matches >= count($expectedNameservers) * 0.5; // At least 50% match

            return [
                'verified' => $verified,
                'expected_nameservers' => $expectedNameservers,
                'current_nameservers' => $currentNameservers,
                'matches' => $matches,
                'match_percentage' => $matches / count($expectedNameservers) * 100,
            ];

        } catch (\Exception $e) {
            Log::error('DNS transfer verification failed', [
                'domain_id' => $domain->id,
                'error' => $e->getMessage(),
            ]);

            return [
                'verified' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Verify transfer via registrar confirmation
     */
    protected function verifyRegistrarTransfer(Domain $domain): array
    {
        try {
            $transferId = $this->transferEvidence['transfer_id'] ?? '';
            $registrarConfirmation = $this->transferEvidence['registrar_confirmation'] ?? '';

            if (!$transferId && !$registrarConfirmation) {
                return [
                    'verified' => false,
                    'reason' => 'No registrar confirmation provided',
                ];
            }

            // This would integrate with registrar APIs to verify transfer
            // For now, we'll check if the evidence looks valid
            $hasTransferId = !empty($transferId) && strlen($transferId) > 5;
            $hasConfirmation = !empty($registrarConfirmation);

            return [
                'verified' => $hasTransferId || $hasConfirmation,
                'has_transfer_id' => $hasTransferId,
                'has_confirmation' => $hasConfirmation,
                'transfer_id' => $transferId,
            ];

        } catch (\Exception $e) {
            Log::error('Registrar transfer verification failed', [
                'domain_id' => $domain->id,
                'error' => $e->getMessage(),
            ]);

            return [
                'verified' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Get current nameservers for a domain
     */
    protected function getCurrentNameservers(string $domainName): array
    {
        try {
            $nameservers = dns_get_record($domainName, DNS_NS);
            return array_column($nameservers, 'target');
        } catch (\Exception $e) {
            Log::error('Failed to get nameservers', [
                'domain_name' => $domainName,
                'error' => $e->getMessage(),
            ]);
            return [];
        }
    }

    /**
     * Handle successful transfer verification
     */
    protected function handleSuccessfulTransfer(Transaction $transaction, array $verificationResult): void
    {
        try {
            DB::transaction(function () use ($transaction, $verificationResult) {
                // Update transaction status
                $transaction->update([
                    'escrow_state' => 'transfer_verified',
                    'transfer_verified_at' => now(),
                    'transfer_verification_data' => $verificationResult,
                ]);

                // Update domain status
                $transaction->domain->update([
                    'status' => 'sold',
                    'sold_at' => now(),
                    'sold_to_user_id' => $transaction->buyer_id,
                ]);

                // Trigger payout process
                // This would dispatch a PayoutJob
                Log::info('Transfer verified successfully, triggering payout', [
                    'transaction_id' => $transaction->id,
                    'domain_id' => $transaction->domain_id,
                ]);
            });

            Log::info('Transfer verification completed successfully', [
                'transaction_id' => $transaction->id,
                'verification_result' => $verificationResult,
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to handle successful transfer', [
                'transaction_id' => $transaction->id,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Handle failed transfer verification
     */
    protected function handleFailedTransfer(Transaction $transaction, array $verificationResult): void
    {
        try {
            $transaction->update([
                'escrow_state' => 'transfer_failed',
                'transfer_verification_data' => $verificationResult,
                'transfer_failed_at' => now(),
            ]);

            // Notify parties about failed transfer
            Log::info('Transfer verification failed', [
                'transaction_id' => $transaction->id,
                'verification_result' => $verificationResult,
            ]);

            // This would trigger notifications to buyer and seller
            // and potentially initiate refund process

        } catch (\Exception $e) {
            Log::error('Failed to handle failed transfer', [
                'transaction_id' => $transaction->id,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('VerifyTransferJob failed permanently', [
            'transaction_id' => $this->transactionId,
            'error' => $exception->getMessage(),
            'trace' => $exception->getTraceAsString(),
        ]);

        // Mark transaction as verification failed
        try {
            $transaction = Transaction::find($this->transactionId);
            if ($transaction) {
                $transaction->update([
                    'escrow_state' => 'verification_failed',
                    'verification_failed_at' => now(),
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Failed to update transaction after job failure', [
                'transaction_id' => $this->transactionId,
                'error' => $e->getMessage(),
            ]);
        }
    }
}