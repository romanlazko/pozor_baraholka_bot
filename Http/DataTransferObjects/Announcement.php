<?php

namespace App\Bots\pozor_baraholka_bot\Http\DataTransferObjects;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use Romanlazko\Telegram\App\Config;

class Announcement
{
    public function __construct(
        public ?int $id = null,
        public ?int $chat = null,
        public ?string $city = null,
        public ?string $type = null,
        public ?string $title = null,
        public ?string $caption = null,
        public ?int $cost = null,
        public ?string $category = null,
        public ?string $condition = null,
        public ?int $views = null,
        public ?string $status = null,
        public Collection|array|null $photos = null,
        public ?Carbon $created_at = null,
        public ?Carbon $updated_at = null,
    )
    {

    }

    public static function fromObject($data): Announcement
    {
        $photos = null;

        if (isset($data->photos)){
            if ($data->photos instanceof Collection) {
                $photos = $data->photos->pluck('file_id')->take(9);
            } elseif (is_array($data->photos)) {
                $photos = collect($data->photos)->map(function ($photo) {
                    return ['file_id' => $photo];
                })->pluck('file_id')->take(9);
            }
        }

        return new self(
            id: $data->id ?? null,
            chat: $data->chat ?? null,
            city: $data->city ?? null,
            type: $data->type ?? null,
            title: $data->title ?? null,
            caption: $data->caption ?? null,
            cost: $data->cost ?? null,
            category: $data->category ?? null,
            condition: $data->condition ?? null,
            views: $data->views ?? null,
            status: $data->status ?? null,
            photos: $photos,
            created_at: $data->created_at ?? null,
            updated_at: $data->updated_at ?? null,
        );
    }
    
    public function prepare() {
        $categoryArr = [
            'clothes'       => '#одежда',
            'accessories'   => '#аксессуары',
            'for_home'      => '#для_дома',
            'electronics'   => '#электроника',
            'sport'         => '#спорт',
            'furniture'     => '#мебель',
            'books'         => '#книги',
            'games'         => '#игры',
            'auto'          => '#авто_мото',
            'property'      => '#недвижимость',
            'animals'       => '#животные',
            'other'         => '#прочее',
        ];

        $text = [];

        if ($this->type) {
            $text[] = $this->type === 'buy' ? '#куплю' : '#продам';
        }

        if ($this->title) {
            $text[] = "<b>{$this->title}</b>";
        }

        if ($this->caption) {
            $text[] = $this->caption;
        }

        if ($this->condition) {
            $text[] = "<i>Состояние:</i> " . ($this->condition === 'new' ? 'Новое' : 'Б/у');
        }

        if ($this->cost) {
            $text[] = "<i>Стоимость:</i> {$this->cost}";
        }

        if ($this->id) {
            $text[] = "<a href='https://t.me/". Config::get('bot_username') ."?start=announcement={$this->id}'>🔗Контакт</a> (<i>Перейди по ссылке и нажми <b>Начать</b></i>)";
        }

        if ($this->category && isset($categoryArr[$this->category])) {
            $text[] = $categoryArr[$this->category];
        }

        return implode("\n\n", $text);
    }
}
