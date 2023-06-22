<?php 

namespace App\Bots\pozor_baraholka_bot\Commands\UserCommands;

use Romanlazko\Telegram\App\BotApi;
use Romanlazko\Telegram\App\Commands\Command;
use Romanlazko\Telegram\App\Entities\Response;
use Romanlazko\Telegram\App\Entities\Update;

class AnnouncementType extends Command
{
    public static $command = 'type';

    public static $title = '';

    public static $usage = ['type'];

    protected $enabled = true;

    public function execute(Update $updates): Response
    {
        $this->getConversation()->update([
            'city' => $updates->getInlineData()->getCity()
        ]);

        $buttons = BotApi::inlineKeyboard([
            [
                array('Продать', Count::$command, 'sell'),
                array('Купить', Photo::$command, 'buy')
            ],
            [array(MenuCommand::getTitle('ru'), MenuCommand::$command, '')]
        ], 'type');

        $data = [
            'text'          => "Какой *тип* объявления ты хочешь прислать?",
            'reply_markup'  => $buttons,
            'chat_id'       => $updates->getChat()->getId(),
            'message_id'    => $updates->getCallbackQuery()?->getMessage()?->getMessageId(),
            'parse_mode'    => "Markdown"
        ];
                
        return BotApi::returnInline($data);
        
    }
}
