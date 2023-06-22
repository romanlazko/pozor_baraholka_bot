<?php 

namespace App\Bots\pozor_baraholka_bot\Commands\UserCommands;

use Romanlazko\Telegram\App\BotApi;
use Romanlazko\Telegram\App\Commands\Command;
use Romanlazko\Telegram\App\Entities\Response;
use Romanlazko\Telegram\App\Entities\Update;

class Count extends Command
{
    public static $command = 'product_count';

    public static $title = '';

    public static $usage = ['product_count'];

    protected $enabled = true;

    public function execute(Update $updates): Response
    {
        $this->getConversation()->update([
            'type' => $updates->getInlineData()->getType(),
        ]);
            
        $buttons = BotApi::inlineKeyboard([
            [
                array('Один', Photo::$command, 'title'),
                array('Несколько', Photo::$command, 'category')
            ],
            [array(MenuCommand::getTitle('ru'), MenuCommand::$command, '')]
        ], 'next');

        $data = [
            'text'          => "Сколько товаров будет в объявлении?",
            'reply_markup'  => $buttons,
            'chat_id'       => $updates->getChat()->getId(),
            'message_id'    => $updates->getCallbackQuery()?->getMessage()?->getMessageId(),
            'parse_mode'    => "Markdown"
        ];
                
        return BotApi::returnInline($data);
    }
}
