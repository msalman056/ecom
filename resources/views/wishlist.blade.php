@extends('layouts.app')

@section('content')
<main class="pt-90">
  <div class="mb-4 pb-4"></div>
  <section class="shop-checkout container">
    <h2 class="page-title">Wishlist</h2>
    
    <div class="shopping-cart">
      <div class="cart-table__wrapper">
        <table class="cart-table">
          <thead>
            <tr>
              <th>Product</th>
              <th></th>
              <th>Price</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody>
            @forelse ($items as $item)
            <tr>
              <td>
                <div class="shopping-cart__product-item">
                  <img loading="lazy" 
                       src="{{ asset('uploads/products/' . $item->model->image) }}" 
                       width="120" height="120" 
                       alt="{{ $item->model->name }}" />
                </div>
              </td>
              <td>
                <div class="shopping-cart__product-item__detail">
                  <h4>{{ $item->name }}</h4>
                </div>
              </td>
              <td>
                <span class="shopping-cart__product-price">${{ $item->price }}</span>
              </td>
              <td>
                <div class="row ">
                  <div class="col-6">
                    <form action="{{ route('wishlist.move_to_cart', $item->rowId) }}" method="POST" id="move-to-cart-{{ $item->rowId }}">
                      @csrf
                      <button type="submit" class="btn btn-primary btn-sm">Move to Cart</button>
                    </form>
                  </div>
                </div>
                <form action="{{ route('wishlist.remove', $item->rowId) }}" method="POST" id="remove-wishlist-{{ $item->rowId }}">
                  @csrf
                  @method('DELETE')
              
                <a href="javascript:void(0)" class="remove-cart" onclick="document.getElementById('remove-wishlist-{{ $item->rowId }}').submit();">
                  <svg width="10" height="10" viewBox="0 0 10 10" fill="#767676" xmlns="http://www.w3.org/2000/svg">
                    <path d="M0.259435 8.85506L9.11449 0L10 0.885506L1.14494 9.74056L0.259435 8.85506Z" />
                    <path d="M0.885506 0.0889838L9.74057 8.94404L8.85506 9.82955L0 0.97449L0.885506 0.0889838Z" />
                  </svg>
                </a>
                </form>
              </td>
            </tr>
            @empty
            <tr>
              <td colspan="4" class="text-center">Your wishlist is empty.</td>
            </tr>
            @endforelse
          </tbody>
        </table>
        <div class="cart-table-footer">
            <form action=""></form>
            <button class="btn btn-light">UPDATE CART</button>
            
          </div>
      </div>
    </div>
  </section>
</main>
@endsection
