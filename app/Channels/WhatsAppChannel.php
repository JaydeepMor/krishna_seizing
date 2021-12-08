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

        try {
            $create = $twilio->messages->create('whatsapp:' . $to, [
                "from" => 'whatsapp:' . $from,
                "body" => $message->content
            ]);
        } catch (\Exception $e) {
            $create = ['code' => 401, 'msg' => __($e->getMessage())];
        }

        if (!empty($create) && $create instanceof MessageInstance) {
            $this->storeWhatsappMessage($notifiable, $create);

            $create = ['code' => 200, 'msg' => __("Whatsapp message sent successfully!")];
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
