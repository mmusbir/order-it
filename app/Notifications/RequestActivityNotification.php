<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class RequestActivityNotification extends Notification
{
    use Queueable;

    protected $request;
    protected $oldStatus;
    protected $newStatus;
    protected $action; // 'created', 'updated', 'approved', 'rejected'

    /**
     * Create a new notification instance.
     */
    public function __construct($request, $oldStatus, $newStatus, $action = 'updated')
    {
        $this->request = $request;
        $this->oldStatus = $oldStatus;
        $this->newStatus = $newStatus;
        $this->action = $action;
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
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'request_id' => $this->request->id,
            'ticket_no' => $this->request->ticket_no,
            'old_status' => $this->oldStatus,
            'new_status' => $this->newStatus,
            'action' => $this->action,
            'message' => "Request {$this->request->ticket_no} has been {$this->action}.",
            'link' => route('requests.show', $this->request->id),
        ];
    }
}
