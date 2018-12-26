<?php

namespace App\Notifications;

use Illuminate\Support\Facades\Input;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class AnalystDiscussionNotifications extends Notification {

    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($from, $to, $message, $category, $latest_id, $type) {

        $this->from = $from;
        $this->to = $to;
        $this->message = $message;
        $this->category = $category;
        $this->latest_id = $latest_id;
        $this->type = $type;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable) {
        return ['database'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable) {
        return (new MailMessage)
                        ->line('The introduction to the notification.')
                        ->action('Notification Action', 'https://laravel.com')
                        ->line('Thank you for using our application!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable) {
        return [
            'to' => $this->to,
            'from' => $this->from,
            'message' => $this->message,
            'category' => $this->category,
            'notifiable_id' => $this->latest_id,
            'type' => $this->type,
        ];
    }

}
