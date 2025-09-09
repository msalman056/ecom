@extends('layouts.app')
@section('content')
<style>
    .badge {
        padding: 0.5em 0.75em;
        font-size: 0.75em;
        font-weight: 600;
        border-radius: 0.375rem;
        color: #34eba1;
    }
    .badge.bg-warning {
        background-color: #f59e0b; /* Tailwind's yellow-500 */
    }
</style>

<main class="pt-90" style="padding-top: 0px;">
    <div class="mb-4 pb-4"></div>
    <section class="my-account container">
        <h2 class="page-title">Orders</h2>
        <div class="row">
            <div class="col-lg-2">
                @include('user.account-nav')
                           </div>

            <div class="col-lg-10">
                <div class="wg-table table-all-user">
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th style="width: 80px">OrderNo</th>
                                    <th>Name</th>
                                    <th class="text-center">Phone</th>
                                    <th class="text-center">Subtotal</th>
                                    <th class="text-center">Tax</th>
                                    <th class="text-center">Total</th>
                                    
                                    <th class="text-center">Status</th>
                                    <th class="text-center">Order Date</th>
                                    <th class="text-center">Items</th>
                                    <th class="text-center">Delivered On</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>

                                   @foreach ($orders as $order)
                                     <tr>
                                    <td class="text-center">{{ $order->id ?? '-' }}</td>  
                                    <td class="text-center">{{ $order->name ?? '-' }}</td>
                                    <td class="text-center">{{ $order->phone ?? '-' }}</td>
                                    <td class="text-center">${{ isset($order->subtotal) ? number_format($order->subtotal, 2) : '-' }}</td>
                                    <td class="text-center">${{ isset($order->tax) ? number_format($order->tax, 2) : '-' }}</td>
                                    <td class="text-center">${{ isset($order->total) ? number_format($order->total, 2) : '-' }}</td>

                                    <td class="text-center">
                                         <span  class="  badge bg-warning">{{ ucfirst($order->status ?? '-') }}</span>
                                    </td>
                                    <td class="text-center">{{ $order->created_at ?? '-' }}</td>
                                    <td class="text-center">{{ isset($order->orderItems) ? $order->orderItems->count() : '-' }}</td>
                                    <td class="text-center">{{ $order->delivered_on ?? '-' }}   </td>
                                    <td class="text-center">
                                        <a href="account-orders-details.html">
                                        <div class="list-icon-function view-icon">
                                            <div class="item eye">
                                                <i class="fa fa-eye"></i>
                                            </div>                                        
                                        </div>
                                        </a>
                                    </td>
                                </tr>
                                <!-- Enhanced Product Details Row -->
                                <tr>
                                    <td colspan="12" style="background: #f6f8fa; padding:0;">
                                        <div style="padding:18px 24px; border-radius:8px; box-shadow:0 2px 8px rgba(0,0,0,0.04); margin:10px 0;">
                                            <div style="font-weight:600; font-size:1.1em; margin-bottom:10px; color:#2d3748;">
                                                <i class="fa fa-box" style="margin-right:6px; color:#34eba1;"></i> Products in this order
                                            </div>
                                            <table class="table table-bordered table-hover" style="background:#fff; border-radius:6px; overflow:hidden;">
                                                <thead style="background:#e2e8f0;">
                                                    <tr>
                                                        <th style="width:40%">Name</th>
                                                        <th style="width:20%">Price</th>
                                                        <th style="width:20%">Quantity</th>
                                                        <th style="width:20%">Subtotal</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @php
                                                        $totalPrice = 0;
                                                    @endphp
                                                    @forelse ($order->orderItems as $orderItem)
                                                        @php
                                                            $price = $orderItem->product->sale_price ?? $orderItem->product->regular_price ?? 0;
                                                            $subtotal = $price * ($orderItem->quantity ?? 1);
                                                            $totalPrice += $subtotal;
                                                        @endphp
                                                        <tr>
                                                            <td style="vertical-align:middle;">
                                                                <i class="fa fa-cube" style="color:#34eba1; margin-right:4px;"></i>
                                                                {{ $orderItem->product->name ?? '-' }}
                                                            </td>
                                                            <td style="vertical-align:middle;">
                                                                <span style="font-weight:500; color:#2563eb;">
                                                                    ${{ number_format($price, 2) }}
                                                                </span>
                                                            </td>
                                                            <td style="vertical-align:middle;">
                                                                <span style="background:#f59e0b; color:#fff; padding:2px 10px; border-radius:12px; font-size:0.95em;">
                                                                    {{ $orderItem->quantity ?? '-' }}
                                                                </span>
                                                            </td>
                                                            <td style="vertical-align:middle;">
                                                                <span style="font-weight:500; color:#059669;">
                                                                    ${{ number_format($subtotal, 2) }}
                                                                </span>
                                                            </td>
                                                        </tr>
                                                    @empty
                                                        <tr><td colspan="4" style="text-align:center; color:#888;">No products found for this order.</td></tr>
                                                    @endforelse
                                                    <tr style="background:#e2e8f0; font-weight:600;">
                                                        <td colspan="3" style="text-align:right;">Subtotal:</td>
                                                        <td style="color:#059669;">${{ number_format($totalPrice, 2) }}</td>
                                                    </tr>
                                                    <tr style="background:#e2e8f0; font-weight:600;">
                                                        <td colspan="3" style="text-align:right;">Tax:</td>
                                                        <td style="color:#f59e0b;">${{ isset($order->tax) ? number_format($order->tax, 2) : '0.00' }}</td>
                                                    </tr>
                                                    <tr style="background:#e2e8f0; font-weight:600;">
                                                        <td colspan="3" style="text-align:right;">Grand Total:</td>
                                                        <td style="color:#2563eb;">${{ number_format($totalPrice + ($order->tax ?? 0), 2) }}</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
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
    </section>
</main>
@endsection