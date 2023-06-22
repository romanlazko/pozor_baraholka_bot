<?php 

namespace App\Bots\pozor_baraholka_bot\Commands\UserCommands;

use Romanlazko\Telegram\App\BotApi;
use Romanlazko\Telegram\App\Commands\Command;
use Romanlazko\Telegram\App\Entities\Response;
use Romanlazko\Telegram\App\Entities\Update;

class AwaitPhoto extends Command
{
    public static $expectation = 'photo|1';

    public static $pattern = '/photo\|\d/';

    protected $enabled = true;

    public function execute(Update $updates): Response
    {
        return $this->savePhoto(function(int $photoCount) use($updates){
            
            $buttons = BotApi::inlineKeyboard([
                [array('Готово', Next::$command, '')],
                [array(MenuCommand::getTitle('ru'), MenuCommand::$command, '')]
            ]);
        
            $data = [
                'text'          =>  "*Фото {$photoCount}/9 сохранено*"."\n\n".
                                    "_Как только все фото будут сохранены, нажми_ *'Готово'*.",

                'chat_id'       =>  $updates->getChat()->getId(),
                'reply_markup'  =>  $buttons,
                'parse_mode'    =>  "Markdown",
                'message_id'    =>  $updates->getChat()->getLastMessageId()
            ];
        
            return $photoCount > 1 
                ? BotApi::editMessageText($data) 
                : BotApi::sendMessage($data);
        });
    }

    public function savePhoto(\Closure $action): Response
    {
        $user           = $this->updates->getFrom();
        $conversation   = $this->getConversation();

        list($expectationType, $expectationValue) = explode('|', $user->getExpectation());
        
        if (!$photo = $this->updates->getMessage()?->getPhoto()) {
            $conversation->unsetNote($expectationType);
            $this->handleError("Один из присланных файлов не является фотографией.");
            return $this->bot->executeCommand(Photo::$command);
        }

        if ($expectationValue > 9) {
            $conversation->unsetNote($expectationType);
            $this->handleError("Слишком много фотографий.");
            return $this->bot->executeCommand(Photo::$command);
        }

        $conversation->notes['photos'][$expectationValue] = $photo;
        $conversation->update();

        $user->setExpectation($expectationType . '|' . ($expectationValue + 1));
    
        return $action($expectationValue);
    }
}
