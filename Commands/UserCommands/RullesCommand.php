<?php 

namespace App\Bots\pozor_baraholka_bot\Commands\UserCommands;

use Romanlazko\Telegram\App\BotApi;
use Romanlazko\Telegram\App\Commands\Command;
use Romanlazko\Telegram\App\Entities\Response;
use Romanlazko\Telegram\App\Entities\Update;

class RullesCommand extends Command
{
    public static $command = 'rulles';

    public static $title = [
        'ru' => 'Правила',
        'en' => 'Rulles',
    ];

    public static $usage = ['rulles'];

    protected $enabled = true;

    public function execute(Update $updates): Response
    {
        $buttons = BotApi::inlineKeyboard([
            [array(MenuCommand::getTitle('ru'), MenuCommand::$command, '')]
        ]);

        $data = [
            'text'          =>  "C правилами можно ознакомиться [тут](https://telegra.ph/Pravila-pablika-Baraholka-03-21).",
            'chat_id'       =>  $updates->getChat()->getId(),
            'reply_markup'  =>  $buttons,
            'parse_mode'    =>  'Markdown',
            'message_id'    =>  $updates->getCallbackQuery()?->getMessage()?->getMessageId(),
        ];

        return BotApi::editMessageText($data);
    }
}
