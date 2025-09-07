<?php

namespace App\Http\Controllers;

use App\Models\Domain;
use App\Models\Transaction;
use App\Services\EscrowService;
use App\Services\PayPalService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PurchaseController extends Controller
{
    protected EscrowService $escrowService;
    protected PayPalService $paypalService;

    public function __construct(EscrowService $escrowService, PayPalService $paypalService)
    {
        $this->middleware(['auth', 'verified']);
        $this->escrowService = $escrowService;
        $this->paypalService = $paypalService;
    }

    /**
     * Show purchase form for a domain.
     */
    public function showPurchaseForm(Domain $domain)
    {
        if ($domain->status !== 'active') {
            abort(404, 'Domain not available for purchase');
        }

        if ($domain->user_id === Auth::id()) {
            return redirect()->back()->with('error', 'You cannot purchase your own domain');
        }

        return view('domains.purchase', compact('domain'));
    }

    /**
     * Process buy-now purchase.
     */
    public function processBuyNow(Request $request, Domain $domain)
    {
        $request->validate([
            'payment_method' => 'required|in:paypal',
        ]);

        if ($domain->status !== 'active') {
            return redirect()->back()->with('error', 'Domain is no longer available for purchase');
        }

        if ($domain->user_id === Auth::id()) {
            return redirect()->back()->with('error', 'You cannot purchase your own domain');
        }

        if (!$domain->buy_now_price) {
            return redirect()->back()->with('error', 'This domain does not have a buy-now price');
        }

        try {
            DB::beginTransaction();

            // Create escrow transaction
            $transaction = $this->escrowService->createEscrowTransaction(
                Auth::user(),
                $domain->user,
                $domain,
                $domain->buy_now_price,
                'paypal'
            );

            // Create PayPal payment
            $paymentData = [
                'intent' => 'sale',
                'payer' => [
                    'payment_method' => 'paypal'
                ],
                'transactions' => [
                    [
                        'amount' => [
                            'total' => number_format($domain->buy_now_price, 2, '.', ''),
                            'currency' => 'USD'
                        ],
                        'description' => "Purchase of domain: {$domain->full_domain}",
                        'custom' => $transaction->id,
                        'item_list' => [
                            'items' => [
                                [
                                    'name' => $domain->full_domain,
                                    'description' => "Domain purchase - {$domain->description}",
                                    'quantity' => 1,
                                    'price' => number_format($domain->buy_now_price, 2, '.', ''),
                                    'currency' => 'USD'
                                ]
                            ]
                        ]
                    ]
                ],
                'redirect_urls' => [
                    'return_url' => route('purchase.success'),
                    'cancel_url' => route('purchase.cancel')
                ]
            ];

            $payment = $this->paypalService->createPayment($paymentData);

            if ($payment && isset($payment['links'])) {
                $approvalUrl = collect($payment['links'])->firstWhere('rel', 'approval_url')['href'];
                
                DB::commit();
                
                return redirect($approvalUrl);
            } else {
                throw new \Exception('Failed to create PayPal payment');
            }

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Buy now purchase failed', [
                'domain_id' => $domain->id,
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
            ]);

            return redirect()->back()->with('error', 'Purchase failed: ' . $e->getMessage());
        }
    }

    /**
     * Process auction win.
     */
    public function processAuctionWin(Domain $domain)
    {
        if ($domain->status !== 'active' || !$domain->enable_bidding) {
            abort(404, 'Domain not available for auction');
        }

        $winningBid = $domain->bids()->where('is_winning', true)->first();
        
        if (!$winningBid || $winningBid->user_id !== Auth::id()) {
            return redirect()->back()->with('error', 'You are not the winning bidder');
        }

        try {
            DB::beginTransaction();

            // Create escrow transaction
            $transaction = $this->escrowService->createEscrowTransaction(
                Auth::user(),
                $domain->user,
                $domain,
                $winningBid->amount,
                'paypal'
            );

            // Create PayPal payment
            $paymentData = [
                'intent' => 'sale',
                'payer' => [
                    'payment_method' => 'paypal'
                ],
                'transactions' => [
                    [
                        'amount' => [
                            'total' => number_format($winningBid->amount, 2, '.', ''),
                            'currency' => 'USD'
                        ],
                        'description' => "Auction win for domain: {$domain->full_domain}",
                        'custom' => $transaction->id,
                        'item_list' => [
                            'items' => [
                                [
                                    'name' => $domain->full_domain,
                                    'description' => "Auction win - {$domain->description}",
                                    'quantity' => 1,
                                    'price' => number_format($winningBid->amount, 2, '.', ''),
                                    'currency' => 'USD'
                                ]
                            ]
                        ]
                    ]
                ],
                'redirect_urls' => [
                    'return_url' => route('purchase.success'),
                    'cancel_url' => route('purchase.cancel')
                ]
            ];

            $payment = $this->paypalService->createPayment($paymentData);

            if ($payment && isset($payment['links'])) {
                $approvalUrl = collect($payment['links'])->firstWhere('rel', 'approval_url')['href'];
                
                DB::commit();
                
                return redirect($approvalUrl);
            } else {
                throw new \Exception('Failed to create PayPal payment');
            }

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Auction win processing failed', [
                'domain_id' => $domain->id,
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
            ]);

            return redirect()->back()->with('error', 'Payment processing failed: ' . $e->getMessage());
        }
    }

    /**
     * Handle successful payment.
     */
    public function handleSuccess(Request $request)
    {
        $paymentId = $request->get('paymentId');
        $payerId = $request->get('PayerID');

        if (!$paymentId || !$payerId) {
            return redirect()->route('dashboard')->with('error', 'Invalid payment parameters');
        }

        try {
            // Execute PayPal payment
            $execution = $this->paypalService->executePayment($paymentId, $payerId);

            if ($execution && $execution['state'] === 'approved') {
                // Find transaction by custom field
                $transactionId = $execution['transactions'][0]['custom'] ?? null;
                
                if ($transactionId) {
                    $transaction = Transaction::find($transactionId);
                    
                    if ($transaction) {
                        // Mark as in escrow
                        $this->escrowService->markInEscrow(
                            $transaction,
                            $execution['id'],
                            ['paypal_execution' => $execution]
                        );

                        return redirect()->route('purchase.complete', $transaction)
                            ->with('success', 'Payment successful! Your domain purchase is now in escrow.');
                    }
                }
            }

            return redirect()->route('dashboard')->with('error', 'Payment verification failed');

        } catch (\Exception $e) {
            Log::error('Payment success handling failed', [
                'payment_id' => $paymentId,
                'payer_id' => $payerId,
                'error' => $e->getMessage(),
            ]);

            return redirect()->route('dashboard')->with('error', 'Payment processing failed');
        }
    }

    /**
     * Handle cancelled payment.
     */
    public function handleCancel()
    {
        return redirect()->route('dashboard')->with('error', 'Payment was cancelled');
    }

    /**
     * Show purchase completion page.
     */
    public function showComplete(Transaction $transaction)
    {
        if ($transaction->buyer_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        return view('purchase.complete', compact('transaction'));
    }

    /**
     * Show buyer's transactions.
     */
    public function myPurchases()
    {
        $transactions = Auth::user()->buyerTransactions()
            ->with(['seller', 'domain', 'domainTransfer'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('purchase.my-purchases', compact('transactions'));
    }

    /**
     * Show seller's transactions.
     */
    public function mySales()
    {
        $transactions = Auth::user()->sellerTransactions()
            ->with(['buyer', 'domain', 'domainTransfer'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('purchase.my-sales', compact('transactions'));
    }
}