<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use Illuminate\Support\Facades\Session;
use Stripe\Stripe;
use Stripe\Checkout\Session as StripeSession;

class StripeController extends Controller
{
    public function success(Request $request)
    {
        $sessionId = $request->get('session_id');

        if (!$sessionId) {
            return redirect()->route('cart');
        }

        Stripe::setApiKey(config('services.stripe.secret'));

        try {
            $session = StripeSession::retrieve($sessionId);

            if (!$session) {
                throw new \Exception('Session not found.');
            }

            $orderId = $session->metadata->order_id ?? null;
            $order = Order::find($orderId);

            if (!$order) {
                throw new \Exception('Order not found.');
            }

            if ($order->status === 'unpaid' || $order->status === 'pending') {
                $order->status = 'paid';
                $order->save();
            }

            // Clear cart
            Session::forget('cart');

            // Redirect to a success page or dashboard with success message
            // Ideally we should have a specific order confirmation page.
            // For now, redirect to dashboard as per original flow or orders show
            return redirect()->route('dashboard')->with('success', 'Payment successful! Your order has been placed.');

        } catch (\Exception $e) {
            return redirect()->route('checkout')->with('error', 'Payment verification failed. Please try again.');
        }
    }

    public function cancel()
    {
        return redirect()->route('checkout')->with('error', 'Payment was cancelled.');
    }
}
