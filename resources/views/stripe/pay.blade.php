@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Pay for Order #{{ $order->id }}</h2>
    <p>Amount: ${{ $amount }}</p>
    <div id="card-element"></div>
    <button id="pay-btn" class="btn btn-success mt-2">Pay</button>
    <div id="payment-message" class="mt-3"></div>
</div>
<script src="https://js.stripe.com/v3/"></script>
<script>
    const stripe = Stripe("{{ config('services.stripe.key') }}");
    const elements = stripe.elements();
    const card = elements.create('card');
    card.mount('#card-element');

    document.getElementById('pay-btn').onclick = async function() {
        // Create PaymentIntent via AJAX
        let response = await fetch("{{ route('stripe.pay') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ amount: {{ $amount }} })
        });
        let data = await response.json();
        const msg = document.getElementById('payment-message');
        if (!data.clientSecret) {
            msg.textContent = 'Error creating payment intent.';
            msg.classList.add('text-danger');
            return;
        }
        const {paymentIntent, error} = await stripe.confirmCardPayment(data.clientSecret, {
            payment_method: {
                card: card,
            }
        });
        if (error) {
            msg.textContent = error.message;
            msg.classList.add('text-danger');
        } else if (paymentIntent && paymentIntent.status === 'succeeded') {
            msg.textContent = 'Payment successful!';
            msg.classList.add('text-success');
            // Optionally redirect to order confirmation
            setTimeout(() => { window.location.href = "{{ route('cart.order_confirmation') }}"; }, 1500);
        }
    };
</script>
@endsection
