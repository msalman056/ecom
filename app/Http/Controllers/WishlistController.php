<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Product;
use Surfsidemedia\Shoppingcart\Facades\Cart;

class WishlistController extends Controller
{


    public function index(){
        $items = Cart::instance('wishlist')->content();
        return view('wishlist', compact('items'));
    }

    public function add_to_wishlist(Request $request)
    {
        Cart::instance('wishlist')->add(
            $request->id,       // product ID
            $request->name, 
            $request->quantity, // quantity
            $request->price,    // price
            []                  // options (must be array)
        )->associate(\App\Models\Product::class);

        return redirect()->back()->with('success', 'Item added to wishlist');
    }
    public function remove_from_wishlist($id)
    {
        try {
            $item = Cart::instance('wishlist')->get($id);
            Cart::instance('wishlist')->remove($id);
            return redirect()->back()->with('success', 'Item removed from wishlist');
        } catch (\Surfsidemedia\Shoppingcart\Exceptions\InvalidRowIDException $e) {
            return redirect()->back()->with('error', 'Item not found in wishlist.');
        }
    }
    public function clear_wishlist()
    {
        Cart::instance('wishlist')->destroy();
        return redirect()->back()->with('success', 'Wishlist cleared');
    }
    public function move_to_cart($rowId)
{
    $item = Cart::instance('wishlist')->get($rowId);

    Cart::instance('wishlist')->remove($rowId);

    Cart::instance('cart')->add($item->id, $item->name, $item->qty, $item->price)
        ->associate('App\Models\Product');

    return redirect()->back();
}
}

    