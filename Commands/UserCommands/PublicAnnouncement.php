<?php 

namespace App\Bots\pozor_baraholka_bot\Commands\UserCommands;

use Romanlazko\Telegram\App\BotApi;
use Romanlazko\Telegram\App\Commands\Command;
use Romanlazko\Telegram\App\Entities\Response;
use Romanlazko\Telegram\App\Entities\Update;
use Romanlazko\Telegram\Exceptions\TelegramException;

class PublicAnnouncement extends Command
{
    public static $command = 'public';

    public static $title = '';

    public static $usage = ['public'];

    protected $enabled = true;

    public function execute(Update $updates): Response
    {
        $chat                   = $updates->getChat();
        $chatInfo               = BotApi::getChat(['chat_id' => $chat->getId()]);
        $hasPrivateForwards     = $chatInfo->getResult()->getHasPrivateForwards();

        if($hasPrivateForwards AND $hasPrivateForwards === true){
            try {
                $buttons = BotApi::inlineKeyboard([
                    [array('Продолжить', PublicAnnouncement::$command, '')],
                ]);

                $text = implode("\n", [
                    "*Ой ой*"."\n",
                    "Мы не можем опубликовать объявление поскольку твои настройки конфиденциальности не позволяют нам сослаться на тебя."."\n",
                    "Сделай все как указанно в [инструкции](https://telegra.ph/Kak-razreshit-peresylku-soobshchenij-12-27) что бы исправить это."."\n",
                    "*Настройки конфиденциальности вступят в силу в течении 5-ти минут, после этого нажми на кнопку «Продолжить»*",
                ]);
    
                return BotApi::editMessageText([
                    'text'          => $text,
                    'reply_markup'  => $buttons,
                    'chat_id'       => $updates->getChat()->getId(),
                    'message_id'    => $updates->getCallbackQuery()->getMessage()->getMessageId(),
                    'parse_mode'    => "Markdown",
                ]);
            }
            catch(TelegramException $e){
                return BotApi::answerCallbackQuery([
                    'callback_query_id' => $updates->getCallbackQuery()->getId(),
                    'text'              => "Настройки еще не вступили в силу. Подождите 5 минут после изменения настроек и попробуйте еще раз.",
                    'show_alert'        => true
                ]);
            }
        }

        return $this->bot->executeCommand(SaveAnnouncement::$command);
    }
}
