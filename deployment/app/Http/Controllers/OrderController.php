<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Domain;
use App\Models\PaymentTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::user();
        
        // Get orders where user is buyer or seller
        $orders = Order::forUser($user->id)
            ->with(['domain', 'buyer', 'seller'])
            ->latest()
            ->paginate(15);

        return view('orders.index', compact('orders'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // This method is not used for orders
        // Orders are created through the buy process
        return redirect()->back();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'domain_id' => 'required|exists:domains,id',
            'payment_method' => 'required|in:stripe,paypal,razorpay',
        ]);

        $domain = Domain::findOrFail($request->domain_id);
        
        // Check if domain is available for purchase
        if ($domain->status !== 'active') {
            return back()->with('error', 'This domain is not available for purchase.');
        }

        // Check if user is not buying their own domain
        if ($domain->user_id === Auth::id()) {
            return back()->with('error', 'You cannot buy your own domain.');
        }

        // Calculate commission and amounts
        $commissionRate = $domain->commission_rate ?? 5.00;
        $commissionAmount = round(($domain->asking_price * $commissionRate) / 100, 2);
        $totalAmount = $domain->asking_price + $commissionAmount;
        $sellerAmount = $domain->asking_price - $commissionAmount;

        try {
            DB::beginTransaction();

            // Create the order
            $order = Order::create([
                'domain_id' => $domain->id,
                'buyer_id' => Auth::id(),
                'seller_id' => $domain->user_id,
                'domain_price' => $domain->asking_price,
                'commission_amount' => $commissionAmount,
                'total_amount' => $totalAmount,
                'seller_amount' => $sellerAmount,
                'status' => 'pending',
                'payment_method' => $request->payment_method,
            ]);

            // Update domain status
            $domain->update(['status' => 'pending_sale']);

            DB::commit();

            // Redirect to payment processing
            return redirect()->route('orders.payment', $order)
                ->with('success', 'Order created successfully. Please complete payment.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to create order. Please try again.');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Order $order)
    {
        $user = Auth::user();
        
        // Check if user has access to this order
        if ($order->buyer_id !== $user->id && $order->seller_id !== $user->id) {
            abort(403, 'Unauthorized access to this order.');
        }

        $order->load(['domain', 'buyer', 'seller', 'paymentTransactions', 'messages']);

        return view('orders.show', compact('order'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Order $order)
    {
        // Orders cannot be edited after creation
        return redirect()->route('orders.show', $order)
            ->with('error', 'Orders cannot be edited after creation.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Order $order)
    {
        $user = Auth::user();
        
        // Only seller can update order notes
        if ($order->seller_id !== $user->id) {
            abort(403, 'Only the seller can update this order.');
        }

        $request->validate([
            'notes' => 'nullable|string|max:1000',
        ]);

        $order->update([
            'notes' => $request->notes
        ]);

        return back()->with('success', 'Order updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Order $order)
    {
        $user = Auth::user();
        
        // Only buyer can cancel pending orders
        if ($order->buyer_id !== $user->id) {
            abort(403, 'Only the buyer can cancel this order.');
        }

        if (!$order->canBeCancelled()) {
            return back()->with('error', 'This order cannot be cancelled.');
        }

        try {
            DB::beginTransaction();

            // Cancel the order
            $order->update(['status' => 'cancelled']);

            // Restore domain status
            $order->domain->update(['status' => 'active']);

            DB::commit();

            return redirect()->route('orders.index')
                ->with('success', 'Order cancelled successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to cancel order. Please try again.');
        }
    }

    /**
     * Show payment page for the order.
     */
    public function payment(Order $order)
    {
        $user = Auth::user();
        
        if ($order->buyer_id !== $user->id) {
            abort(403, 'Unauthorized access to this order.');
        }

        if ($order->status !== 'pending') {
            return redirect()->route('orders.show', $order)
                ->with('error', 'This order is not pending payment.');
        }

        return view('orders.payment', compact('order'));
    }

    /**
     * Process payment for the order.
     */
    public function processPayment(Request $request, Order $order)
    {
        $user = Auth::user();
        
        if ($order->buyer_id !== $user->id) {
            abort(403, 'Unauthorized access to this order.');
        }

        $request->validate([
            'payment_token' => 'required|string',
        ]);

        try {
            DB::beginTransaction();

            // Create payment transaction record
            PaymentTransaction::create([
                'transaction_id' => 'TXN-' . strtoupper(uniqid()),
                'order_id' => $order->id,
                'user_id' => $user->id,
                'type' => 'payment',
                'status' => 'processing',
                'amount' => $order->total_amount,
                'currency' => 'USD',
                'payment_method' => $order->payment_method,
                'description' => 'Payment for order #' . $order->order_number,
                'metadata' => [
                    'payment_token' => $request->payment_token,
                    'order_number' => $order->order_number
                ]
            ]);

            // Mark order as paid and move to escrow
            $order->markAsPaid($order->payment_method, $request->payment_token);
            $order->moveToEscrow();

            DB::commit();

            return redirect()->route('orders.show', $order)
                ->with('success', 'Payment processed successfully. Order is now in escrow.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Payment processing failed. Please try again.');
        }
    }

    /**
     * Complete the order (release escrow).
     */
    public function complete(Order $order)
    {
        $user = Auth::user();
        
        // Only buyer can complete the order
        if ($order->buyer_id !== $user->id) {
            abort(403, 'Only the buyer can complete this order.');
        }

        if (!$order->canBeCompleted()) {
            return back()->with('error', 'This order cannot be completed yet.');
        }

        try {
            DB::beginTransaction();

            // Complete the order
            $order->complete();

            // Create escrow release transaction
            PaymentTransaction::create([
                'transaction_id' => 'ESC-' . strtoupper(uniqid()),
                'order_id' => $order->id,
                'user_id' => $order->seller_id,
                'type' => 'release',
                'status' => 'completed',
                'amount' => $order->seller_amount,
                'currency' => 'USD',
                'payment_method' => 'escrow',
                'description' => 'Escrow release for order #' . $order->order_number,
                'processed_at' => now()
            ]);

            DB::commit();

            return redirect()->route('orders.show', $order)
                ->with('success', 'Order completed successfully. Funds have been released to seller.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to complete order. Please try again.');
        }
    }

    /**
     * Mark order as disputed.
     */
    public function dispute(Order $order)
    {
        $user = Auth::user();
        
        if ($order->buyer_id !== $user->id && $order->seller_id !== $user->id) {
            abort(403, 'Unauthorized access to this order.');
        }

        if (!in_array($order->status, ['paid', 'in_escrow'])) {
            return back()->with('error', 'This order cannot be disputed.');
        }

        $order->update(['status' => 'disputed']);

        return back()->with('success', 'Dispute raised successfully. Our team will review this case.');
    }
}
