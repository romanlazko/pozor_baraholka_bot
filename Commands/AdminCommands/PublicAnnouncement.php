<?php 

namespace App\Bots\pozor_baraholka_bot\Commands\AdminCommands;

use Romanlazko\Telegram\App\BotApi;
use App\Bots\pozor_baraholka_bot\Events\AnnouncementPublished;
use App\Bots\pozor_baraholka_bot\Http\DataTransferObjects\Announcement;
use App\Bots\pozor_baraholka_bot\Models\BaraholkaAnnouncement;
use Romanlazko\Telegram\App\Commands\Command;
use Romanlazko\Telegram\App\Config;
use Romanlazko\Telegram\App\Entities\Response;
use Romanlazko\Telegram\App\Entities\Update;
use Romanlazko\Telegram\Exceptions\TelegramException;
use Romanlazko\Telegram\Exceptions\TelegramUserException;

class PublicAnnouncement extends Command
{
    public static $command = 'public';

    public static $title = [
        'ru' => 'Публикуем',
        'en' => 'Public',
    ];

    public static $usage = ['public'];

    protected $enabled = true;

    public function execute(Update $updates): Response
    {
        $announcement = BaraholkaAnnouncement::findOr($updates->getInlineData()?->getAnnouncementId(), function () {
            throw new TelegramUserException("Объявление не найдено");
        });

        if ($announcement->status !== 'new') {
            throw new TelegramUserException("Объявление уже обработано");
        }

        try {
            $response = BotApi::sendMessageWithMedia([
                'text'                      => $announcement->prepare(),
                'chat_id'                   => Config::get($announcement->city),
                'media'                     => $announcement->dto()->photos ?? null,
                'parse_mode'                => 'HTML',
                'disable_web_page_preview'  => 'true',
            ]);

            if ($response->getOk()) {
                $announcement->update([
                    'status' => 'published'
                ]);
        
                event(new AnnouncementPublished($announcement, $this->bot));
            }
        }
        catch (TelegramException $exception) {
            throw new TelegramUserException("Ошибка публикации: {$exception->getMessage()}");
        }

        return $this->bot->executeCommand(MenuCommand::$command);
    }
}
