<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
   
{
    function index()
    {
        return view('user.index');
    }

    public function login(Request $request)
    {
        if ($request->isMethod('post')) {
            $credentials = $request->only('email', 'password');
            if (Auth::attempt($credentials)) {
                $request->session()->regenerate();
                return redirect()->intended('/');
            }
            return back()->withErrors([
                'email' => 'The provided credentials do not match our records.',
            ]);
        }
        return view('user.login');
    }
    
   public function orders()
{
    $orders = Order::where('user_id', Auth::user()->id  )
        ->orderBy('created_at', 'desc')
        ->paginate(10);
    return view('orders', compact('orders'));
   
}
public function slider()
{
    $sliders = Slider::latest()->paginate(10); // or ->get()
    return view('index', compact('sliders'));
}
 public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('user.login');
    }
}
