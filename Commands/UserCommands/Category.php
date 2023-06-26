<?php 

namespace App\Bots\pozor_baraholka_bot\Commands\UserCommands;

use Romanlazko\Telegram\App\BotApi;
use Romanlazko\Telegram\App\Commands\Command;
use Romanlazko\Telegram\App\Entities\Response;
use Romanlazko\Telegram\App\Entities\Update;

class Category extends Command
{
    public static $command = 'category';

    public static $title = '';

    public static $usage = ['category'];

    protected $enabled = true;

    public function execute(Update $updates): Response
    {
        $notes = $this->getConversation()->notes;
        $buttons = BotApi::inlineKeyboard([
            [
                array("Одежда", SaveCategory::$command, 'clothes'),
                array("Аксессуары", SaveCategory::$command, 'accessories'),
                array("Для дома", SaveCategory::$command, 'for_home'),
            ],
            [
                array("Электроника", SaveCategory::$command, 'electronics'),
                array("Спорт", SaveCategory::$command, 'sport'),
                array("Мебель", SaveCategory::$command, 'furniture'),
            ],
            [
                array("Книги", SaveCategory::$command, 'books'),
                array("Игры", SaveCategory::$command, 'games'),
                array("Авто-мото", SaveCategory::$command, 'auto'),
            ],
            [
                array("Недвижимость", SaveCategory::$command, 'property'),
                array("Животные", SaveCategory::$command, 'animals'),
                array("Прочее", SaveCategory::$command, 'other'),
            ],
            [
                array("👈 Назад", $notes['next'] === 'title' ? Cost::$command : Photo::$command, ''),
                array(MenuCommand::getTitle('ru'), MenuCommand::$command, '')
            ],
        ], 'category');

        $data = [
            'text'          => "Выбери к какой *категории* относится товар(ы).",
            'chat_id'       => $updates->getChat()->getId(),
            'message_id'    => $updates->getCallbackQuery()?->getMessage()?->getMessageId(),
            'parse_mode'    => "Markdown",
            'reply_markup'  => $buttons
        ];

        return BotApi::returnInline($data);
    }
}
