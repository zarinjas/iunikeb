<?php

namespace App\Notifications;

use App\Models\Announcement;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AnnouncementPublished extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        private readonly Announcement $announcement,
    ) {}

    public function via(object $notifiable): array
    {
        $channels = ['database'];

        if ($this->announcement->hasEmail()) {
            $channels[] = 'mail';
        }

        return $channels;
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'title' => $this->announcement->title,
            'summary' => $this->announcement->summary ?? 'Pengumuman baharu telah diterbitkan.',
            'url' => '/member/announcements/' . $this->announcement->slug,
            'announcement_id' => $this->announcement->id,
        ];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject($this->announcement->title)
            ->greeting('Salam sejahtera,')
            ->line($this->announcement->summary ?? 'Pengumuman baharu telah diterbitkan.')
            ->action('Lihat Pengumuman', url('/member/announcements/' . $this->announcement->slug))
            ->salutation('Terima kasih.')
            ->line('E-mel ini dijana secara automatik oleh sistem.');
    }
}
