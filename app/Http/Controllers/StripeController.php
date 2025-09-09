<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\PaymentIntent;

class StripeController extends Controller
{
    public function showPaymentPage($orderId)
    {
        $order = \App\Models\Order::findOrFail($orderId);
        return view('stripe.pay', [
            'order' => $order,
            'amount' => $order->total,
        ]);
    }
    public function showForm()
    {
        return view('stripe.form');
    }

    public function processPayment(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1',
        ]);

        Stripe::setApiKey(config('services.stripe.secret'));

        $paymentIntent = PaymentIntent::create([
            'amount' => $request->amount * 100, // amount in cents
            'currency' => 'usd',
            'payment_method_types' => ['card'],
        ]);

        // If AJAX, return JSON
        if ($request->wantsJson() || $request->isJson() || $request->ajax()) {
            return response()->json([
                'clientSecret' => $paymentIntent->client_secret,
            ]);
        }
        // Otherwise, fallback to view (legacy)
        return view('stripe.confirm', [
            'clientSecret' => $paymentIntent->client_secret,
            'amount' => $request->amount,
        ]);
    }
}
