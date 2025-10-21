<?php

namespace App\Http\Controllers;

use App\Models\WalletTransaction;
use App\Notifications\WalletTransactionNotification;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class WalletController extends Controller
{
    /**
     * Show wallet dashboard
     */
    public function index(): View
    {
        $user = auth()->user();
        
        $recentTransactions = $user->getRecentWalletTransactions(10);
        
        return view('wallet.index', compact('recentTransactions'));
    }

    /**
     * Get wallet balance (AJAX)
     */
    public function getBalance(): JsonResponse
    {
        $user = auth()->user();
        
        return response()->json([
            'balance' => $user->wallet_balance,
            'formatted_balance' => $user->formatted_wallet_balance,
            'total_earnings' => $user->total_earnings,
            'total_withdrawals' => $user->total_withdrawals
        ]);
    }

    /**
     * Get transaction history (AJAX)
     */
    public function getTransactions(Request $request): JsonResponse
    {
        $user = auth()->user();
        
        $perPage = $request->get('per_page', 15);
        $type = $request->get('type');
        $status = $request->get('status');
        
        $query = $user->walletTransactions()->orderBy('created_at', 'desc');
        
        if ($type) {
            $query->where('type', $type);
        }
        
        if ($status) {
            $query->where('status', $status);
        }
        
        $transactions = $query->paginate($perPage);
        
        return response()->json([
            'transactions' => $transactions->items(),
            'pagination' => [
                'current_page' => $transactions->currentPage(),
                'last_page' => $transactions->lastPage(),
                'per_page' => $transactions->perPage(),
                'total' => $transactions->total()
            ]
        ]);
    }

    /**
     * Process withdrawal request
     */
    public function withdraw(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'amount' => 'required|numeric|min:10|max:10000',
            'paypal_email' => 'required|email',
            'description' => 'nullable|string|max:255'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = auth()->user();
        $amount = $request->amount;
        $paypalEmail = $request->paypal_email;
        $description = $request->description;

        // Check if user can withdraw
        if (!$user->canWithdraw($amount)) {
            return response()->json([
                'success' => false,
                'message' => 'Unable to process withdrawal. Please check your account verification status and balance.'
            ], 400);
        }

        // Verify PayPal email matches user's verified email
        if ($paypalEmail !== $user->paypal_email) {
            return response()->json([
                'success' => false,
                'message' => 'PayPal email does not match your verified email address.'
            ], 400);
        }

        try {
            DB::beginTransaction();

            // Create withdrawal transaction
            $transaction = $user->walletTransactions()->create([
                'type' => WalletTransaction::TYPE_DEBIT,
                'amount' => $amount,
                'description' => $description ?? 'Withdrawal to PayPal',
                'status' => WalletTransaction::STATUS_PENDING,
                'reference_id' => 'WTH-' . time() . '-' . $user->id,
                'metadata' => [
                    'paypal_email' => $paypalEmail,
                    'withdrawal_method' => 'paypal'
                ]
            ]);

            // Process withdrawal
            $success = $user->withdrawFromWallet($amount, 'Withdrawal to PayPal: ' . $paypalEmail);
            
            if ($success) {
                $transaction->markAsCompleted();
                
                // Send notification
                $user->notify(new WalletTransactionNotification($transaction));
                
                DB::commit();
                
                return response()->json([
                    'success' => true,
                    'message' => 'Withdrawal request processed successfully. Funds will be transferred to your PayPal account within 1-2 business days.',
                    'transaction_id' => $transaction->id,
                    'new_balance' => $user->fresh()->wallet_balance
                ]);
            } else {
                $transaction->markAsFailed();
                DB::rollBack();
                
                return response()->json([
                    'success' => false,
                    'message' => 'Insufficient balance for withdrawal.'
                ], 400);
            }

        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while processing your withdrawal. Please try again later.'
            ], 500);
        }
    }

    /**
     * Add funds to wallet (for testing/admin purposes)
     */
    public function addFunds(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'amount' => 'required|numeric|min:1|max:10000',
            'description' => 'nullable|string|max:255'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = auth()->user();
        $amount = $request->amount;
        $description = $request->description ?? 'Funds added to wallet';

        try {
            $user->addToWallet($amount, $description);
            
            // Get the latest transaction and send notification
            $transaction = $user->walletTransactions()->latest()->first();
            if ($transaction) {
                $user->notify(new WalletTransactionNotification($transaction));
            }
            
            return response()->json([
                'success' => true,
                'message' => 'Funds added successfully',
                'new_balance' => $user->fresh()->wallet_balance
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while adding funds. Please try again later.'
            ], 500);
        }
    }

    /**
     * Get withdrawal eligibility
     */
    public function getWithdrawalEligibility(): JsonResponse
    {
        $user = auth()->user();
        
        $canWithdraw = $user->canWithdraw();
        $minWithdrawal = 10.00;
        $maxWithdrawal = min($user->wallet_balance, 10000.00);
        
        return response()->json([
            'can_withdraw' => $canWithdraw,
            'min_withdrawal' => $minWithdrawal,
            'max_withdrawal' => $maxWithdrawal,
            'current_balance' => $user->wallet_balance,
            'paypal_verified' => $user->paypal_verified,
            'account_verified' => $user->is_verified,
            'account_status' => $user->account_status
        ]);
    }

    /**
     * Export transaction history
     */
    public function exportTransactions(Request $request)
    {
        $user = auth()->user();
        $format = $request->get('format', 'csv');
        
        $transactions = $user->walletTransactions()
            ->orderBy('created_at', 'desc')
            ->get();
        
        if ($format === 'csv') {
            $filename = 'wallet_transactions_' . now()->format('Y-m-d') . '.csv';
            
            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            ];
            
            $callback = function() use ($transactions) {
                $file = fopen('php://output', 'w');
                
                // CSV headers
                fputcsv($file, ['Date', 'Type', 'Amount', 'Description', 'Status', 'Reference ID']);
                
                // CSV data
                foreach ($transactions as $transaction) {
                    fputcsv($file, [
                        $transaction->created_at->format('Y-m-d H:i:s'),
                        ucfirst($transaction->type),
                        '$' . number_format($transaction->amount, 2),
                        $transaction->description,
                        ucfirst($transaction->status),
                        $transaction->reference_id ?? 'N/A'
                    ]);
                }
                
                fclose($file);
            };
            
            return response()->stream($callback, 200, $headers);
        }
        
        return response()->json(['message' => 'Unsupported format'], 400);
    }
}