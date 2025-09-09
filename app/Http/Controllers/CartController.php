<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Surfsidemedia\Shoppingcart\Facades\Cart;
use App\Models\Coupon;
use Carbon\Carbon;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;
use App\Models\Address;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Transaction;


class CartController extends Controller
{
    
    public function index(Request $request)
    {
        $items = Cart::instance('cart')->content();
        return view('cart', compact('items'));
    }
    public function add_cart(Request $request)
    {
        // Debug: dump the incoming request data
        \Log::info('Add to cart request', $request->all());
        if (!$request->id) {
            return back()->with('error', 'Product ID is missing!');
        }
        if (!is_numeric($request->price) || $request->price === null) {
            return back()->with('error', 'Product price is invalid!');
        }
        Cart::instance('cart')->add($request->id, $request->name, $request->quantity, $request->price)->associate('App\\Models\\Products');
        return redirect()->route('cart.index')->with('success', 'Product added to cart!');
    }
       public function increase_cart_quantity($rowId)
{
    $product = Cart::instance('cart')->get($rowId);
    $qty = $product->qty + 1;
    Cart::instance('cart')->update($rowId, $qty);
    return redirect()->back();
}

       public function decrease_cart_quantity($rowId)
         {
    $product = Cart::instance('cart')->get($rowId);
    $qty = $product->qty - 1;
    Cart::instance('cart')->update($rowId, $qty);
    return redirect()->back();
            }
         public function remove_cart_item($rowId)
        {
          Cart::instance('cart')->remove($rowId);
         return redirect()->back();
         }
            public function clear_cart()
          {
                Cart::instance('cart')->destroy();
               return redirect()->back();
          }
          public function apply_coupon_code(Request $request)
{
    $coupon_code = $request->coupon_code;
    if (isset($coupon_code)) {

        $coupon = Coupon::where('code', $coupon_code)
            ->where('expiry_date', '>=', Carbon::today())
            ->where('cart_value', '<=', Cart::instance('cart')->subtotal())
            ->first();

        if (!$coupon) {
            return redirect()->back()->with('error', 'Invalid coupon code!');
        } else {
            Session::put('coupon', [
                'code'       => $coupon->code,
                'type'       => $coupon->type,
                'value'      => $coupon->value,
                'cart_value' => $coupon->cart_value,
            ]);
            $this->calculateDiscount();
            return redirect()->back()->with('success', 'Coupon applied successfully!');
        }
    } else {
        return redirect()->back()->with('error', 'Invalid coupon code!');
    }
}

public function calculateDiscount()
{
    $discount = 0;

  
    if (Session::has('coupon')) {

        
        if (Session::get('coupon')['type'] == 'fixed') {
            
            $discount = Session::get('coupon')['value'];
        } else {
           
            $discount = (Cart::instance('cart')->subtotal() * Session::get('coupon')['value']) / 100;
        }
    }

   
    $subtotalAfterDiscount = Cart::instance('cart')->subtotal() - $discount;

    
    $taxAfterDiscount = ($subtotalAfterDiscount * config('cart.tax')) / 100;

   
    $totalAfterDiscount = $subtotalAfterDiscount + $taxAfterDiscount;

  
    Session::put('discounts', [
        'discount' => number_format(floatval($discount), 2, '.', ''),
        'subtotal' => number_format(floatval($subtotalAfterDiscount), 2, '.', ''),
        'tax'      => number_format(floatval($taxAfterDiscount), 2, '.', ''),
        'total'    => number_format(floatval($totalAfterDiscount), 2, '.', ''),
    ]);
} 

    public function removeCoupon()
    {
        Session::forget('coupon');
        Session::forget('discounts');
        return redirect()->back()->with('success', 'Coupon removed successfully!');
    }
    public function checkout()
    {
        if(!Auth::check()) {
            return redirect()->route('login');
        }
        $address=Address::where('user_id',Auth::user()->id)->where('is_default',1   )->first();
        return view('checkout', compact('address'));

    }
    public function place_order(Request $request)
{
    $user_id = Auth::user()->id;
    $address = Address::where('user_id',$user_id)->where('is_default',true)->first();

    if(!$address)
    {
        $request->validate([
            'name' => 'required|max:100',
            'phone' => 'required|numeric|digits:11',
            'zip' => 'required|numeric|digits:5',
            'state' => 'required',
            'city' => 'required',
            'address' => 'required',
            'locality' => 'required',
            'landmark' => 'required',
        ]);

        $address = new Address();
        $address->name = $request->name;
        $address->phone = $request->phone;
        $address->zip = $request->zip;
        $address->state = $request->state;
        $address->city = $request->city;
        $address->address = $request->address;
        $address->locality = $request->locality;
        $address->landmark = $request->landmark;
        $address->country = 'pakistan';
        $address->user_id = $user_id;
    $address->is_default = true;
        $address->save();
    }
    $this->setAmountForCheckout();

        $order=new Order();
        $order->user_id=$user_id;
        $order->subtotal=Session::get('checkout')['subtotal'];
        $order->discount=Session::get('checkout')['discount'];
        $order->tax=Session::get('checkout')['tax'];
        $order->total=Session::get('checkout')['total'];
    $order->name=$address->name;
    $order->phone=$address->phone;
    $order->zip=$address->zip;   
    $order->state=$address->state;
    $order->city=$address->city;
    $order->address=$address->address;
    $order->locality=$address->locality;
    $order->landmark=$address->landmark;
    $order->country=$address->country;
        $order->status='ordered';
        $order->save();
        foreach(Cart::instance('cart')->content() as $item)
        {
            $orderItem=new OrderItem();
            $orderItem->order_id=$order->id;
            $orderItem->product_id=$item->id;
            $orderItem->quantity=$item->qty;
            $orderItem->price=$item->price;
            $orderItem->save();
        }

        if($request->mode=='card')
        {
            // Redirect to Stripe payment page with order id and total
            return redirect()->route('stripe.pay.page', ['order' => $order->id]);
        }
        else if($request->mode=='paypal')
        {
            //paypal
        }
        elseif($request->mode=='cod')
        {
            $transaction=new Transaction();
            $transaction->user_id=$user_id;
            $transaction->order_id=$order->id;
            $transaction->mode=$request->mode;
            $transaction->status='pending';
            $transaction->save();
            Cart::instance('cart')->destroy();
            Session::forget('coupon');
            Session::forget('discounts');
            Session::forget('checkout');
            Session::put('order_id',$order->id);
            return redirect()->route('cart.order_confirmation');
        }
}
public function setAmountForCheckout()
{
  
    if (Cart::instance('cart')->content()->count() <= 0) {
        Session::forget('checkout');
        return;
    }

   
    if (Session::has('coupon')) {
        Session::put('checkout', [
            'discount' => Session::get('discounts')['discount'],
            'tax'      => Session::get('discounts')['tax'],
            'subtotal' => Session::get('discounts')['subtotal'],
            'total'    => Session::get('discounts')['total'],
        ]);
    } else {
        
        Session::put('checkout', [
            'discount' => 0,
            'subtotal' => Cart::instance('cart')->subtotal(),
            'tax'      => Cart::instance('cart')->tax(),
            'total'    => Cart::instance('cart')->total(),
        ]);
    }

}
public function order_confirmation(Request $request)
{
    if (Session::has('order_id')) {
        $order = Order::find(Session::get('order_id'));
        return view('order_confirmation', compact('order'));
    }
    return redirect()->route('cart.index');
}
}