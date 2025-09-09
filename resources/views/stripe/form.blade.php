@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Stripe Payment</h2>
    <form action="{{ route('stripe.pay') }}" method="POST">
        @csrf
        <div class="form-group">
            <label for="amount">Amount (USD):</label>
            <input type="number" name="amount" id="amount" class="form-control" min="1" required>
        </div>
        <button type="submit" class="btn btn-primary mt-2">Pay with Stripe</button>
    </form>
</div>
@endsection
