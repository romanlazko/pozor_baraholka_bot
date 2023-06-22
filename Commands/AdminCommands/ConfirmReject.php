<?php 

namespace App\Bots\pozor_baraholka_bot\Commands\AdminCommands;

use App\Bots\pozor_baraholka_bot\Events\AnnouncementRejected;
use App\Bots\pozor_baraholka_bot\Models\BaraholkaAnnouncement;
use Romanlazko\Telegram\App\BotApi;
use Romanlazko\Telegram\App\Commands\Command;
use Romanlazko\Telegram\App\Entities\Response;
use Romanlazko\Telegram\App\Entities\Update;
use Romanlazko\Telegram\Exceptions\TelegramException;
use Romanlazko\Telegram\Exceptions\TelegramUserException;

class ConfirmReject extends Command
{
    public static $command = 'confirm_reject';

    public static $title = [
        'ru' => 'Отклонить',
        'en' => 'Reject',
    ];

    public static $usage = ['confirm_reject'];

    protected $enabled = true;

    public function execute(Update $updates): Response
    {
        $announcement = BaraholkaAnnouncement::findOr($updates->getInlineData()?->getAnnouncementId(), function () {
            throw new TelegramUserException("Объявление не найдено");
        });

        if ($announcement->status !== 'new') {
            throw new TelegramUserException("Объявление уже обработано");
        }

        $announcement->update([
            'status' => 'rejected'
        ]);
        
        event(new AnnouncementRejected($announcement, $this->bot));

        return $this->bot->executeCommand(MenuCommand::$command);
    }
}
