<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ImportCompleted extends Notification
{
    use Queueable;

    protected $dataType;

    /**
     * Create a new notification instance.
     */
    public function __construct(string $dataType)
    {
        $this->dataType = $dataType;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
                    ->subject($this->dataType . ' Import Completed')
                    ->line('Your import of ' . $this->dataType . ' data has been completed.')
                    ->line('You can now view the imported data in the system.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'title' => $this->dataType . ' Import Completed',
            'message' => 'Your import of ' . $this->dataType . ' data has been completed.',
            'type' => 'import_completed',
        ];
    }
}