<?php
namespace App\Notifications;

use App\Models\Assignment;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class DriverAssignmentNotification extends Notification
{
    use Queueable;

    protected Assignment $assignment;
    protected string $message;

    public function __construct(Assignment $assignment, string $message)
    {
        $this->assignment = $assignment;
        $this->message = $message;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        $vehicle = $this->assignment->vehicle;
        $shift = $this->assignment->shiftTemplate;

        return [
            'assignment_id' => $this->assignment->id,
            'vehicle' => $vehicle?->car_model,
            'plate_number' => $vehicle?->plate_number,
            'shift' => $shift?->name,
            'shift_start' => $shift?->start_time,
            'shift_end' => $shift?->end_time,
            'days_of_week' => $shift?->days_of_week,
            'receiver_role' => 'driver',
            'message' => $this->message,
        ];
    }
}