
<?php
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\AuthAdmin;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\WishlistController;


Auth::routes();

Route::get('/', [HomeController::class, 'index'])->name('home.index');
//shop
Route::get('/shop', [ShopController::class, 'index'])->name('shop.index');
Route::get('/shop/{product_slug}', [ShopController::class, 'product_detail'])->name('shop.product.detail');

//logout
Route::post('/logout', [UserController::class, 'logout'])->name('user.logout');
//slider

Route::get('/admin/sliders', [UserController::class, 'slider'])->name('admin.slider.index');


//cart
Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::post('/cart/add', [CartController::class, 'add_cart'])->name('cart.add');
Route::put('/cart/increase_quantity/{rowId}', [CartController::class, 'increase_cart_quantity'])->name('cart.update');
Route::put('/cart/decrease_quantity/{rowId}', [CartController::class, 'decrease_cart_quantity'])->name('cart.remove');
Route::delete('/cart/remove/{rowId}', [CartController::class, 'remove_cart_item'])->name('cart.remove');
Route::delete('/cart/clear', [CartController::class, 'clear_cart'])->name('cart.clear');
Route::post('/cart/apply-coupon', [CartController::class, 'apply_coupon_code'])->name('cart.apply_coupon');
Route::post('/cart/remove-coupon', [CartController::class, 'removeCoupon'])->name('cart.remove_coupon');

//checkout
Route::get('/checkout', [CartController::class, 'checkout'])->name('cart.checkout');
Route::post('/checkout', [CartController::class, 'place_order'])->name('cart.place_order');
Route::get('/order-confirmation', [CartController::class, 'order_confirmation'])->name('cart.order_confirmation');

// Stripe payment
use App\Http\Controllers\StripeController;
Route::get('/stripe', [StripeController::class, 'showForm'])->name('stripe.form');
Route::post('/stripe/pay', [StripeController::class, 'processPayment'])->name('stripe.pay');
Route::get('/stripe/pay/{order}', [StripeController::class, 'showPaymentPage'])->name('stripe.pay.page');

//

Route::get('/user/orders', [UserController::class, 'orders'])->name('user.orders');


//slider
Route::get('/admin/slider', [AdminController::class, 'slider'])->name('admin.slider');
Route::get('/admin/slider/add', [AdminController::class, 'add_slider'])->name('admin.slider.add');
Route::post('/admin/slider/add', [AdminController::class, 'slider_store'])->name('admin.slider.store');
Route::get('/admin/slider/edit/{id}', [AdminController::class, 'slider_edit'])->name('admin.slider.edit');
Route::put('/admin/slider/edit/{id}', [AdminController::class, 'slider_update'])->name('admin.slider.update');
Route::delete('/admin/slider/delete/{id}', [AdminController::class, 'slider_delete'])->name('admin.slider.delete');

//wishlist


Route::post('/wishlist/add', [WishlistController::class, 'add_to_wishlist'])->name('wishlist.add');
Route::get('/wishlist', [WishlistController::class, 'index'])->name('wishlist.index');
Route::delete('/wishlist/remove/{rowId}', [WishlistController::class, 'remove_from_wishlist'])->name('wishlist.remove');
Route::delete('/wishlist/clear', [WishlistController::class, 'clear_wishlist'])->name('wishlist.clear');
Route::post('/wishlist/move-to-cart/{rowId}', [WishlistController::class, 'move_to_cart'])->name('wishlist.move_to_cart');

//about
Route::get('/about', [HomeController::class, 'about'])->name('home.about');


//contact
Route::get('/contact', [HomeController::class, 'contact'])->name('contact');
Route::post('/contact', [HomeController::class, 'contact_store'])->name('contact.store');


