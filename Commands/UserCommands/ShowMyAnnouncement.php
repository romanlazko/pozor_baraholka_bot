<?php 

namespace App\Bots\pozor_baraholka_bot\Commands\UserCommands;

use App\Bots\pozor_baraholka_bot\Http\DataTransferObjects\Announcement;
use Romanlazko\Telegram\App\BotApi;
use App\Bots\pozor_baraholka_bot\Models\BaraholkaAnnouncement;
use Romanlazko\Telegram\App\Commands\Command;
use Romanlazko\Telegram\App\Entities\Response;
use Romanlazko\Telegram\App\Entities\Update;
use Romanlazko\Telegram\Exceptions\TelegramException;
use Romanlazko\Telegram\Exceptions\TelegramUserException;

class ShowMyAnnouncement extends Command
{
    public static $command = 'show_announcement';

    public static $title = '';

    public static $usage = ['show_announcement'];

    protected $enabled = true;

    public function execute(Update $updates): Response
    {
        $announcement = BaraholkaAnnouncement::findOr($updates->getInlineData()?->getAnnouncementId(), function () {
            throw new TelegramUserException("Объявление не найдено");
        });

        if ($announcement->status !== 'published' OR $announcement->status !== 'new') {
            throw new TelegramUserException("Объявление уже не актуально.");
        }

        try {
            BotApi::sendMessageWithMedia([
                'text'                      => $announcement->prepare(),
                'chat_id'                   => $updates->getChat()->getId(),
                'media'                     => $announcement->dto()->photos ?? null,
                'parse_mode'                => 'HTML',
                'disable_web_page_preview'  => 'true',
            ]);

            return $this->sendConfirmMessage($updates, $announcement);
        }
        catch (TelegramException $exception) {
            throw new TelegramUserException("Ошибка публикации: {$exception->getMessage()}");
        }
    }
    
    private function sendConfirmMessage(Update $updates, BaraholkaAnnouncement $announcement): Response
    {
        $buttons = BotApi::inlineKeyboard([
            $announcement->status === 'published' ? [array(SoldAnnouncement::getTitle('ru'), SoldAnnouncement::$command, $announcement->id)] : [],
            [array(IrrelevantAnnouncement::getTitle('ru'), IrrelevantAnnouncement::$command, $announcement->id)],
            [array(MenuCommand::getTitle('ru'), MenuCommand::$command, '')]
        ], 'announcement_id');

        return BotApi::sendMessage([
            'text'          => "Дата публикации: *{$announcement->created_at->format('d.m.Y')}*", 
            'chat_id'       => $updates->getChat()->getId(),
            'parse_mode'    => 'Markdown',
            'reply_markup'  => $buttons
        ]);
    }
}
