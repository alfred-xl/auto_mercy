<?php

return [
    'currency' => 'NGN',
    'business' => [
        'name' => 'Auto Mercy',
        'legal_name' => 'Auto Mercy of God Nigeria Limited',
        'cac_number' => '7328497',
        'operating_since' => 2023,
        'phone_display' => '08061731673',
        'phone_e164' => '+2348061731673',
        'telephone_url' => 'tel:+2348061731673',
        'whatsapp_url' => 'https://wa.me/2348061731673',
        'email' => 'automercyofgod19@gmail.com',
        'opening_hours_display' => 'Monday–Saturday, 8:00 AM–6:00 PM',
    ],
    'locations' => [
        [
            'name' => 'Iju Road',
            'slug' => 'iju',
            'address' => '9 Moshalashi Alao Street, Oyemukun Bus Stop, Iju Road, Lagos',
            'map_url' => null,
        ],
        [
            'name' => 'Bamboo Plaza',
            'slug' => 'ogunnisi-road',
            'address' => '6/8 Ogunnisi Road, Bamboo Plaza, adjacent Omole Phase 1',
            'map_url' => null,
        ],
    ],
    'reservation' => [
        'amount' => 500_000,
        'duration_days' => 14,
    ],
    'media' => [
        'disk' => env('CAR_MEDIA_DISK', 'public'),
        'image_max_kilobytes' => 15 * 1024,
        'image_max_pixels' => 40_000_000,
        'image_extensions' => ['jpg', 'jpeg', 'png', 'webp'],
        'image_mime_types' => ['image/jpeg', 'image/png', 'image/webp'],
    ],
];
