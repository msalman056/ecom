@extends('layouts.admin')
@section('content')
@if(session('status'))
    <div class="alert alert-success" style="margin-bottom:18px; border-radius:6px; background:#e6fffa; color:#059669; padding:10px 18px; font-weight:500;">
        <i class="fa fa-check-circle" style="margin-right:8px;"></i> {{ session('status') }}
    </div>
@endif
<style>
    .order-status-form {
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }
    .order-status-form .form-select {
        border-radius: 6px;
        border: 1px solid #d1d5db;
        padding: 4px 12px;
        font-size: 0.98em;
        background: #f9fafb;
        color: #374151;
        transition: border-color 0.2s;
    }
    .order-status-form .form-select:focus {
        border-color: #2563eb;
        outline: none;
    }
    .order-status-form .btn-primary {
        background: linear-gradient(90deg, #34eba1 0%, #2563eb 100%);
        color: #fff;
        border: none;
        border-radius: 6px;
        padding: 4px 16px;
        font-weight: 600;
        font-size: 0.98em;
        box-shadow: 0 2px 6px rgba(52,235,161,0.08);
        transition: background 0.2s, box-shadow 0.2s;
    }
    .order-status-form .btn-primary:hover {
        background: linear-gradient(90deg, #2563eb 0%, #34eba1 100%);
        box-shadow: 0 4px 12px rgba(37,99,235,0.12);
    }
</style>
          <div class="main-content-inner">
                            <div class="main-content-wrap">
                                <div class="flex items-center flex-wrap justify-between gap20 mb-27">
                                    <h3>Orders</h3>
                                    <ul class="breadcrumbs flex items-center flex-wrap justify-start gap10">
                                        <li>
                                            <a href="{{ route('admin.index') }}">
                                                <div class="text-tiny">Dashboard</div>
                                            </a>
                                        </li>
                                        <li>
                                            <i class="icon-chevron-right"></i>
                                        </li>
                                        <li>
                                            <div class="text-tiny">Orders</div>
                                        </li>
                                    </ul>
                                </div>

                                <div class="wg-box">
                                    <div class="flex items-center justify-between gap10 flex-wrap">
                                        <div class="wg-filter flex-grow">
                                            <form class="form-search">
                                                <fieldset class="name">
                                                    <input type="text" placeholder="Search here..." class="" name="name"
                                                        tabindex="2" value="" aria-required="true" required="">
                                                </fieldset>
                                                <div class="button-submit">
                                                    <button class="" type="submit"><i class="icon-search"></i></button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                    <div class="wg-table table-all-user">
                                        <div class="table-responsive" style="overflow-x:auto;">
                                            <table class="table table-striped table-bordered" style="min-width:1200px; table-layout:fixed;">
                                                <thead>
                                                    <tr>
                                                        <th style="width:60px">OrderNo</th>
                                                        <th style="width:120px" class="text-center">Name</th>
                                                        <th style="width:110px" class="text-center">Phone</th>
                                                        <th style="width:90px" class="text-center">Subtotal</th>
                                                        <th style="width:80px" class="text-center">Tax</th>
                                                        <th style="width:90px" class="text-center">Total</th>
                                                        <th style="width:120px" class="text-center">Status</th>
                                                        <th style="width:120px" class="text-center">Order Date</th>
                                                        <th style="width:80px" class="text-center">Items</th>
                                                        <th style="width:110px" class="text-center">Delivered On</th>
                                                        <th style="width:90px" class="text-center">SKU</th>
                                                        <th style="width:110px" class="text-center">Category</th>
                                                        <th style="width:110px" class="text-center">Brand</th>
                                                        <th style="width:110px" class="text-center">Options</th>
                                                        <th style="width:60px"></th>
                                                    </tr>
                                                </thead>
                                                @foreach ($orders as $order)
                                                <tbody>
                                                    <tr>
                                                        <td class="text-center">{{ $order->id ?? '-' }}</td>
                                                        <td class="text-center">{{ $order->name ?? '-' }}</td>
                                                        <td class="text-center">{{ $order->phone ?? '-' }}</td>
                                                        <td class="text-center">${{ isset($order->subtotal) ? number_format($order->subtotal, 2) : '-' }}</td>
                                                        <td class="text-center">${{ isset($order->tax) ? number_format($order->tax, 2) : '-' }}</td>
                                                        <td class="text-center">${{ isset($order->total) ? number_format($order->total, 2) : '-' }}</td>

                                                        <td class="text-center">
                                                            <form method="POST" action="{{ route('admin.orders.updateStatus', $order->id) }}" class="order-status-form">
                                                                @csrf
                                                                @method('PATCH')
                                                                <select name="status" class="form-select form-select-sm" style="min-width:110px;">
                                                                    <option value="pending" @if($order->status=='pending') selected @endif>Pending</option>
                                                                    <option value="processing" @if($order->status=='processing') selected @endif>Processing</option>
                                                                    <option value="completed" @if($order->status=='completed') selected @endif>Completed</option>
                                                                    <option value="cancelled" @if($order->status=='cancelled') selected @endif>Cancelled</option>
                                                                    <option value="ordered" @if($order->status=='ordered') selected @endif>Ordered</option>
                                                                    <option value="delivered" @if($order->status=='delivered') selected @endif>Delivered</option>
                                                                    <option value="canceled" @if($order->status=='canceled') selected @endif>Canceled</option>
                                                                </select>
                                                                <button type="submit" class="btn btn-sm btn-primary">Update</button>
                                                            </form>
                                                        </td>
                                                        <td class="text-center">{{ $order->created_at ?? '-' }}</td>
                                                        <td class="text-center">{{ isset($order->orderItems) ? $order->orderItems->count() : '-' }}</td>
                                                        <td class="text-center">{{ $order->delivered_on ?? '-' }}</td>
                                                        <td class="text-center">
                                                            @if(isset($order->orderItems) && $order->orderItems->count() > 0)
                                                                {{ $order->orderItems[0]->SKU ?? '-' }}
                                                            @else
                                                                -
                                                            @endif
                                                        </td>
                                                        <td class="text-center">
                                                            @if(isset($order->orderItems) && $order->orderItems->count() > 0 && isset($order->orderItems[0]->product->category))
                                                                {{ $order->orderItems[0]->product->category->name ?? '-' }}
                                                            @else
                                                                -
                                                            @endif
                                                        </td>
                                                        <td class="text-center">
                                                            @if(isset($order->orderItems) && $order->orderItems->count() > 0 && isset($order->orderItems[0]->product->brand))
                                                                {{ $order->orderItems[0]->product->brand->name ?? '-' }}
                                                            @else
                                                                -
                                                            @endif
                                                        </td>
                                                        <td class="text-center">
                                                            @if(isset($order->orderItems) && $order->orderItems->count() > 0)
                                                                {{ $order->orderItems[0]->options ?? '-' }}
                                                            @else
                                                                -
                                                            @endif
                                                        </td>
                                                        <td class="text-center">
                                                            <a href="order-details.html">
                                                                <div class="list-icon-function view-icon">
                                                                    <div class="item eye">
                                                                        <i class="icon-eye"></i>
                                                                    </div>
                                                                </div>
                                                            </a>
                                                        </td>
                                                    </tr>
                                                   @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    <div class="divider"></div>
                                    <div class="flex items-center justify-between flex-wrap gap10 wgp-pagination">
                                     {{ $orders->links('pagination::bootstrap-5') }}
                                    </div>
                                </div>
                            </div>
                        </div>
@endsection