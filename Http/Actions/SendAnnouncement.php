<?php 

namespace App\Bots\pozor_baraholka_bot\Http\Actions;

use App\Bots\pozor_baraholka_bot\Http\DataTransferObjects\AnnouncementDTO;
use App\Bots\pozor_baraholka_bot\Models\BaraholkaAnnouncement;
use Romanlazko\Telegram\App\Telegram;

class SendAnnouncement
{
    public function __invoke(Telegram $telegram, BaraholkaAnnouncement $announcement, int $chat_id)
    {
        $photos = $announcement->photos->pluck('file_id')->take(9);

        return $telegram::sendMessageWithMedia([
            'text'                      => $announcement->prepare(),
            'chat_id'                   => $chat_id,
            'media'                     => $photos ?? null,
            'parse_mode'                => 'HTML',
            'reply_markup'              => $buttons ?? null,
        ]);
    }
}
