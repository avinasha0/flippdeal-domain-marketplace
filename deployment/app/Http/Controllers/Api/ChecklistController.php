<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Services\AuditService;
use App\Services\UploadService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ChecklistController extends Controller
{
    protected $auditService;
    protected $uploadService;

    public function __construct(AuditService $auditService, UploadService $uploadService)
    {
        $this->auditService = $auditService;
        $this->uploadService = $uploadService;
    }

    /**
     * Get checklist for a transaction
     */
    public function getChecklist(Transaction $transaction): JsonResponse
    {
        $user = Auth::user();
        
        // Check if user can view this transaction
        if (!$this->canViewTransaction($transaction, $user)) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $userRole = $this->getUserRole($transaction, $user);
        $checklist = $userRole === 'seller' ? $transaction->seller_checklist : $transaction->buyer_checklist;

        return response()->json([
            'user_role' => $userRole,
            'checklist' => $checklist ?? [],
            'transaction_status' => $transaction->status,
            'can_modify' => $this->canModifyChecklist($transaction, $user),
        ]);
    }

    /**
     * Mark checklist item as completed
     */
    public function markItem(Request $request, Transaction $transaction): JsonResponse
    {
        $user = Auth::user();
        
        // Check if user can modify this transaction
        if (!$this->canModifyChecklist($transaction, $user)) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $request->validate([
            'item' => 'required|string',
            'status' => 'required|in:completed,pending,blocked',
            'user_role' => 'required|in:seller,buyer',
            'data' => 'nullable|array',
        ]);

        $item = $request->input('item');
        $status = $request->input('status');
        $userRole = $request->input('user_role');
        $data = $request->input('data', []);

        // Verify user role matches
        if ($userRole !== $this->getUserRole($transaction, $user)) {
            return response()->json(['error' => 'Invalid user role'], 400);
        }

        $checklist = $userRole === 'seller' ? $transaction->seller_checklist : $transaction->buyer_checklist;
        $checklist = $checklist ?? [];

        $checklist[$item] = [
            'status' => $status,
            'timestamp' => now()->toISOString(),
            'user_id' => $user->id,
            'data' => $data,
        ];

        if ($userRole === 'seller') {
            $transaction->update(['seller_checklist' => $checklist]);
        } else {
            $transaction->update(['buyer_checklist' => $checklist]);
        }

        // Log the checklist update
        $this->auditService->log($user, 'transaction.checklist.updated', $transaction, [
            'item' => $item,
            'status' => $status,
            'user_role' => $userRole,
        ]);

        return response()->json([
            'success' => true,
            'checklist' => $checklist,
        ]);
    }

    /**
     * Upload evidence for checklist item
     */
    public function uploadEvidence(Request $request, Transaction $transaction): JsonResponse
    {
        $user = Auth::user();
        
        // Check if user can modify this transaction
        if (!$this->canModifyChecklist($transaction, $user)) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $request->validate([
            'file' => 'required|file|max:10240|mimes:jpeg,png,gif,pdf',
            'item' => 'required|string',
            'user_role' => 'required|in:seller,buyer',
        ]);

        $file = $request->file('file');
        $item = $request->input('item');
        $userRole = $request->input('user_role');

        // Verify user role matches
        if ($userRole !== $this->getUserRole($transaction, $user)) {
            return response()->json(['error' => 'Invalid user role'], 400);
        }

        try {
            // Upload file
            $uploadResult = $this->uploadService->uploadFile(
                $file,
                $user,
                $transaction->domain,
                'transfer_evidence'
            );

            if (!$uploadResult['success']) {
                return response()->json(['error' => $uploadResult['message']], 400);
            }

            $fileUpload = $uploadResult['file_upload'];

            // Update checklist with evidence
            $checklist = $userRole === 'seller' ? $transaction->seller_checklist : $transaction->buyer_checklist;
            $checklist = $checklist ?? [];

            $checklist[$item] = [
                'status' => 'completed',
                'timestamp' => now()->toISOString(),
                'user_id' => $user->id,
                'evidence_url' => $uploadResult['signed_url'],
                'evidence_id' => $fileUpload->id,
            ];

            if ($userRole === 'seller') {
                $transaction->update(['seller_checklist' => $checklist]);
            } else {
                $transaction->update(['buyer_checklist' => $checklist]);
            }

            // Log the evidence upload
            $this->auditService->log($user, 'transaction.evidence.uploaded', $transaction, [
                'item' => $item,
                'user_role' => $userRole,
                'file_upload_id' => $fileUpload->id,
            ]);

            return response()->json([
                'success' => true,
                'evidence_url' => $uploadResult['signed_url'],
                'checklist' => $checklist,
            ]);

        } catch (\Exception $e) {
            return response()->json(['error' => 'Upload failed'], 500);
        }
    }

    /**
     * Get evidence URL for checklist item
     */
    public function getEvidence(Transaction $transaction, string $item): JsonResponse
    {
        $user = Auth::user();
        
        // Check if user can view this transaction
        if (!$this->canViewTransaction($transaction, $user)) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $userRole = $this->getUserRole($transaction, $user);
        $checklist = $userRole === 'seller' ? $transaction->seller_checklist : $transaction->buyer_checklist;

        if (!isset($checklist[$item]['evidence_id'])) {
            return response()->json(['error' => 'Evidence not found'], 404);
        }

        $fileUpload = \App\Models\FileUpload::find($checklist[$item]['evidence_id']);
        
        if (!$fileUpload) {
            return response()->json(['error' => 'Evidence file not found'], 404);
        }

        $signedUrl = $this->uploadService->generateSignedUrl($fileUpload, 60);

        return response()->json([
            'evidence_url' => $signedUrl,
            'file_name' => $fileUpload->original_name,
            'uploaded_at' => $fileUpload->created_at->toISOString(),
        ]);
    }

    /**
     * Check if user can view transaction
     */
    protected function canViewTransaction(Transaction $transaction, $user): bool
    {
        return $user->id === $transaction->buyer_id || 
               $user->id === $transaction->seller_id || 
               $user->hasRole('admin');
    }

    /**
     * Check if user can modify checklist
     */
    protected function canModifyChecklist(Transaction $transaction, $user): bool
    {
        return $user->id === $transaction->buyer_id || 
               $user->id === $transaction->seller_id || 
               $user->hasRole('admin');
    }

    /**
     * Get user role in transaction
     */
    protected function getUserRole(Transaction $transaction, $user): string
    {
        if ($user->id === $transaction->seller_id) {
            return 'seller';
        } elseif ($user->id === $transaction->buyer_id) {
            return 'buyer';
        } else {
            return 'admin';
        }
    }
}
