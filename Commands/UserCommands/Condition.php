<?php 

namespace App\Bots\pozor_baraholka_bot\Commands\UserCommands;

use Romanlazko\Telegram\App\BotApi;
use Romanlazko\Telegram\App\Commands\Command;
use Romanlazko\Telegram\App\Entities\Response;
use Romanlazko\Telegram\App\Entities\Update;

class Condition extends Command
{
    public static $command = 'condition';

    public static $title = '';

    public static $usage = ['condition'];

    protected $enabled = true;

    public function execute(Update $updates): Response
    {
        $buttons = BotApi::inlineKeyboard([
            [
                array('Б/у', SaveCondition::$command, 'used'),
                array('Новое', SaveCondition::$command, 'new')
            ],
            [
                array("👈 Назад", Cost::$command, ''),
                array(MenuCommand::getTitle('ru'), MenuCommand::$command, '')
            ]
        ], 'condition');

        $data = [
            'text'          => "В каком *состоянии* находится товар?",
            'chat_id'       => $updates->getChat()->getId(),
            'parse_mode'    => "Markdown",
            'message_id'    => $updates->getCallbackQuery()?->getMessage()?->getMessageId(),
            'reply_markup'  => $buttons
        ];

        return BotApi::returnInline($data);
    }




}
