<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\DomainTransfer;
use App\Services\EscrowService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class EscrowController extends Controller
{
    protected EscrowService $escrowService;

    public function __construct(EscrowService $escrowService)
    {
        $this->middleware(['auth', 'admin']);
        $this->escrowService = $escrowService;
    }

    /**
     * Display escrow dashboard.
     */
    public function index(Request $request)
    {
        $query = Transaction::with(['buyer', 'seller', 'domain', 'domainTransfer']);

        // Apply filters
        if ($request->filled('state')) {
            $query->where('escrow_state', $request->state);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        if ($request->filled('amount_min')) {
            $query->where('amount', '>=', $request->amount_min);
        }

        if ($request->filled('amount_max')) {
            $query->where('amount', '<=', $request->amount_max);
        }

        $transactions = $query->orderBy('created_at', 'desc')->paginate(20);
        $statistics = $this->escrowService->getEscrowStatistics();

        return view('admin.escrow.index', compact('transactions', 'statistics'));
    }

    /**
     * Show transaction details.
     */
    public function show(Transaction $transaction)
    {
        $transaction->load(['buyer', 'seller', 'domain', 'domainTransfer', 'audits']);
        
        return view('admin.escrow.show', compact('transaction'));
    }

    /**
     * Release escrow funds.
     */
    public function releaseEscrow(Request $request, Transaction $transaction)
    {
        $request->validate([
            'verification_notes' => 'nullable|string|max:1000',
        ]);

        try {
            DB::beginTransaction();

            $this->escrowService->releaseEscrow(
                $transaction,
                auth()->id(),
                [
                    'admin_notes' => $request->verification_notes,
                    'released_at' => now()->toISOString(),
                ]
            );

            DB::commit();

            return redirect()->back()->with('success', 'Escrow funds released successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to release escrow', [
                'transaction_id' => $transaction->id,
                'error' => $e->getMessage(),
            ]);

            return redirect()->back()->with('error', 'Failed to release escrow: ' . $e->getMessage());
        }
    }

    /**
     * Refund escrow funds.
     */
    public function refundEscrow(Request $request, Transaction $transaction)
    {
        $request->validate([
            'refund_reason' => 'required|string|max:1000',
        ]);

        try {
            DB::beginTransaction();

            $this->escrowService->refundEscrow(
                $transaction,
                $request->refund_reason,
                [
                    'admin_notes' => $request->refund_reason,
                    'refunded_at' => now()->toISOString(),
                ]
            );

            DB::commit();

            return redirect()->back()->with('success', 'Escrow refund initiated successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to refund escrow', [
                'transaction_id' => $transaction->id,
                'error' => $e->getMessage(),
            ]);

            return redirect()->back()->with('error', 'Failed to refund escrow: ' . $e->getMessage());
        }
    }

    /**
     * Verify domain transfer.
     */
    public function verifyTransfer(Request $request, DomainTransfer $transfer)
    {
        $request->validate([
            'verification_notes' => 'nullable|string|max:1000',
        ]);

        try {
            $this->escrowService->verifyDomainTransfer(
                $transfer,
                auth()->id(),
                $request->verification_notes
            );

            return redirect()->back()->with('success', 'Domain transfer verified successfully!');

        } catch (\Exception $e) {
            Log::error('Failed to verify transfer', [
                'transfer_id' => $transfer->id,
                'error' => $e->getMessage(),
            ]);

            return redirect()->back()->with('error', 'Failed to verify transfer: ' . $e->getMessage());
        }
    }

    /**
     * Show pending transfers.
     */
    public function pendingTransfers()
    {
        $transfers = DomainTransfer::with(['domain', 'transaction', 'fromUser', 'toUser'])
            ->where('verified', false)
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.escrow.pending-transfers', compact('transfers'));
    }

    /**
     * Get escrow statistics for API.
     */
    public function statistics()
    {
        $statistics = $this->escrowService->getEscrowStatistics();
        
        return response()->json($statistics);
    }

    /**
     * Export transactions to CSV.
     */
    public function export(Request $request)
    {
        $query = Transaction::with(['buyer', 'seller', 'domain']);

        // Apply same filters as index
        if ($request->filled('state')) {
            $query->where('escrow_state', $request->state);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $transactions = $query->orderBy('created_at', 'desc')->get();

        $filename = 'escrow_transactions_' . now()->format('Y-m-d_H-i-s') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($transactions) {
            $file = fopen('php://output', 'w');
            
            // CSV headers
            fputcsv($file, [
                'ID',
                'Buyer',
                'Seller',
                'Domain',
                'Amount',
                'Fee',
                'Net Amount',
                'Currency',
                'State',
                'Provider',
                'Created At',
                'Released At',
            ]);

            // CSV data
            foreach ($transactions as $transaction) {
                fputcsv($file, [
                    $transaction->id,
                    $transaction->buyer->name,
                    $transaction->seller->name,
                    $transaction->domain->full_domain,
                    $transaction->amount,
                    $transaction->fee_amount,
                    $transaction->net_amount,
                    $transaction->currency,
                    $transaction->escrow_state,
                    $transaction->provider,
                    $transaction->created_at->format('Y-m-d H:i:s'),
                    $transaction->escrow_released_at?->format('Y-m-d H:i:s'),
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}