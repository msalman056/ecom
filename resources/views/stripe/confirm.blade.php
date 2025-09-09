@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Confirm Payment</h2>
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
        const {paymentIntent, error} = await stripe.confirmCardPayment("{{ $clientSecret }}onclick, {
            payment_method: {
                card: card,
            }
        });
        const msg = document.getElementById('payment-message');
        if (error) {
            msg.textContent = error.message;
            msg.classList.add('text-danger');
        } else if (paymentIntent && paymentIntent.status === 'succeeded') {
            msg.textContent = 'Payment successful!';
            msg.classList.add('text-success');
        }
    };
</script>
@endsection
