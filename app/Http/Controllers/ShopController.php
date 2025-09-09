<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Controllers\AdminController;
use App\Models\Brand;
use App\Models\Category;

class ShopController extends Controller
{
    public function index(Request $request)
    {
        $size = $request->query('size') ? $request->query('size') : 12;
        $o_column = 'created_at';
        $o_order = 'desc';
        $f_brands = $request->query('brand');
    $search = $request->query('search');
        $min_price = $request->query('min_price') ? $request->query('min_price') : 1;
        $max_price = $request->query('max_price') ? $request->query('max_price') : 500;
        switch($request->query('order')){
            case 1:
                $o_column='created_at';
                $o_order='desc';
                break;
            case 2:
                $o_column='created_at';
                $o_order='asc';
                break;
            case 3:
                $o_column='sale_price';
                $o_order='asc';
                break;
            case 4:
                $o_column='sale_price';
                $o_order='desc';
                break;
            default:
                $o_column='created_at';
                $o_order='desc';
        }
        $brand=Brand::orderby('name', 'asc')->get();    
    $categories=Category::withCount('products')->orderby('name', 'asc')->get();    
        $order=$request->query('order') ? $request->query('order') : -1;
        $products = Product::when($f_brands, function($query) use ($f_brands) {
                $query->whereIn('brand_id', explode(',', $f_brands));
            })
            ->when($search, function($query) use ($search) {
                $query->where(function($q) use ($search) {
                    $q->where('name', 'like', "%$search%")
                      ->orWhere('description', 'like', "%$search%")
                      ->orWhere('short_description', 'like', "%$search%");
                });
            })
            ->where(function($query) use ($min_price, $max_price) {
                $query->whereBetween('sale_price', [$min_price, $max_price])
                ->orWhereBetween('regular_price', [$min_price, $max_price]);
            })
            ->orderBy($o_column, $o_order)
            ->paginate($size);
    return view('shop', compact('products', 'size', 'order', 'brand', 'f_brands', 'categories', 'min_price', 'max_price'));
    }
    public function product_detail($product_slug)
    {
    $product = Product::where('slug', $product_slug)->first();
    $rproducts = Product::where('slug', '<>', $product->slug)->get()->take(8);
        return view('detail', compact('product', 'rproducts'));
    }
 

}