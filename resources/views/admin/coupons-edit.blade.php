@extends('layouts.admin')

@section('content')
<div class="main-content-inner">
    <div class="main-content-wrap">
        <h3>Edit Coupon</h3>
        @if(session('status'))
            <div class="alert alert-success">{{ session('status') }}</div>
        @endif
        <form action="{{ route('admin.coupons.update', $coupon->id) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="form-group">
                <label for="code">Code</label>
                <input type="text" name="code" id="code" class="form-control" value="{{ old('code', $coupon->code) }}" required>
            </div>
            <div class="form-group">
                <label for="type">Type</label>
                <input type="text" name="type" id="type" class="form-control" value="{{ old('type', $coupon->type) }}" required>
            </div>
            <div class="form-group">
                <label for="value">Value</label>
                <input type="number" name="value" id="value" class="form-control" value="{{ old('value', $coupon->value) }}" required>
            </div>
            <div class="form-group">
                <label for="cart_value">Cart Value</label>
                <input type="number" name="cart_value" id="cart_value" class="form-control" value="{{ old('cart_value', $coupon->cart_value) }}" required>
            </div>
            <div class="form-group">
                <label for="expiry_date">Expiry Date</label>
                <input type="date" name="expiry_date" id="expiry_date" class="form-control" value="{{ old('expiry_date', $coupon->expiry_date) }}" required>
            </div>
            <button type="submit" class="btn btn-primary">Update Coupon</button>
            <a href="{{ route('admin.coupons') }}" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</div>
@endsection
