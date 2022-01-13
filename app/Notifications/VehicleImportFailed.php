<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\FinanceCompany;

class VehicleImportFailed extends Notification
{
    use Queueable;

    private $exceptionContent;

    protected $financeCompanyId;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(int $financeCompanyId, $exceptionContent = null)
    {
        $this->financeCompanyId = $financeCompanyId;

        $this->exceptionContent = $exceptionContent;
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
                    ->subject(__('URGENT KRISHNA SEIZING!! Vehicles Not Imported Successfully for : ' . $financeCompany->name))
                    ->line(__($this->exceptionContent))
                    ->action(__('Click Here to See'), route('vehicle.index', ['finance_company_id' => $this->financeCompanyId]));
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
