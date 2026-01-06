<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\PaymentProcessRequest;
use App\Models\Order;
use App\Models\Payment;
use App\Services\PaymentService;
use Illuminate\Support\Facades\Auth;

class PaymentController extends Controller
{
    /**
     * @var PaymentService
     */
    protected $paymentService;

    /**
     * Create a new PaymentController instance.
     *
     * @param PaymentService $paymentService
     * @return void
     */
    public function __construct(PaymentService $paymentService)
    {
        $this->middleware('auth:api');
        $this->paymentService = $paymentService;
    }

    /**
     * Display a listing of the payments.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(\Illuminate\Http\Request $request)
    {
        $orderId = $request->query('order_id');
        $perPage = $request->query('per_page', 15);

        $query = Payment::with('order');

        if ($orderId) {
            $query->where('order_id', $orderId);
        } else {
            // If no order_id is specified, only show payments for the user's orders
            $query->whereHas('order', function ($query) {
                $query->where('user_id', Auth::id());
            });
        }

        $payments = $query->orderBy('created_at', 'desc')
            ->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $payments
        ]);
    }

    /**
     * Process a payment for an order.
     *
     * @param  PaymentProcessRequest  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function process(PaymentProcessRequest $request)
    {
        $order = Order::find($request->order_id);

        // Check if the order belongs to the authenticated user
        if ($order->user_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. This order does not belong to you.'
            ], 403);
        }

        try {
            // Check if the order is in a state that allows payment processing
            if (!$order->canProcessPayment()) {
                return response()->json([
                    'success' => false,
                    'message' => 'This order cannot be processed for payment. Order status must be confirmed.'
                ], 422);
            }

            // Process the payment
            $payment = $this->paymentService->processPayment($order, $request->all());

            return response()->json([
                'success' => true,
                'message' => 'Payment processed successfully',
                'data' => $payment
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Payment processing failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified payment.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $payment = Payment::with('order')
            ->whereHas('order', function ($query) {
                $query->where('user_id', Auth::id());
            })
            ->find($id);

        if (!$payment) {
            return response()->json([
                'success' => false,
                'message' => 'Payment not found'
            ], 404);
        }

        // Get live payment status from the gateway
        $status = $this->paymentService->getPaymentStatus($payment);

        return response()->json([
            'success' => true,
            'data' => $payment,
            'gateway_status' => $status
        ]);
    }
}
