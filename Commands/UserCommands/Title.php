<?php 

namespace App\Bots\pozor_baraholka_bot\Commands\UserCommands;

use Romanlazko\Telegram\App\BotApi;
use Romanlazko\Telegram\App\Commands\Command;
use Romanlazko\Telegram\App\Entities\Response;
use Romanlazko\Telegram\App\Entities\Update;

class Title extends Command
{
    public static $command = 'title';

    public static $title = '';

    public static $usage = ['title'];

    protected $enabled = true;

    public function execute(Update $updates): Response
    {
        $trade   = $this->getConversation()->notes['type'] === 'buy' 
            ? 'купить' 
            : 'продать';

        $updates->getFrom()->setExpectation(AwaitTitle::$expectation);

        $buttons = BotApi::inlineKeyboard([
            [
                array("👈 Назад", Photo::$command, ''),
                array(MenuCommand::getTitle('ru'), MenuCommand::$command, '')
            ]
        ]);

        $data = [
            'text'          => "Напиши *заголовок* к товару, который ты хочешь *{$trade}*."."\n\n"."_Максимально_ *30* _символов, без эмодзи_.",
            'chat_id'       => $updates->getChat()->getId(),
            'message_id'    => $updates->getCallbackQuery()?->getMessage()?->getMessageId(),
            'parse_mode'    => "Markdown",
            'reply_markup'  => $buttons
        ];

        return BotApi::returnInline($data);
    }




}
