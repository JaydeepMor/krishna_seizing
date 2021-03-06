<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\FinanceCompany;

class VehicleImportComplete extends Notification
{
    use Queueable;

    protected $financeCompanyId;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(int $financeCompanyId)
    {
        $this->financeCompanyId = $financeCompanyId;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        // Get finance company.
        $financeCompany = FinanceCompany::find($this->financeCompanyId);

        return (new MailMessage)
                    ->subject('Vehicles Imported Successfully for : ' . $financeCompany->name)
                    ->action('Click Here to See', route('vehicle.index', ['finance_company_id' => $this->financeCompanyId]));
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
