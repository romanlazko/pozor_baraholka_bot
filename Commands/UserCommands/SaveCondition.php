<?php 

namespace App\Bots\pozor_baraholka_bot\Commands\UserCommands;

use Romanlazko\Telegram\App\Commands\Command;
use Romanlazko\Telegram\App\Entities\Response;
use Romanlazko\Telegram\App\Entities\Update;

class SaveCondition extends Command
{
    public static $command = 'save_condition';

    public static $usage = ['save_condition'];

    protected $enabled = true;

    public function execute(Update $updates): Response
    {
        $this->getConversation()->update([
            'condition' => $updates->getInlineData()->getCondition(),
        ]);
            
        return $this->bot->executeCommand(Caption::$command);
    }
}
