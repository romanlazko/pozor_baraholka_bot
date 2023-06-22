<?php 

namespace App\Bots\pozor_baraholka_bot\Commands\UserCommands;

use App\Bots\pozor_baraholka_bot\Http\DataTransferObjects\Announcement;
use Romanlazko\Telegram\App\BotApi;
use Romanlazko\Telegram\App\Commands\Command;
use Romanlazko\Telegram\App\DB;
use Romanlazko\Telegram\App\Entities\Response;
use Romanlazko\Telegram\App\Entities\Update;
use Romanlazko\Telegram\Exceptions\TelegramException;
use Romanlazko\Telegram\Exceptions\TelegramUserException;

class ShowAnnouncement extends Command
{
    public static $command = 'show';

    public static $title = '';

    public static $usage = ['show'];

    protected $enabled = true;

    public function execute(Update $updates): Response
    {
        $notes = $this->getConversation()->notes;

        try {
            $announcement = Announcement::fromObject((object) $notes);

            BotApi::sendMessageWithMedia([
                'text'                      => $announcement->prepare(),
                'chat_id'                   => $updates->getChat()->getId(),
                'media'                     => $announcement->photos ?? null,
                'parse_mode'                => 'HTML',
                'disable_web_page_preview'  => 'true',
            ]);
        }
        catch (TelegramException $exception) {
            throw new TelegramUserException("Ошибка публикации: {$exception->getMessage()}");
        }
        
        return $this->sendConfirmMessage($updates);
    }

    private function sendConfirmMessage(Update $updates): Response
    {
        $buttons = BotApi::inlineKeyboard([
            [array('Публикуем', PublicAnnouncement::$command, '')],
            [
                array("👈 Назад", Caption::$command, ''),
                array(MenuCommand::getTitle('ru'), MenuCommand::$command, '')
            ],
        ]);

        return BotApi::sendMessage([
            'text'          => "Так будет выглядеть твое объявление." ."\n\n". "*Публикуем?*", 
            'chat_id'       => $updates->getChat()->getId(),
            'parse_mode'    => 'Markdown',
            'reply_markup'  => $buttons
        ]);
    }
}
