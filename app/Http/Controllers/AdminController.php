<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Brand;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Models\Product;
use App\Models\Category;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Transaction;
use App\Models\Slider;
use App\Models\User;


use Illuminate\Support\Facades\Auth;
use App\Events\ProductAdded;
use App\Helpers\EmailHelper;
use Illuminate\Support\Facades\Log;

class AdminController extends Controller
   
{
    function index()
    {
        $totalOrders = Order::count();
        $totalAmount = Order::sum('total');
        $pendingOrders = Order::where('status', 'ordered')->count();
        $pendingAmount = Order::where('status', 'ordered')->sum('total');
        $recentOrders = Order::orderBy('created_at', 'desc')->take(5)->get();
        $revenue = Order::where('status', 'delivered')->sum('total');
        $orderAmount = Order::sum('total');
    $deliveredOrders = Order::where('status', 'delivered')->count();
    $deliveredAmount = Order::where('status', 'delivered')->sum('total');
    $canceledOrders = Order::where('status', 'canceled')->count();
    $canceledAmount = Order::where('status', 'canceled')->sum('total');
    $admin = Auth::user();
    $notifications = $admin ? $admin->unreadNotifications()->take(5)->get() : collect();
    return view('admin.index', compact('totalOrders', 'totalAmount', 'pendingOrders', 'pendingAmount', 'recentOrders', 'revenue', 'orderAmount', 'deliveredOrders', 'deliveredAmount', 'canceledOrders', 'canceledAmount', 'notifications'));
    }
    public function brands()
    {
        $brands = Brand::orderBy('id', 'ASC')->paginate(10);
        return view('admin.brands', compact('brands'));
    }
    public function addbrand()
    {
        return view('admin.brands-add');
    }
    public function add_brand()
{
    return view('admin.brand-add');
}

    public function brand_store(Request $request){
    $request->validate([
        'name' => 'required',
        'slug' => 'required|unique:brands,slug',
        'image' => 'mimes:png,jpg,jpeg|max:2048'
    ]);

    $brand = new Brand;
    $brand->name = $request->name;
    $brand->slug = Str::slug($request->name);
    $image = $request->file('image');
    $file_extention = $request->file('image')->extension();
    $file_name = Carbon::now()->timestamp.'.'.$file_extention;
    $this->GenerateBrandThumbnailsImage($image, $file_name);
    $brand->image = $file_name;
    $brand->save();
    return redirect()->route('admin.brand')->with('status','Brand has been added succesfully!');
    }

      public function GenerateBrandThumbnailsImage($image, $imageName)
    {
    $destinationPath = public_path('uploads/brands');
    $img = Image::make($image->path());
    $img = $img->fit(124, 124, function($constraint){
        $constraint->aspectRatio();
    }, 'top');
    $img->save($destinationPath.'/'.$imageName);
}
         public function brand_edit($id)
         {
             $brand = Brand::findOrFail($id);
             return view('admin.brand-edit', compact('brand'));
         }
         public function brand_update(Request $request, $id)
{
    $request->validate([
        'name' => 'required',
        'slug' => 'required|unique:brands,slug,' . $id,
        'image' => 'mimes:png,jpg,jpeg|max:2048'
    ]);

    $brand = Brand::findOrFail($id);
    $brand->name = $request->name;
    $brand->slug = Str::slug($request->name);

    if ($request->hasFile('image')) {
        // Delete old image
        if ($brand->image && file_exists(public_path('uploads/brands/' . $brand->image))) {
            unlink(public_path('uploads/brands/' . $brand->image));
        }

        $image = $request->file('image');
        $file_extention = $image->extension();
        $file_name = Carbon::now()->timestamp . '.' . $file_extention;

        $this->GenerateBrandThumbnailsImage($image, $file_name);

        $brand->image = $file_name;
    }

    $brand->save();

    return redirect()->route('admin.brand')->with('status', 'Brand has been updated successfully!');
}
       public function brand_delete($id)
      {
           $brand = Brand::findOrFail($id);

    // Delete old image if exists
    if ($brand->image && file_exists(public_path('uploads/brands/' . $brand->image))) {
        unlink(public_path('uploads/brands/' . $brand->image));
    }

    $brand->delete();

    return redirect()->route('admin.brand')->with('status', 'Brand has been deleted successfully!');

       }




