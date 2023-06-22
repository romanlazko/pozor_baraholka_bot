<?php

namespace App\Bots\pozor_baraholka_bot;


class Config
{
    public static function getConfig()
    {
        return [
            'inline_data'       => [
                'city'              => null,
                'type'              => null,
                'next'              => null,
                'condition'         => null,
                'category'          => null,
                'announcement_id'   => null,
            ],
            'brno'      => '@pozor_test',
            'prague'    => '@pozor_test',
            'admin_ids'         => [
                
            ],
            'bot_username' => 'pozorbottestbot'
        ];
    }

    
}
