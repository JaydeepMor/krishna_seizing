<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Channels\Messages\WhatsAppMessage;
use App\Channels\WhatsAppChannel;
use App\Vehicle;

class VehicleCancelled extends Notification
{
    use Queueable;

    public $vehicle;

    private $messageBody;

    public function __construct(Vehicle $vehicle)
    {
        $this->vehicle = $vehicle;
    }

    public function via($notifiable)
    {
        return [WhatsAppChannel::class];
    }

    public function toWhatsApp($notifiable)
    {
        $this->setMessageBody();

        return (new WhatsAppMessage)->content($this->messageBody);
    }

    private function setMessageBody():void
    {
        $this->messageBody = __("CANCELLED!" . "\n\n" . 
                                "Vehicle Number : " . $this->vehicle->registration_number . "\n" . 
                                "Chassis Number : " . $this->vehicle->chassis_number . "\n" . 
                                "Vehicle Maker : " . $this->vehicle->model . "\n" . 
                                "Engine Number : " . $this->vehicle->engine_number . "\n" . 
                                "Customer Name : " . $this->vehicle->customer_name . "\n" . 
                                "Agency : " . env('APP_NAME', 'V.R. Boricha Service') . "\n" . 
                                "Agency Contact : " . env('AGENCY_CONTACT', ''));
    }
}