       public function products(Request $request)
       {
           $query = Product::orderBy('id', 'ASC');
           if ($request->filled('name')) {
               $query->where('name', 'like', '%' . $request->name . '%');
           }
           $products = $query->paginate(10);
           // Keep search query in pagination links
           if ($request->filled('name')) {
               $products->appends(['name' => $request->name]);
           }
           return view('admin.products', compact('products'));
       }


          // Product Categories
          public function categories()
          {
              $categories = Category::orderBy('id', 'ASC')->paginate(10);
              return view('admin.categories', compact('categories'));
          }

          public function addcategory()
          {
              return view('admin.add-category');
          }

          public function category_store(Request $request)
          {
        $request->validate([
            'name' => 'required',
            'slug' => 'required|unique:categories,slug',
            'image' => 'mimes:png,jpg,jpeg|max:2048'
        ]);

        $category = new Category;
        $category->name = $request->name;
        $category->slug = Str::slug($request->name);
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $file_extention = $image->extension();
            $file_name = \Carbon\Carbon::now()->timestamp . '.' . $file_extention;
            $destinationPath = public_path('uploads/categories');
            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0777, true);
            }
            $image->move($destinationPath, $file_name);
            $category->image = $file_name;
        }
        $category->save();

        return redirect()->route('admin.categories')->with('status', 'Category has been added successfully!');
          }

          public function category_edit($id)
          {

              $category = Category::findOrFail($id);
              return view('admin.categories-edit', compact('category'));
          }

        public function category_update(Request $request, $id)
        {
            $request->validate([
                'name' => 'required',
                'slug' => 'required|unique:categories,slug,' . $id,
                'image' => 'nullable|mimes:png,jpg,jpeg|max:2048'
            ]);

            $category = Category::findOrFail($id);
            $category->name = $request->name;
            $category->slug = Str::slug($request->name);

            if ($request->hasFile('image')) {
                // Delete old image if exists
                if ($category->image && file_exists(public_path('uploads/categories/' . $category->image))) {
                    unlink(public_path('uploads/categories/' . $category->image));
                }
                $image = $request->file('image');
                $file_extention = $image->extension();
                $file_name = \Carbon\Carbon::now()->timestamp . '.' . $file_extention;
                $destinationPath = public_path('uploads/categories');
                if (!file_exists($destinationPath)) {
                    mkdir($destinationPath, 0777, true);
                }
                $image->move($destinationPath, $file_name);
                $category->image = $file_name;
            }

            $category->save();

            return redirect()->route('admin.categories')->with('status', 'Category has been updated successfully!');
        }

         public function destroy($id)
         {
    $category = Category::findOrFail($id);
    $category->delete();

    return redirect()->route('admin.categories')->with('success', 'Category deleted successfully!');
          }


         //product
          public function addproduct()
          {
            $categories = Category::select('id', 'name')->orderby('name')->get();
            $brands = Brand::select('id', 'name')->orderby('name')->get();
              return view('admin.add-products', compact('categories', 'brands'));
          }


          // Store product
            public function product_store(Request $request)
            {
                $validated = $request->validate([
                    'name' => 'required|string|max:100',
                    'slug' => 'required|string|max:100|unique:products,slug',
                    'short_description'=> 'nullable|string',
                    'description'      => 'required|string',
                    'regular_price'    => 'required|numeric',
                    'sale_price'       => 'nullable|numeric',
                    'SKU'              => 'required|string|max:100',
                    'stock_status'     => 'required|in:instock,outofstock',
                    'featured'         => 'boolean',
                    'quantity'         => 'nullable|integer|min:0',
                    'image'            => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
                    'images.*'         => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
                    'category_id'      => 'nullable|exists:categories,id',
                    'brand_id'         => 'nullable|exists:brands,id',
                ]);

                $uploadPath = public_path('uploads/products');
                if (!file_exists($uploadPath)) {
                    mkdir($uploadPath, 0777, true);
                }

                // Handle main image
                if ($request->hasFile('image')) {
                    $imageName = uniqid().'_'.$request->file('image')->getClientOriginalName();
                    $request->file('image')->move($uploadPath, $imageName);
                    $validated['image'] = ''.$imageName;
                }

                // Handle gallery images
                if ($request->hasFile('images')) {
                    $images = [];
                    foreach ($request->file('images') as $file) {
                        $galleryImageName = uniqid().'_'.$file->getClientOriginalName();
                        $file->move($uploadPath, $galleryImageName);
                        $images[] = ''.$galleryImageName;
                    }
                    $validated['images'] = json_encode($images);
                }

                $product = Product::create($validated);

                // Fire the ProductAdded event
                event(new ProductAdded($product));

                return redirect()->route('admin.products')->with('success', 'Product created and notifications sent!');
            }
        public function product_edit($id)
        {
            $product = Product::findOrFail($id);
            $categories = Category::select('id', 'name')->orderby('name')->get();
            $brands = Brand::select('id', 'name')->orderby('name')->get();
            return view('admin.products-edit', compact('product', 'categories', 'brands'));
        }
        public function product_update(Request $request, $id)
        {
            $product = Product::findOrFail($id);

            $validated = $request->validate([
                'name' => 'required|string|max:100',
                'slug' => 'required|string|max:100|unique:products,slug,' . $id,
                'short_description'=> 'nullable|string',
                'description'      => 'required|string',
                'regular_price'    => 'required|numeric',
                'sale_price'       => 'nullable|numeric',
                'SKU'              => 'required|string|max:100',
                'stock_status'     => 'required|in:instock,outofstock',
                'featured'         => 'boolean',
                'quantity'         => 'nullable|integer|min:0',
                'image'            => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
                'images.*'         => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
                'category_id'      => 'nullable|exists:categories,id',
                'brand_id'         => 'nullable|exists:brands,id',
            ]);

            $uploadPath = public_path('uploads/products');
            if (!file_exists($uploadPath)) {
                mkdir($uploadPath, 0777, true);
            }

            // Handle main image
            if ($request->hasFile('image')) {
                // Delete old image if exists
                if ($product->image && file_exists(public_path($product->image))) {
                    unlink(public_path($product->image));
                }
                $imageName = uniqid().'_'.$request->file('image')->getClientOriginalName();
                $request->file('image')->move($uploadPath, $imageName);
                $validated['image'] = ''.$imageName;
            }

            // Handle gallery images
            if ($request->hasFile('images')) {
                // Delete old gallery images if exist
                if ($product->images) {
                    $oldImages = json_decode($product->images, true);
                    if (is_array($oldImages)) {
                        foreach ($oldImages as $oldImage) {
                            if (file_exists(public_path($oldImage))) {
                                unlink(public_path($oldImage));
                            }
                        }
                    }
                }

                $images = [];
                foreach ($request->file('images') as $file) {
                    $galleryImageName = uniqid().'_'.$file->getClientOriginalName();
                    $file->move($uploadPath, $galleryImageName);
                    $images[] = ''.$galleryImageName;
                }
                $validated['images'] = json_encode($images);
            }

            $product->update($validated);

            return redirect()->route('admin.products')->with('success', 'Product updated successfully!');
        }
        public function product_delete($id)
        {
            $product = Product::findOrFail($id);

            // Delete main image if exists
            if ($product->image && file_exists(public_path($product->image))) {
                unlink(public_path($product->image));
            }

            // Delete gallery images if exist
            if ($product->images) {
                $oldImages = json_decode($product->images, true);
                if (is_array($oldImages)) {
                    foreach ($oldImages as $oldImage) {
                        if (file_exists(public_path($oldImage))) {
                            unlink(public_path($oldImage));
                        }
                    }
                }
            }

            $product->delete();

            return redirect()->route('admin.products')->with('success', 'Product deleted successfully!');
        }
        public function coupons()
    {
        $coupons = Coupon::orderby('created_at', 'ASC')->paginate(10);
        return view('admin.coupons', compact('coupons'));
    }
    public function createCoupon()
    {
        return view('admin.coupon-add');
    }
    public function coupon_store(Request $request)
{
    
    $request->validate([
        'code'       => 'required',
        'type'       => 'required',
        'value'      => 'required|numeric',
        'cart_value' => 'required|numeric',
        'expiry_date'=> 'required|date',
    ]);

    
    $coupon = new Coupon();
    $coupon->code       = $request->code;
    $coupon->type       = $request->type;
    $coupon->value      = $request->value;
    $coupon->cart_value = $request->cart_value;
    $coupon->expiry_date= $request->expiry_date;
    $coupon->save();

   
return redirect()->route('admin.coupons')->with('status','Coupon has been added successfully!');
}
    public function editCoupon($id)
    {
        $coupon = Coupon::findOrFail($id);
        return view('admin.coupons-edit', compact('coupon'));
    }
    public function updateCoupon(Request $request, $id)
{
    
    $request->validate([
        'code'       => 'required',
        'type'       => 'required',
        'value'      => 'required|numeric',
        'cart_value' => 'required|numeric',
        'expiry_date'=> 'required|date',
    ]);


    $coupon = Coupon::findOrFail($id);
    $coupon->code       = $request->code;
    $coupon->type       = $request->type;
    $coupon->value      = $request->value;
    $coupon->cart_value = $request->cart_value;
    $coupon->expiry_date= $request->expiry_date;
    $coupon->save();

    return redirect()->route('admin.coupons')->with('status', 'Coupon has been updated successfully!');
}
    public function deleteCoupon($id)
    {
        $coupon = Coupon::findOrFail($id);
        $coupon->delete();

        return redirect()->route('admin.coupons')->with('status', 'Coupon has been deleted successfully!');
    }
    public function order()
    {
        $orders = Order::orderBy('id', 'ASC')->paginate(10);
        return view('admin.orders', compact('orders'));
    }
    public function orderDetails($id)
    {
        $order = Order::with('orderItems')->findOrFail($id);
        $orderItems = OrderItem::where('order_id', $id)->orderBy('id', 'ASC')->paginate(12);
        $transaction = Transaction::where('order_id', $id)->first();
        return view('admin.order-details', compact('order', 'orderItems', 'transaction'));

    }
    public function setting()
    {
        $user = auth()->user();
        return view('admin.setting', compact('user'));
    }

    public function updateSetting(Request $request)
    {
        $user = auth()->user();
        $request->validate([
            'name' => 'required|string|max:255',
            'mobile' => 'required|string|max:20',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'old_password' => 'nullable|string',
            'new_password' => 'nullable|string|min:6|confirmed',
        ]);

        $user->name = $request->name;
        $user->mobile = $request->mobile;
        $user->email = $request->email;

        // Handle password change
        if ($request->filled('old_password') && $request->filled('new_password')) {
            if (!\Hash::check($request->old_password, $user->password)) {
                return back()->withErrors(['old_password' => 'Old password is incorrect.'])->withInput();
            }
            $user->password = bcrypt($request->new_password);
        }

        $user->save();
        return back()->with('success', 'Settings updated successfully.');
    }
    public function updateOrderStatus(Request $request, $id)
    {
        $order = Order::findOrFail($id);
        $request->validate([
            'status' => 'required|in:pending,processing,completed,cancelled,ordered,delivered,canceled'
        ]);
        $oldStatus = $order->status;
        $order->status = $request->status;
        $order->save();

        // Update related transaction status if exists
        $transaction = $order->transaction;
        if ($transaction) {
            // Map order status to transaction status
            $transactionStatus = 'pending';
            if (in_array($order->status, ['completed', 'delivered'])) {
                $transactionStatus = 'approved';
            } elseif (in_array($order->status, ['cancelled', 'canceled'])) {
                $transactionStatus = 'declined';
            }
            $transaction->status = $transactionStatus;
            $transaction->save();
        }


        // Notify the user about the status change, only if email is valid
        $user = $order->user;
        if ($user && EmailHelper::isValidEmail($user->email)) {
            try {
                $user->notify(new \App\Notifications\OrderStatusChanged($order, $oldStatus, $order->status));
            } catch (\Exception $e) {
                Log::error('Failed to send order status notification: ' . $e->getMessage(), [
                    'user_id' => $user->id,
                    'email' => $user->email,
                    'order_id' => $order->id,
                ]);
            }
        } else if ($user) {
            Log::warning('Order status notification not sent: invalid or missing email address.', [
                'user_id' => $user->id,
                'email' => $user->email,
                'order_id' => $order->id,
            ]);
        }

        return redirect()->back()->with('status', 'Order status updated successfully!');
    }
    public function slider()
    {
            $sliders = Slider::orderBy('id', 'ASC')->paginate(10);
        return view('admin.slider', compact('sliders'));
    }
    public function add_slider()
    {

        return view('admin.add-slider');
    }public function create()
    {
        return view('banners.create'); // create.blade.php file
    }

    /**
     * Store a newly created banner in DB.
     */
    public function slider_store(Request $request)
    {
        // Validate input
        $request->validate([
            'image'    => 'required|image|mimes:jpg,jpeg,png,gif|max:2048',
            'tagline'  => 'nullable|string|max:255',
            'title'    => 'nullable|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'link'     => 'nullable|url|max:255',
        ]);

        // Upload Image
         $imagePath = null;
    if ($request->hasFile('image')) {
        $image = $request->file('image');
        $imageName = time().'_'.$image->getClientOriginalName();
        $image->move(public_path('uploads/Slider'), $imageName);
        $imagePath = 'uploads/Slider/'.$imageName; // relative path for DB
    }

        Slider::create([
            'image'    => $imagePath,
            'tagline'  => $request->tagline,
            'title'    => $request->title,
            'subtitle' => $request->subtitle,
            'link'     => $request->link,
        ]);

        return redirect()->route('admin.slider')
            
        ->with('success', 'Slider created successfully!');
    }
  
    public function slider_edit($id)
{
    $slide = Slider::findOrFail($id);
    return view('admin.slider_edit', compact('slide'));
}

