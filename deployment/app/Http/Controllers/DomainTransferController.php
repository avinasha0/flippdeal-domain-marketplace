<?php

namespace App\Http\Controllers;

use App\Models\Domain;
use App\Models\Transaction;
use App\Models\DomainTransfer;
use App\Services\EscrowService;
use App\Jobs\VerifyTransferJob;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class DomainTransferController extends Controller
{
    protected EscrowService $escrowService;

    public function __construct(EscrowService $escrowService)
    {
        $this->middleware(['auth', 'verified']);
        $this->escrowService = $escrowService;
    }

    /**
     * Show transfer form for a domain.
     */
    public function showTransferForm(Transaction $transaction)
    {
        if ($transaction->seller_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        if ($transaction->escrow_state !== Transaction::STATE_IN_ESCROW) {
            return redirect()->back()->with('error', 'Transaction is not in escrow');
        }

        $domain = $transaction->domain;
        $buyer = $transaction->buyer;

        return view('domains.transfer', compact('transaction', 'domain', 'buyer'));
    }

    /**
     * Submit transfer evidence.
     */
    public function submitTransfer(Request $request, Transaction $transaction)
    {
        if ($transaction->seller_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        if ($transaction->escrow_state !== Transaction::STATE_IN_ESCROW) {
            return redirect()->back()->with('error', 'Transaction is not in escrow');
        }

        $request->validate([
            'transfer_method' => 'required|in:registrar,dns,manual,auth_code',
            'transfer_notes' => 'nullable|string|max:1000',
            'evidence_file' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:10240', // 10MB max
            'evidence_data' => 'nullable|array',
        ]);

        try {
            $evidenceUrl = null;
            $evidenceData = $request->evidence_data ?? [];

            // Handle file upload
            if ($request->hasFile('evidence_file')) {
                $file = $request->file('evidence_file');
                $filename = 'transfer_evidence_' . $transaction->id . '_' . time() . '.' . $file->getClientOriginalExtension();
                $path = $file->storeAs('transfer_evidence', $filename, 'public');
                $evidenceUrl = Storage::url($path);
            }

            // Create domain transfer record
            $transfer = $this->escrowService->createDomainTransfer(
                $transaction,
                $request->transfer_method,
                $evidenceData,
                $evidenceUrl,
                $request->transfer_notes
            );

            // Queue verification job for automated verification
            if (in_array($request->transfer_method, [DomainTransfer::METHOD_REGISTRAR, DomainTransfer::METHOD_DNS, DomainTransfer::METHOD_AUTH_CODE])) {
                VerifyTransferJob::dispatch($transfer);
            }

            Log::info('Domain transfer evidence submitted', [
                'transaction_id' => $transaction->id,
                'transfer_id' => $transfer->id,
                'method' => $request->transfer_method,
            ]);

            return redirect()->route('purchase.my-sales')
                ->with('success', 'Transfer evidence submitted successfully! It will be reviewed by our team.');

        } catch (\Exception $e) {
            Log::error('Transfer evidence submission failed', [
                'transaction_id' => $transaction->id,
                'error' => $e->getMessage(),
            ]);

            return redirect()->back()->with('error', 'Failed to submit transfer evidence: ' . $e->getMessage());
        }
    }

    /**
     * Show transfer instructions.
     */
    public function showInstructions(Transaction $transaction)
    {
        if ($transaction->seller_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        $domain = $transaction->domain;
        $buyer = $transaction->buyer;

        return view('domains.transfer-instructions', compact('transaction', 'domain', 'buyer'));
    }

    /**
     * Download verification file for file-based verification.
     */
    public function downloadVerificationFile(Domain $domain)
    {
        if ($domain->user_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        $verificationService = app(\App\Services\DomainVerificationService::class);
        $fileContent = $verificationService->generateVerificationFileContent($domain);

        $filename = 'flippdeal-verification-' . $domain->slug . '.html';

        return response($fileContent)
            ->header('Content-Type', 'text/html')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }

    /**
     * Get transfer status for a transaction.
     */
    public function getTransferStatus(Transaction $transaction)
    {
        if ($transaction->seller_id !== Auth::id() && $transaction->buyer_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        $transfer = $transaction->domainTransfer;

        if (!$transfer) {
            return response()->json([
                'status' => 'not_submitted',
                'message' => 'No transfer evidence submitted yet'
            ]);
        }

        return response()->json([
            'status' => $transfer->verified ? 'verified' : 'pending',
            'method' => $transfer->transfer_method_display,
            'submitted_at' => $transfer->created_at->format('M j, Y g:i A'),
            'verified_at' => $transfer->verified_at?->format('M j, Y g:i A'),
            'notes' => $transfer->transfer_notes,
            'verification_notes' => $transfer->verification_notes,
        ]);
    }

    /**
     * Show buyer's transfer status.
     */
    public function showBuyerStatus(Transaction $transaction)
    {
        if ($transaction->buyer_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        $domain = $transaction->domain;
        $seller = $transaction->seller;
        $transfer = $transaction->domainTransfer;

        return view('domains.buyer-transfer-status', compact('transaction', 'domain', 'seller', 'transfer'));
    }
}
