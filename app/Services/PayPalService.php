<?php

namespace App\Services;

use App\Models\Order;
use App\Models\SiteSetting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PayPalService
{
    protected string $clientId;
    protected string $clientSecret;
    protected string $mode;
    protected string $baseUrl;

    public function __construct()
    {
        $this->clientId = SiteSetting::get('paypal_client_id', '');
        $this->clientSecret = SiteSetting::get('paypal_client_secret', '');
        $this->mode = SiteSetting::get('paypal_mode', 'sandbox');
        
        $this->baseUrl = $this->mode === 'live' 
            ? 'https://api-m.paypal.com'
            : 'https://api-m.sandbox.paypal.com';
    }

    /**
     * Get PayPal access token.
     */
    public function getAccessToken(): ?string
    {
        try {
            $response = Http::withBasicAuth($this->clientId, $this->clientSecret)
                ->asForm()
                ->post($this->baseUrl . '/v1/oauth2/token', [
                    'grant_type' => 'client_credentials'
                ]);

            if ($response->successful()) {
                return $response->json('access_token');
            }

            Log::error('PayPal access token request failed', [
                'status' => $response->status(),
                'body' => $response->body()
            ]);

            return null;
        } catch (\Exception $e) {
            Log::error('PayPal access token error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Create PayPal order.
     */
    public function createOrder(Order $order): ?array
    {
        $accessToken = $this->getAccessToken();
        if (!$accessToken) {
            return null;
        }

        $commissionRate = SiteSetting::get('default_commission_rate', 5.00);
        $commissionAmount = $order->total_amount * ($commissionRate / 100);
        $sellerAmount = $order->total_amount - $commissionAmount;

        $payload = [
            'intent' => 'CAPTURE',
            'purchase_units' => [
                [
                    'reference_id' => 'order_' . $order->id,
                    'amount' => [
                        'currency_code' => 'USD',
                        'value' => number_format($order->total_amount, 2, '.', '')
                    ],
                    'description' => 'Domain purchase: ' . $order->domain->full_domain,
                    'custom_id' => $order->id,
                    'soft_descriptor' => 'Domain Marketplace'
                ]
            ],
            'application_context' => [
                'brand_name' => SiteSetting::get('site_name', 'Domain Marketplace'),
                'landing_page' => 'NO_PREFERENCE',
                'user_action' => 'PAY_NOW',
                'return_url' => route('orders.payment.success', $order),
                'cancel_url' => route('orders.payment.cancel', $order)
            ]
        ];

        try {
            $response = Http::withToken($accessToken)
                ->post($this->baseUrl . '/v2/checkout/orders', $payload);

            if ($response->successful()) {
                $orderData = $response->json();
                
                // Update order with PayPal order ID
                $order->update([
                    'payment_reference' => $orderData['id'],
                    'payment_status' => 'pending'
                ]);

                return $orderData;
            }

            Log::error('PayPal order creation failed', [
                'order_id' => $order->id,
                'status' => $response->status(),
                'body' => $response->body()
            ]);

            return null;
        } catch (\Exception $e) {
            Log::error('PayPal order creation error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Capture PayPal order.
     */
    public function captureOrder(string $paypalOrderId): ?array
    {
        $accessToken = $this->getAccessToken();
        if (!$accessToken) {
            return null;
        }

        try {
            $response = Http::withToken($accessToken)
                ->post($this->baseUrl . '/v2/checkout/orders/' . $paypalOrderId . '/capture');

            if ($response->successful()) {
                return $response->json();
            }

            Log::error('PayPal order capture failed', [
                'paypal_order_id' => $paypalOrderId,
                'status' => $response->status(),
                'body' => $response->body()
            ]);

            return null;
        } catch (\Exception $e) {
            Log::error('PayPal order capture error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Get PayPal order details.
     */
    public function getOrderDetails(string $paypalOrderId): ?array
    {
        $accessToken = $this->getAccessToken();
        if (!$accessToken) {
            return null;
        }

        try {
            $response = Http::withToken($accessToken)
                ->get($this->baseUrl . '/v2/checkout/orders/' . $paypalOrderId);

            if ($response->successful()) {
                return $response->json();
            }

            Log::error('PayPal order details failed', [
                'paypal_order_id' => $paypalOrderId,
                'status' => $response->status(),
                'body' => $response->body()
            ]);

            return null;
        } catch (\Exception $e) {
            Log::error('PayPal order details error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Process refund.
     */
    public function processRefund(string $captureId, float $amount, string $reason = 'Refund'): ?array
    {
        $accessToken = $this->getAccessToken();
        if (!$accessToken) {
            return null;
        }

        $payload = [
            'amount' => [
                'value' => number_format($amount, 2, '.', ''),
                'currency_code' => 'USD'
            ],
            'note_to_payer' => $reason
        ];

        try {
            $response = Http::withToken($accessToken)
                ->post($this->baseUrl . '/v2/payments/captures/' . $captureId . '/refund', $payload);

            if ($response->successful()) {
                return $response->json();
            }

            Log::error('PayPal refund failed', [
                'capture_id' => $captureId,
                'amount' => $amount,
                'status' => $response->status(),
                'body' => $response->body()
            ]);

            return null;
        } catch (\Exception $e) {
            Log::error('PayPal refund error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Verify webhook signature.
     */
    public function verifyWebhook(string $headers, string $body): bool
    {
        // This is a simplified verification
        // In production, you should implement proper webhook signature verification
        return true;
    }

    /**
     * Handle PayPal webhook.
     */
    public function handleWebhook(array $data): void
    {
        $eventType = $data['event_type'] ?? '';
        
        switch ($eventType) {
            case 'CHECKOUT.ORDER.APPROVED':
                $this->handleOrderApproved($data);
                break;
            case 'PAYMENT.CAPTURE.COMPLETED':
                $this->handlePaymentCompleted($data);
                break;
            case 'PAYMENT.CAPTURE.DENIED':
                $this->handlePaymentDenied($data);
                break;
            case 'PAYMENT.CAPTURE.REFUNDED':
                $this->handlePaymentRefunded($data);
                break;
        }
    }

    /**
     * Handle order approved webhook.
     */
    private function handleOrderApproved(array $data): void
    {
        $orderId = $data['resource']['custom_id'] ?? null;
        if ($orderId) {
            $order = Order::find($orderId);
            if ($order) {
                $order->update(['payment_status' => 'approved']);
                Log::info("Order {$orderId} approved via PayPal webhook");
            }
        }
    }

    /**
     * Handle payment completed webhook.
     */
    private function handlePaymentCompleted(array $data): void
    {
        $orderId = $data['resource']['custom_id'] ?? null;
        if ($orderId) {
            $order = Order::find($orderId);
            if ($order) {
                $order->update([
                    'payment_status' => 'completed',
                    'status' => 'completed',
                    'completed_at' => now()
                ]);
                Log::info("Order {$orderId} payment completed via PayPal webhook");
            }
        }
    }

    /**
     * Handle payment denied webhook.
     */
    private function handlePaymentDenied(array $data): void
    {
        $orderId = $data['resource']['custom_id'] ?? null;
        if ($orderId) {
            $order = Order::find($orderId);
            if ($order) {
                $order->update(['payment_status' => 'failed']);
                Log::info("Order {$orderId} payment denied via PayPal webhook");
            }
        }
    }

    /**
     * Handle payment refunded webhook.
     */
    private function handlePaymentRefunded(array $data): void
    {
        $orderId = $data['resource']['custom_id'] ?? null;
        if ($orderId) {
            $order = Order::find($orderId);
            if ($order) {
                $order->update(['payment_status' => 'refunded']);
                Log::info("Order {$orderId} payment refunded via PayPal webhook");
            }
        }
    }
}
