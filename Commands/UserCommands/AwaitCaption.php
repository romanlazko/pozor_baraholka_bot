<?php 

namespace App\Bots\pozor_baraholka_bot\Commands\UserCommands;

use Romanlazko\Telegram\App\Commands\Command;
use Romanlazko\Telegram\App\Entities\Response;
use Romanlazko\Telegram\App\Entities\Update;

class AwaitCaption extends Command
{
    public static $expectation = 'await_caption';

    public static $pattern = '/^await_caption$/';

    protected $enabled = true;

    public function execute(Update $updates): Response
    {
        $text = $updates->getMessage()?->getText();

        if ($text === null) {
            $this->handleError("*Пришлите пожалуйста описание в виде текста.*");
            return $this->bot->executeCommand(Caption::$command);
        }

        if (iconv_strlen($text) > 800){
            $this->handleError("*Слишком много символов*");
            return $this->bot->executeCommand(Caption::$command);
        }

        $this->getConversation()->update([
            'caption' => $text,
        ]);
        
        return $this->bot->executeCommand(ShowAnnouncement::$command);
    }




}
