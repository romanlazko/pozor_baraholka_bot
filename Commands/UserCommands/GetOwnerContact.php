<?php 

namespace App\Bots\pozor_baraholka_bot\Commands\UserCommands;

use Romanlazko\Telegram\App\BotApi;
use App\Bots\pozor_baraholka_bot\Models\BaraholkaAnnouncement;
use Romanlazko\Telegram\App\Commands\Command;
use Romanlazko\Telegram\App\Commands\UserCommands\AdvertisementCommand;
use Romanlazko\Telegram\App\Entities\Response;
use Romanlazko\Telegram\App\Entities\Update;
use Romanlazko\Telegram\Exceptions\TelegramUserException;

class GetOwnerContact extends Command
{
    public static $command = 'get_owner_contact';

    public static $title = '';

    public static $pattern = "/^(\/start\s)(announcement)=(\d+)$/";

    protected $enabled = true;

    public function execute(Update $updates): Response
    {
        $this->bot->executeCommand(AdvertisementCommand::$command);

        preg_match(static::$pattern, $updates->getMessage()?->getCommand(), $matches);

        $announcement = BaraholkaAnnouncement::findOr($matches[3] ?? null, function () {
            throw new TelegramUserException("Объявление не найдено");
        });

        if ($announcement->status === 'irrelevant') {
            throw new TelegramUserException("Объявление уже не актуально.");
        }

        if ($announcement->status === 'published') {
            $announcement->increment('views');
        }
        
        return $this->sendAnnouncementContact($announcement);
    }

    private function sendAnnouncementContact($announcement)
    {
        $buttons = BotApi::inlineKeyboardWithLink([
            'text'  => "👤 Контакт на автора", 
            'url'   => "tg://user?id={$announcement->chat()->first()->chat_id}"
        ]);

        $text = [
            "<b>Вот контакт на автора объявления:</b>",
            $announcement->prepare()
        ];

        return BotApi::sendMessage([
            'text'          => implode("\n\n", $text),
            'reply_markup'  => $buttons,
            'chat_id'       => $this->updates->getChat()->getId(),
            'parse_mode'    => 'HTML',
            'disable_web_page_preview' => true,
        ]);
    }
}
