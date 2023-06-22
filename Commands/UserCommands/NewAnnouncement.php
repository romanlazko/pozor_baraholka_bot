<?php 

namespace App\Bots\pozor_baraholka_bot\Commands\UserCommands;

use Romanlazko\Telegram\App\BotApi;
use Romanlazko\Telegram\App\Commands\Command;
use Romanlazko\Telegram\App\Entities\Response;
use Romanlazko\Telegram\App\Entities\Update;

class NewAnnouncement extends Command
{
    public static $command = 'new_announcement';

    public static $title = [
        'ru' => 'Создать объявление',
        'en' => 'Create announcement',
    ];

    public static $usage = ['new_announcement'];

    protected $enabled = true;

    public function execute(Update $updates): Response
    {
        $this->getConversation()->clear();

        $buttons = BotApi::inlineKeyboard([
            [
                array('Прага', AnnouncementType::$command, 'prague'),
                array('Брно', AnnouncementType::$command, 'brno'),
            ],
            [array(MenuCommand::getTitle('ru'), MenuCommand::$command, '')]
        ], 'city');
                                
        return BotApi::editMessageText([
            'text'          => "В каком *городе* ты хочешь опубликовать объявление?",
            'reply_markup'  => $buttons,
            'chat_id'       => $updates->getChat()->getId(),
            'message_id'    => $updates->getCallbackQuery()?->getMessage()?->getMessageId(),
            'parse_mode'    => "Markdown"
        ]);
    }
}
