<?php 

namespace App\Bots\pozor_baraholka_bot\Commands\UserCommands;

use Romanlazko\Telegram\App\BotApi;
use App\Bots\pozor_baraholka_bot\Models\BaraholkaAnnouncement;
use Romanlazko\Telegram\App\Commands\Command;
use Romanlazko\Telegram\App\Entities\Response;
use Romanlazko\Telegram\App\Entities\Update;
use Romanlazko\Telegram\Exceptions\TelegramUserException;

class SoldAnnouncement extends Command
{
    public static $command = 'sold';

    public static $title = [
        'ru' => 'Продано',
        'en' => 'Sold'
    ];

    public static $usage = ['sold'];

    protected $enabled = true;

    public function execute(Update $updates): Response
    {
        $announcement = BaraholkaAnnouncement::findOr($updates->getInlineData()?->getAnnouncementId(), function () {
            throw new TelegramUserException("Объявление не найдено");
        });

        if ($announcement->status !== 'published') {
            throw new TelegramUserException("Объявление уже не актуально.");
        }

        $announcement->update([
            'status' => 'sold'
        ]);

        BotApi::answerCallbackQuery([
            'callback_query_id' => $updates->getCallbackQuery()->getId(),
            'text'              => "Вам больше не смогут писать по поводу этого объявления",
            'show_alert'        => true
        ]);

        return $this->bot->executeCommand(MenuCommand::$command);
    }
}
