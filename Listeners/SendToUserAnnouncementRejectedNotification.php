<?php

namespace App\Bots\pozor_baraholka_bot\Listeners;

use App\Bots\pozor_baraholka_bot\Events\AnnouncementRejected;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendToUserAnnouncementRejectedNotification
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(AnnouncementRejected $event): void
    {
        $announcement = $event->announcement;
        $telegram = $event->telegram;

        $telegram::sendMessage([
            'chat_id'       => $announcement->chat()->first()->chat_id,
            'text'          => "Ваше объявление отклонено", 
            'parse_mode'    => 'HTML',
        ]);
    }
}
