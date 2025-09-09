@extends('layouts.admin')

@section('content')
<style>
.table-transaction>tbody>tr:nth-of-type(odd) {
    --bs-table-accent-bg: #fff !important;
}
.order-status-form {
    display: inline-flex;
    align-items: center;
    gap: 8px;
}
.order-status-form .form-select {
    border-radius: 6px;
    border: 1px solid #d1d5db;
    padding: 4px 12px;
    font-size: 0.95em;
    background: #f9fafb;
    color: #374151;
}
.order-status-form .btn-primary {
    background: linear-gradient(90deg, #34eba1 0%, #2563eb 100%);
    color: #fff;
    border: none;
    border-radius: 6px;
    padding: 4px 14px;
    font-weight: 600;
    font-size: 0.95em;
}
</style>

<div class="main-content-inner">
    <div class="main-content-wrap">

        <div class="flex items-center flex-wrap justify-between gap20 mb-27">
            <h3>Order Details</h3>
            <ul class="breadcrumbs flex items-center flex-wrap justify-start gap10">
                <li>
                    <a href="{{ route('admin.index') }}">
                        <div class="text-tiny">Dashboard</div>
                    </a>
                </li>
                <li><i class="icon-chevron-right"></i></li>
                <li><div class="text-tiny">Order Items</div></li>
            </ul>
        </div>

        <!-- Order Info -->
        <div class="wg-box">
            <div class="flex items-center justify-between gap10 flex-wrap">
                <div class="wg-filter flex-grow">
                    <h5>Order Information</h5>
                </div>
                <a class="tf-button style-1 w208" href="{{ route('admin.orders') }}">Back</a>
            </div>
            <div class="table-responsive">
                <table class="table table-striped table-bordered">
                    <tbody>
                        <tr>
                            <th class="text-center">Name</th>
                            <td class="text-center">{{ $order->name }}</td>
                            <th class="text-center">Phone</th>
                            <td class="text-center">{{ $order->phone }}</td>
                            <th class="text-center">Zip</th>
                            <td class="text-center">{{ $order->zip }}</td>
                        </tr>
                        <tr>
                            <th class="text-center">Order Date</th>
                            <td class="text-center">{{ $order->created_at ? $order->created_at->format('d M Y, h:i A') : '-' }}</td>
                            <th class="text-center">Delivery Date</th>
                            <td class="text-center">{{ $order->delivered_at ? $order->delivered_at->format('d M Y') : '-' }}</td>
                            <th class="text-center">Cancel Date</th>
                            <td class="text-center">{{ $order->canceled_at ? $order->canceled_at->format('d M Y') : '-' }}</td>
                        </tr>
                        <tr>
                            <th class="text-center">Status</th>
                            <td colspan="5" class="text-center">
                                <form method="POST" action="{{ route('admin.orders.updateStatus', $order->id) }}" class="order-status-form">
                                    @csrf
                                    @method('PATCH')
                                    <select name="status" class="form-select form-select-sm">
                                        <option value="pending" @selected($order->status == 'pending')>Pending</option>
                                        <option value="processing" @selected($order->status == 'processing')>Processing</option>
                                        <option value="shipped" @selected($order->status == 'shipped')>Shipped</option>
                                        <option value="out_for_delivery" @selected($order->status == 'out_for_delivery')>Out for Delivery</option>
                                        <option value="delivered" @selected($order->status == 'delivered')>Delivered</option>
                                        <option value="cancelled" @selected($order->status == 'cancelled')>Cancelled</option>
                                    </select>
                                    <button type="submit" class="btn btn-sm btn-primary">Update</button>
                                </form>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Ordered Items -->
        <div class="wg-box mt-4">
            <div class="flex items-center justify-between gap10 flex-wrap">
                <div class="wg-filter flex-grow">
                    <h5>Ordered Items</h5>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th class="text-center">Price</th>
                            <th class="text-center">Quantity</th>
                            <th class="text-center">SKU</th>
                            <th class="text-center">Category</th>
                            <th class="text-center">Brand</th>
                            <th class="text-center">Options</th>
                            <th class="text-center">Return Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($orderItems as $item)
                        <tr>
                            <td class="pname">
                                <div class="image">
                                    <img src="{{ asset('uploads/products/' . $item->product->image) }}" 
                                         alt="{{ $item->product->name }}" width="50">
                                </div>
                                <div class="name">
                                    <a href="#" class="body-title-2">{{ $item->product->name }}</a>
                                </div>
                            </td>
                            <td class="text-center">${{ number_format($item->price, 2) }}</td>
                            <td class="text-center">{{ $item->quantity }}</td>
                            <td class="text-center">{{ $item->sku ?? '-' }}</td>
                            <td class="text-center">{{ $item->product->category->name ?? '-' }}</td>
                            <td class="text-center">{{ $item->product->brand->name ?? '-' }}</td>
                            <td class="text-center">
                                @if($item->options)
                                    @php $options = json_decode($item->options, true); @endphp
                                    @foreach($options as $key => $val)
                                        <div><b>{{ ucfirst($key) }}:</b> {{ $val }}</div>
                                    @endforeach
                                @else
                                    -
                                @endif
                            </td>
                            <td class="text-center">{{ $item->return_status ?? 'N/A' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Shipping Address -->
        <div class="wg-box mt-5">
            <h5>Shipping Address</h5>
            <div class="my-account__address-item col-md-6">
                <div class="my-account__address-item__detail">
                    <p>{{ $order->name }}</p>
                    <p>{{ $order->address }}</p>
                    <p>{{ $order->city }}, {{ $order->state }}</p>
                    <p>{{ $order->zip }}</p>
                    <br>
                    <p>Mobile : {{ $order->phone }}</p>
                </div>
            </div>
        </div>

        <!-- Transactions -->
        <div class="wg-box mt-5">
            <h5>Transactions</h5>
            <table class="table table-striped table-bordered table-transaction">
                <tbody>
                    <tr>
                        <th>Subtotal</th>
                        <td>${{ number_format($order->subtotal, 2) }}</td>
                        <th>Tax</th>
                        <td>${{ number_format($order->tax, 2) }}</td>
                        <th>Discount</th>
                        <td>${{ number_format($order->discount, 2) }}</td>
                    </tr>
                    <tr>
                        <th>Total</th>
                        <td>${{ number_format($order->total, 2) }}</td>
                        <th>Payment Mode</th>
                        <td>{{ $order->payment_mode ?? 'COD' }}</td>
                        <th>Status</th>
                        <td>{{ ucfirst($order->payment_status ?? 'pending') }}</td>
                    </tr>
                </tbody>
            </table>
        </div>

    </div>
</div>
@endsection
