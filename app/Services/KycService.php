<?php

namespace App\Services;

use App\Models\User;
use App\Models\Transaction;
use App\Models\KycRequest;
use App\Models\AmlFlag;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class KycService
{
    protected $kycThreshold;

    public function __construct()
    {
        $this->kycThreshold = config('app.kyc_threshold', 10000); // Default $10,000
    }

    /**
     * Check if a transaction requires KYC verification
     */
    public function requiresKyc(Transaction $transaction): bool
    {
        return $transaction->amount >= $this->kycThreshold;
    }

    /**
     * Create a KYC request for a transaction
     */
    public function createKycRequest(Transaction $transaction, User $user): KycRequest
    {
        $kycRequest = KycRequest::create([
            'user_id' => $user->id,
            'transaction_id' => $transaction->id,
            'amount' => $transaction->amount,
            'status' => 'pending',
            'submitted_at' => now(),
        ]);

        // Update transaction to require KYC
        $transaction->update([
            'kyc_required' => true,
            'kyc_request_id' => $kycRequest->id,
        ]);

        Log::info('KYC request created', [
            'kyc_request_id' => $kycRequest->id,
            'transaction_id' => $transaction->id,
            'user_id' => $user->id,
            'amount' => $transaction->amount,
        ]);

        return $kycRequest;
    }

    /**
     * Approve a KYC request
     */
    public function approveKycRequest(KycRequest $kycRequest, User $admin, string $notes = null): bool
    {
        try {
            DB::transaction(function () use ($kycRequest, $admin, $notes) {
                $kycRequest->update([
                    'status' => 'approved',
                    'reviewed_by_admin_id' => $admin->id,
                    'reviewed_at' => now(),
                    'notes' => $notes,
                ]);

                // Update related transaction
                if ($kycRequest->transaction_id) {
                    $kycRequest->transaction->update([
                        'kyc_approved' => true,
                        'kyc_approved_at' => now(),
                    ]);
                }
            });

            Log::info('KYC request approved', [
                'kyc_request_id' => $kycRequest->id,
                'admin_id' => $admin->id,
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('Failed to approve KYC request', [
                'kyc_request_id' => $kycRequest->id,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Reject a KYC request
     */
    public function rejectKycRequest(KycRequest $kycRequest, User $admin, string $reason): bool
    {
        try {
            DB::transaction(function () use ($kycRequest, $admin, $reason) {
                $kycRequest->update([
                    'status' => 'rejected',
                    'reviewed_by_admin_id' => $admin->id,
                    'reviewed_at' => now(),
                    'rejection_reason' => $reason,
                ]);

                // Update related transaction
                if ($kycRequest->transaction_id) {
                    $kycRequest->transaction->update([
                        'kyc_approved' => false,
                    ]);
                }
            });

            Log::info('KYC request rejected', [
                'kyc_request_id' => $kycRequest->id,
                'admin_id' => $admin->id,
                'reason' => $reason,
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('Failed to reject KYC request', [
                'kyc_request_id' => $kycRequest->id,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Check if user can proceed with transaction (KYC approved or not required)
     */
    public function canProceedWithTransaction(Transaction $transaction): bool
    {
        if (!$this->requiresKyc($transaction)) {
            return true;
        }

        return $transaction->kyc_approved === true;
    }

    /**
     * Get KYC threshold amount
     */
    public function getKycThreshold(): float
    {
        return $this->kycThreshold;
    }

    /**
     * Update KYC threshold
     */
    public function setKycThreshold(float $threshold): void
    {
        $this->kycThreshold = $threshold;
        config(['app.kyc_threshold' => $threshold]);
    }
}
