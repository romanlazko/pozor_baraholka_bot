<?php 

namespace App\Bots\pozor_baraholka_bot\Commands\UserCommands;

use App\Bots\pozor_baraholka_bot\Models\BaraholkaAnnouncement;
use Romanlazko\Telegram\App\Commands\Command;
use Romanlazko\Telegram\App\DB;
use Romanlazko\Telegram\App\Entities\Response;
use Romanlazko\Telegram\App\Entities\Update;

class SaveAnnouncement extends Command
{
    public static $command = 'save_announcement';

    public static $usage = ['save_announcement'];

    protected $enabled = true;

    public function execute(Update $updates): Response
    {
        $notes   = $this->getConversation()->notes;

        $announcement = BaraholkaAnnouncement::updateOrCreate([
            'chat'          => DB::getChat($updates->getChat()->getId())->id,
            'city'          => $notes['city'] ?? null,
            'type'          => $notes['type'] ?? null,
            'title'         => $notes['title'] ?? null,
            'caption'       => $notes['caption'] ?? null,
            'cost'          => $notes['cost'] ?? null,
            'condition'     => $notes['condition'] ?? null,
            'category'      => $notes['category'] ?? null,
            'status'        => 'new',
        ]);

        if (array_key_exists('photos', $notes)) {
            foreach ($notes['photos'] as $id => $file_id) {
                $announcement->photos()->updateOrCreate([
                    'file_id' => $file_id,
                ]);
            }
        }
        
        return $this->bot->executeCommand(Published::$command);
    }
}