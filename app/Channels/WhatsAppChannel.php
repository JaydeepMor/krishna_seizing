<?php

namespace App\Channels;

use Illuminate\Notifications\Notification;
use Twilio\Rest\Client;
use Twilio\Rest\Api\V2010\Account\MessageInstance;
use App\User;
use App\WhatsappMessage;

class WhatsAppChannel
{
    public function send($notifiable, Notification $notification)
    {
        $message = $notification->toWhatsApp($notifiable);
        $to      = $notifiable->routeNotificationFor('WhatsApp');
        $from    = config('services.twilio.whatsapp_from');


        $twilio = new Client(config('services.twilio.sid'), config('services.twilio.token'));

        $create = $twilio->messages->create('whatsapp:' . $to, [
            "from" => 'whatsapp:' . $from,
            "body" => $message->content
        ]);

        if ($create) {
            $this->storeWhatsappMessage($notifiable, $create);
        }

        return $create;
    }

    private function storeWhatsappMessage(User $notifiable, MessageInstance $messageInstance)
    {
        $model      = new WhatsappMessage();

        $data       = [
            'sid'     => $messageInstance->sid,
            'from'    => $messageInstance->from,
            'to'      => $messageInstance->to,
            'body'    => $messageInstance->body,
            'user_id' => $notifiable->id
        ];

        $validator  = $model->validator($data);

        if (!$validator->fails()) {
            $model::create($data);
        } else {
            \Log::emergency('Something went wrong while saving whatsapp message! Data : ' . json_encode($data));
        }
    }
}
