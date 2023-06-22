<?php

namespace App\Bots\pozor_baraholka_bot\Providers;

use App\Bots\pozor_baraholka_bot\Events\AnnouncementPublished;
use App\Bots\pozor_baraholka_bot\Events\AnnouncementRejected;
use App\Bots\pozor_baraholka_bot\Listeners\SendToUserAnnouncementPublishedNotification;
use App\Bots\pozor_baraholka_bot\Listeners\SendToUserAnnouncementRejectedNotification;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        AnnouncementPublished::class => [
            SendToUserAnnouncementPublishedNotification::class,
        ],
        AnnouncementRejected::class => [
            SendToUserAnnouncementRejectedNotification::class,
        ],
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        //
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}