//admin 
Route::middleware(['auth', AuthAdmin::class])->group(function () {
    // users
    Route::get('/admin/users', [AdminController::class, 'users'])->name('admin.users');
    Route::get('/admin', [AdminController::class, 'index'])->name('admin.index');
    //brands
    Route::get('/admin/brands', [AdminController::class, 'brands'])->name('admin.brand');
    Route::get('/admin/brands/add', [AdminController::class, 'addbrand'])->name('admin.brands.add');
    Route::post('/admin/brands/add', [AdminController::class, 'brand_store'])->name('admin.brands.store');
    Route::get('/admin/brands/edit/{id}', [AdminController::class, 'brand_edit'])->name('admin.brands.edit');
    Route::put('/admin/brands/edit/{id}', [AdminController::class, 'brand_update'])->name('admin.brand.update');
    Route::delete('/admin/brands/delete/{id}', [AdminController::class, 'brand_delete'])->name('admin.brands.delete');

    //products 
    Route::get('/admin/products', [AdminController::class, 'products'])->name('admin.products');
    Route::get('/admin/products/create', [AdminController::class, 'addproduct'])->name('admin.products.create');
    Route::post('/admin/products/create', [AdminController::class, 'product_store'])->name('admin.products.store');
    Route::get('/admin/products/edit/{id}', [AdminController::class, 'product_edit'])->name('admin.products.edit');
    Route::put('/admin/products/edit/{id}', [AdminController::class, 'product_update'])->name('admin.products.update');
    Route::delete('/admin/products/{id}', [AdminController::class, 'product_delete'])->name('admin.products.delete');
    //categories
    Route::get('/admin/categories', [AdminController::class, 'categories'])->name('admin.categories');
    Route::get('/admin/categories/add', [AdminController::class, 'addcategory'])->name('admin.categories.add');
    Route::post('/admin/categories/add', [AdminController::class, 'category_store'])->name('admin.categories.store');
    Route::get('/admin/categories/edit/{id}', [AdminController::class, 'category_edit'])->name('admin.categories.edit');
    Route::put('/admin/categories/edit/{id}', [AdminController::class, 'category_update'])->name('admin.categories.update');
    Route::delete('/admin/categories/delete/{id}', [AdminController::class, 'destroy'])->name('admin.categories.delete');
    //coupons
    Route::get('/admin/coupons', [AdminController::class, 'coupons'])->name('admin.coupons');
Route::get('/admin/coupons/create', [AdminController::class, 'createCoupon'])->name('admin.coupons.create');
    Route::post('/admin/coupons/create', [AdminController::class, 'coupon_store'])->name('admin.coupons.store');
    Route::get('/admin/coupons/edit/{id}', [AdminController::class, 'editCoupon'])->name('admin.coupons.edit');
    Route::put('/admin/coupons/edit/{id}', [AdminController::class, 'updateCoupon'])->name('admin.coupons.update');
    Route::delete('/admin/coupons/delete/{id}', [AdminController::class, 'deleteCoupon'])->name('admin.coupons.delete');

    //orders
    Route::get('/admin/orders', [AdminController::class, 'order'])->name('admin.orders');
    Route::get('/admin/orders/{id}', [AdminController::class, 'orderDetails'])->name('admin.order.details');
    Route::get('/admin/orders/{id}/tracking', [AdminController::class, 'orderTracking'])->name('admin.order.tracking');
    Route::patch('/admin/orders/{id}/status', [AdminController::class, 'updateOrderStatus'])->name('admin.orders.updateStatus');


    //settings
    Route::get('/admin/setting', [AdminController::class, 'setting'])->name('admin.setting');
        // admin logout route for admin panel
        Route::post('/admin/logout', function () {
            \Auth::logout();
            request()->session()->invalidate();
            request()->session()->regenerateToken();
            return redirect('/account-login');
        })->name('logout');
    Route::post('/admin/setting', [AdminController::class, 'updateSetting'])->name('admin.setting.update');
});

//user account
Route::get('/account-login', [UserController::class, 'login'])->name('user.login');
Route::post('/account-login', [UserController::class, 'login']);



// Search users
Route::get('/admin/search', [AdminController::class, 'search'])->name('admin.users.search');  

Route::middleware(['auth'])->group(function () {
    Route::get('/account-dashboard', [UserController::class, 'index'])->name('user.index');
});
