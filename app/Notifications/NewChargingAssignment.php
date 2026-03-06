<?php

namespace App\Notifications;

use App\Models\Booking;
use App\Models\ChargingAssignment;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewChargingAssignment extends Notification
{
    use Queueable;

    public $booking;
    public $assignment;

    public function __construct(Booking $booking, ChargingAssignment $assignment)
    {
        $this->booking = $booking;
        $this->assignment = $assignment;
    }

    public function via($notifiable)
    {
        return ['database', 'mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->line('New charging assignment available!')
            ->line("Booking: {$this->booking->booking_reference}")
            ->line("Customer: {$this->booking->customer_name}")
            ->line("Amount: ${$this->booking->amount_charged}")
            ->action('Accept Assignment', url("/charging/accept/{$this->assignment->id}"))
            ->line('First to accept will handle this booking.');
    }

    public function toArray($notifiable)
    {
        return [
            'booking_id' => $this->booking->id,
            'assignment_id' => $this->assignment->id,
            'message' => "New charging assignment: {$this->booking->booking_reference}",
            'url' => url("/charging/accept/{$this->assignment->id}"),
        ];
    }
}
