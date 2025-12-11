<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Services\Payment\RazorpayService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class PaymentController extends Controller
{
    protected $razorpayService;

    public function __construct(RazorpayService $razorpayService)
    {
        $this->razorpayService = $razorpayService;
    }

    public function initiate(Product $product)
    {
        Log::info('Payment Initiation Started for Product: ' . $product->id);

        if (!auth()->check()) {
            Log::info('User not authenticated, redirecting to login');
            return redirect()->route('login');
        }

        Log::info('User authenticated: ' . auth()->id());

        // Create a pending order
        $order = Order::create([
            'order_number' => (string) Str::uuid(),
            'user_id' => auth()->id(),
            'product_id' => $product->id,
            'amount' => $product->price,
            'platform_fee' => 0, // Calculated later or now
            'vendor_amount' => 0, // Calculated later or now
            'status' => 'pending',
            'payment_method' => 'razorpay',
        ]);

        Log::info('Order Created: ' . $order->id);

        // Calculate split (logic from Order model or simple logic here for now)
        // Assuming Order model has observer or we set it manually. 
        // For now, let's rely on Order model logic if it exists, or update it here.
        // Let's rely on the Order's boot method if we added one, otherwise explicit set:

        // Re-saving to trigger any model events if needed, but let's just use the values.
        // The migration has them.

        try {
            // Create Razorpay Order
            Log::info('Creating Razorpay Order with keys: ' . config('services.razorpay.key'));
            $razorpayOrder = $this->razorpayService->createOrder(
                $order->amount,
                $order->order_number,
                'USD' // Or INR based on generic config
            );

            Log::info('Razorpay Order Created: ' . $razorpayOrder['id']);

            $order->update(['payment_intent_id' => $razorpayOrder['id']]);

            return view('payment.checkout', [
                'order' => $order,
                'razorpayOrderId' => $razorpayOrder['id'],
                'razorpayKey' => config('services.razorpay.key'),
                'product' => $product
            ]);

        } catch (\Exception $e) {
            Log::error('Payment Initiation Failed: ' . $e->getMessage());
            Log::error($e->getTraceAsString());

            // For local testing/demo purposes without valid keys, allow rendering the page with mock data
            if (app()->environment('local')) {
                Log::warning('Using mock checkout due to Razorpay failure in local env');
                return view('payment.checkout', [
                    'order' => $order,
                    'razorpayOrderId' => 'order_mock_' . Str::random(10),
                    'razorpayKey' => 'rzp_test_mock',
                    'product' => $product
                ]);
            }

            return back()->with('error', 'Unable to initiate payment. Please try again.');
        }
    }

    public function callback(Request $request)
    {
        $signatureStatus = $this->razorpayService->verifyPaymentSignature([
            'razorpay_order_id' => $request->razorpay_order_id,
            'razorpay_payment_id' => $request->razorpay_payment_id,
            'razorpay_signature' => $request->razorpay_signature
        ]);

        if ($signatureStatus) {
            $order = Order::where('payment_intent_id', $request->razorpay_order_id)->firstOrFail();

            DB::transaction(function () use ($order, $request) {
                $order->update([
                    'status' => 'completed',
                    // potentially store payment_id in a meta field if needed
                ]);

                // Generate Download Token
                $order->downloadToken()->create([
                    'token' => Str::random(64),
                    'expires_at' => now()->addHours(\App\Models\Setting::get('download_expiry_hours', 72)), // Configurable
                ]);
            });

            return redirect()->route('payment.success', $order);
        } else {
            return redirect()->route('payment.failed');
        }
    }

    public function success(Order $order)
    {
        if ($order->user_id !== auth()->id()) {
            abort(403);
        }
        return view('payment.success', compact('order'));
    }

    public function failed()
    {
        return view('payment.failed');
    }
}
