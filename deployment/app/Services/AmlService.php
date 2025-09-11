<?php

namespace App\Services;

use App\Models\User;
use App\Models\Transaction;
use App\Models\AmlFlag;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AmlService
{
    protected $highVolumeThreshold = 50000; // $50,000 in 24 hours
    protected $rapidTransferThreshold = 5; // 5 transfers in 1 hour
    protected $multipleHighValueThreshold = 3; // 3 high-value transactions in 7 days

    /**
     * Run AML checks for a user
     */
    public function runAmlChecks(User $user): array
    {
        $flags = [];

        // Check for high volume transactions
        $highVolumeFlag = $this->checkHighVolume($user);
        if ($highVolumeFlag) {
            $flags[] = $highVolumeFlag;
        }

        // Check for rapid transfers
        $rapidTransferFlag = $this->checkRapidTransfers($user);
        if ($rapidTransferFlag) {
            $flags[] = $rapidTransferFlag;
        }

        // Check for email mismatch
        $emailMismatchFlag = $this->checkEmailMismatch($user);
        if ($emailMismatchFlag) {
            $flags[] = $emailMismatchFlag;
        }

        // Check for multiple high-value transactions
        $multipleHighValueFlag = $this->checkMultipleHighValue($user);
        if ($multipleHighValueFlag) {
            $flags[] = $multipleHighValueFlag;
        }

        return $flags;
    }

    /**
     * Check for high volume transactions in 24 hours
     */
    protected function checkHighVolume(User $user): ?AmlFlag
    {
        $totalAmount = Transaction::where('buyer_id', $user->id)
            ->where('created_at', '>=', now()->subDay())
            ->where('status', 'completed')
            ->sum('amount');

        if ($totalAmount >= $this->highVolumeThreshold) {
            return $this->createAmlFlag($user, 'high_volume', [
                'total_amount' => $totalAmount,
                'threshold' => $this->highVolumeThreshold,
                'period' => '24_hours',
            ], "High volume transactions detected: \${$totalAmount} in 24 hours");
        }

        return null;
    }

    /**
     * Check for rapid transfers (multiple transactions in short time)
     */
    protected function checkRapidTransfers(User $user): ?AmlFlag
    {
        $recentTransactions = Transaction::where('buyer_id', $user->id)
            ->where('created_at', '>=', now()->subHour())
            ->where('status', 'completed')
            ->count();

        if ($recentTransactions >= $this->rapidTransferThreshold) {
            return $this->createAmlFlag($user, 'rapid_transfers', [
                'transaction_count' => $recentTransactions,
                'threshold' => $this->rapidTransferThreshold,
                'period' => '1_hour',
            ], "Rapid transfers detected: {$recentTransactions} transactions in 1 hour");
        }

        return null;
    }

    /**
     * Check for email mismatch between user profile and payment method
     */
    protected function checkEmailMismatch(User $user): ?AmlFlag
    {
        // This would need to be implemented based on your payment provider integration
        // For now, we'll check if PayPal email is different from user email
        if ($user->paypal_email && $user->paypal_email !== $user->email) {
            return $this->createAmlFlag($user, 'email_mismatch', [
                'user_email' => $user->email,
                'paypal_email' => $user->paypal_email,
            ], "Email mismatch between user profile and PayPal account");
        }

        return null;
    }

    /**
     * Check for multiple high-value transactions in short period
     */
    protected function checkMultipleHighValue(User $user): ?AmlFlag
    {
        $highValueTransactions = Transaction::where('buyer_id', $user->id)
            ->where('created_at', '>=', now()->subWeek())
            ->where('amount', '>=', 10000) // $10,000+ transactions
            ->where('status', 'completed')
            ->count();

        if ($highValueTransactions >= $this->multipleHighValueThreshold) {
            return $this->createAmlFlag($user, 'multiple_high_value', [
                'transaction_count' => $highValueTransactions,
                'threshold' => $this->multipleHighValueThreshold,
                'period' => '7_days',
                'min_amount' => 10000,
            ], "Multiple high-value transactions detected: {$highValueTransactions} transactions over \$10,000 in 7 days");
        }

        return null;
    }

    /**
     * Create an AML flag
     */
    protected function createAmlFlag(User $user, string $flagType, array $metadata, string $description): AmlFlag
    {
        return AmlFlag::create([
            'user_id' => $user->id,
            'flag_type' => $flagType,
            'description' => $description,
            'metadata' => $metadata,
            'status' => 'active',
        ]);
    }

    /**
     * Resolve an AML flag
     */
    public function resolveAmlFlag(AmlFlag $flag, User $admin, string $resolutionNotes): bool
    {
        try {
            $flag->update([
                'status' => 'resolved',
                'reviewed_by_admin_id' => $admin->id,
                'reviewed_at' => now(),
                'resolution_notes' => $resolutionNotes,
            ]);

            Log::info('AML flag resolved', [
                'flag_id' => $flag->id,
                'admin_id' => $admin->id,
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('Failed to resolve AML flag', [
                'flag_id' => $flag->id,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Mark an AML flag as false positive
     */
    public function markAsFalsePositive(AmlFlag $flag, User $admin, string $notes): bool
    {
        try {
            $flag->update([
                'status' => 'false_positive',
                'reviewed_by_admin_id' => $admin->id,
                'reviewed_at' => now(),
                'resolution_notes' => $notes,
            ]);

            Log::info('AML flag marked as false positive', [
                'flag_id' => $flag->id,
                'admin_id' => $admin->id,
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('Failed to mark AML flag as false positive', [
                'flag_id' => $flag->id,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Get active AML flags for a user
     */
    public function getActiveFlags(User $user): \Illuminate\Database\Eloquent\Collection
    {
        return AmlFlag::where('user_id', $user->id)
            ->where('status', 'active')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Check if user has any active AML flags
     */
    public function hasActiveFlags(User $user): bool
    {
        return AmlFlag::where('user_id', $user->id)
            ->where('status', 'active')
            ->exists();
    }
}
