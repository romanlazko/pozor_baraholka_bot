<?php 

namespace App\Bots\pozor_baraholka_bot\Commands\AdminCommands;

use Romanlazko\Telegram\App\BotApi;
use Romanlazko\Telegram\App\Commands\Command;
use Romanlazko\Telegram\App\Entities\Response;
use Romanlazko\Telegram\App\Entities\Update;

class RejectAnnouncement extends Command
{
    public static $command = 'reject';

    public static $title = [
        'ru' => 'Отклонить',
        'en' => 'Reject',
    ];

    public static $usage = ['reject'];

    protected $enabled = true;

    public function execute(Update $updates): Response
    {
        $buttons = BotApi::inlineKeyboard([
            [array(ConfirmReject::getTitle('ru'), ConfirmReject::$command, '')],
            [
                array('👈 Назад', ShowAnnouncement::$command, ''),
                array(MenuCommand::getTitle('ru'), MenuCommand::$command, '')
            ]
        ]);
        return BotApi::returnInline([
            'chat_id'       => $updates->getChat()->getId(),
            'text'          => "Вы уверены что хотите отклонить объявление?", 
            'parse_mode'    => 'HTML',
            'message_id'    => $updates->getCallbackQuery()?->getMessage()->getMessageId(),
            'reply_markup'  => $buttons
        ]);
    }
}
