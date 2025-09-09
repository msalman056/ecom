<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Slider;
use App\Models\Category;
use Illuminate\Support\Facades\Validator;
use App\Models\Contact;
use App\Models\Product;


class HomeController extends Controller
{
    
    public function index()
    {
        $sliders = Slider::latest()->get();
        $categories = Category::orderby('name', 'asc')->get();
        $sproducts = Product::WhereNotNull('sale_price')->where('sale_price', '<>', '')->inRandomOrder()->take(8)->get();
        return view('index', compact('sliders', 'categories', 'sproducts'));
    }
    public function about()
    {
        return view('about');
    }  
    
    public function contact()
   {
       return view('contact');
   }
    public function contact_store(Request $request)
{
    $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|email|max:255',
        'phone' => 'required|string|max:255',
        'message' => 'required|string',
    ]);

    
    Contact::create([
        'name' => $request->name,
        'email' => $request->email,
        'phone' => $request->phone,
        'message' => $request->message,
    ]);

    return redirect()->route('contact')->with('success', 'Your message has been sent successfully.');
     }
}