<?php

namespace App\Listeners;


use App\Events\ProductAdded;
use App\Mail\NewProductNotification;
use App\Models\User;
use App\Models\ProductUserNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;


class SendProductAddedNotification implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Handle the event.
     */
    public function handle(ProductAdded $event): void
    {
        $users = User::all();
        foreach ($users as $user) {
            $alreadyNotified = ProductUserNotification::where('user_id', $user->id)
                ->where('product_id', $event->product->id)
                ->exists();
            if ($alreadyNotified) {
                continue;
            }
            if (filter_var($user->email, FILTER_VALIDATE_EMAIL)) {
                Mail::to($user->email)->queue(new NewProductNotification($event->product));
                ProductUserNotification::create([
                    'user_id' => $user->id,
                    'product_id' => $event->product->id,
                ]);
            } else {
                Log::warning('Product notification not sent: invalid or missing email address.', [
                    'user_id' => $user->id,
                    'email' => $user->email,
                ]);
            }
        }
    }
}
