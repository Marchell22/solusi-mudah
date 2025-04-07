<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ExportCompleted extends Notification
{
    use Queueable;

    protected $dataType;
    protected $downloadUrl;

    /**
     * Create a new notification instance.
     */
    public function __construct(string $dataType, string $downloadUrl)
    {
        $this->dataType = $dataType;
        $this->downloadUrl = $downloadUrl;
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
                    ->subject($this->dataType . ' Export Completed')
                    ->line('Your export of ' . $this->dataType . ' data has been completed.')
                    ->action('Download File', url($this->downloadUrl))
                    ->line('The file will be available for download for 7 days.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'title' => $this->dataType . ' Export Completed',
            'message' => 'Your export of ' . $this->dataType . ' data has been completed.',
            'download_url' => $this->downloadUrl,
            'type' => 'export_completed',
        ];
    }
}
