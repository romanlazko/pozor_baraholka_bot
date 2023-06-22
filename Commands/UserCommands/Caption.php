<?php 

namespace App\Bots\pozor_baraholka_bot\Commands\UserCommands;

use Romanlazko\Telegram\App\BotApi;
use Romanlazko\Telegram\App\Commands\Command;
use Romanlazko\Telegram\App\Entities\Response;
use Romanlazko\Telegram\App\Entities\Update;

class Caption extends Command
{
    public static $command = 'caption';

    public static $title = [
        'ru' => "Описание товара",
        'en' => "Caption"
    ];

    public static $usage = ['caption'];

    protected $enabled = true;

    public function execute(Update $updates): Response
    {
        $updates->getFrom()->setExpectation(AwaitCaption::$expectation);

        $buttons = BotApi::inlineKeyboard([
            [
                array("👈 Назад", Category::$command, ''),
                array(MenuCommand::getTitle('ru'), MenuCommand::$command, '')
            ],
        ]);

        $data = [
            'text'          => $this->createText(),
            'chat_id'       => $updates->getChat()->getId(),
            'message_id'    => $updates->getCallbackQuery()?->getMessage()?->getMessageId(),
            'parse_mode'    => "Markdown",
            'reply_markup'  => $buttons
        ];

        return BotApi::returnInline($data);
    }

    private function createText(): string
    {
        $notes  = $this->getConversation()->notes;
        $trade  = $notes['type'] === 'buy' ? 'купить' : 'продать';

        return $notes['next'] === 'title'
            ?   "Напиши *описание* товара, который ты хочешь *{$trade}*.". "\n\n" ."_Максимально_ *800* _символов, без эмодзи_."
            :   "Напиши построчно *описания товаров* на продажу и их стоимость."."\n\n".
                "*Пример*:"."\n". 
                "1) Футболка - 100 крон,"."\n".
                "2) Куртка - 250 крон."."\n\n".
                "_Максимально_ *800* _символов, без эмодзи._";
    }
}
