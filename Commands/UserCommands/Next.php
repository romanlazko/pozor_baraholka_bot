<?php 

namespace App\Bots\pozor_baraholka_bot\Commands\UserCommands;

use Romanlazko\Telegram\App\Commands\Command;
use Romanlazko\Telegram\App\Entities\Response;
use Romanlazko\Telegram\App\Entities\Update;

class Next extends Command
{
    public static $command = 'next';

    public static $title = '';

    public static $usage = ['next'];

    protected $enabled = true;

    public function execute(Update $updates): Response
    {
        $next = $this->getConversation()->notes['next'];
        
        return $this->bot->executeCommand($next);

    }
}
