<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use App\Models\Order;

class OrderStatusChanged extends Notification implements ShouldQueue
{
    use Queueable;

    protected $order;
    protected $oldStatus;
    protected $newStatus;

    public function __construct(Order $order, $oldStatus, $newStatus)
    {
        $this->order = $order;
        $this->oldStatus = $oldStatus;
        $this->newStatus = $newStatus;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Your Order Status Has Changed')
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line('The status of your order #' . $this->order->id . ' has changed.')
            ->line('Previous status: ' . ucfirst($this->oldStatus))
            ->line('New status: ' . ucfirst($this->newStatus))
            ->action('View Order', url('/user/orders'))
            ->line('Thank you for shopping with us!');
    }
}
