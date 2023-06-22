<?php

namespace App\Bots\pozor_baraholka_bot\Listeners;

use App\Bots\pozor_baraholka_bot\Commands\UserCommands\MenuCommand;
use App\Bots\pozor_baraholka_bot\Commands\UserCommands\ShowMyAnnouncement;
use App\Bots\pozor_baraholka_bot\Events\AnnouncementPublished;

class SendToUserAnnouncementPublishedNotification
{

    /**
     * Create the event listener.
     */
    public function __construct()
    {
    }

    /**
     * Handle the event.
     */
    public function handle(AnnouncementPublished $event): void
    {
        $announcement = $event->announcement;
        $telegram = $event->telegram;

        $buttons = $telegram::inlineKeyboard([
            [array($announcement->title ?? $announcement->caption, ShowMyAnnouncement::$command, $announcement->id)],
            [array(MenuCommand::getTitle('ru'), MenuCommand::$command, '')]
        ], 'announcement_id');

        $telegram::sendMessage([
            'chat_id'       => $announcement->chat()->first()->chat_id,
            'text'          => "Ваше объявление опубликовано",
            'reply_markup'  => $buttons,
            'parse_mode'    => 'HTML',
        ]);
    }
}
