<?php

namespace App\Observers;

use App\Models\Product;
use App\Models\User;
use App\Notifications\NewProductNotification;
use Illuminate\Support\Facades\Notification;

class ProductObserver
{
    public function created(Product $product)
    {
        $users = User::all();
        Notification::send($users, new NewProductNotification($product));
    }
}