public function slider_update(Request $request, $id)
{
    $request->validate([
        'title'    => 'required|string|max:255',
        'tagline'  => 'nullable|string|max:255',
        'subtitle' => 'nullable|string|max:255',
        'link'     => 'nullable|url|max:255',
        'image'    => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
    ]);

    $slide = Slider::findOrFail($id);
    $slide->title    = $request->title;
    $slide->tagline  = $request->tagline;
    $slide->subtitle = $request->subtitle;
    $slide->link     = $request->link;

    if ($request->hasFile('image')) {
        // Delete old image if exists
        if ($slide->image && file_exists(public_path('uploads/Slider/'.$slide->image))) {
            unlink(public_path(''.$slide->image));
        }

        $imageName = time().'.'.$request->image->extension();
        $request->image->move(public_path('uploads/Slider'), $imageName);
        $slide->image = $imageName;
    }

    $slide->save();

    return redirect()->route('admin.slider')
                     ->with('success', 'Slide updated successfully!');
}
public function slider_delete($id)
{
    $slide = Slider::findOrFail($id);

    // Delete image file if exists
    if ($slide->image && file_exists(public_path('uploads/sliders/'.$slide->image))) {
        unlink(public_path(''.$slide->image));
    }

    $slide->delete();

    return redirect()->route('admin.slider')
                     ->with('success', 'Slide deleted successfully!');  
}

 public function users()
    {
    $users = User::orderBy('id', 'ASC')->paginate(10);
        return view('admin.user', compact('users'));
    }
    public function search(){
        $query = request()->input('query');
        $users = User::where('name', 'like', "%$query%")->get()->take(10);
        return response()->json($results);
    }
}