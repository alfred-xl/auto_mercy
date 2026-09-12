<?php

return [
    'currency' => 'NGN',
    'admin' => [
        'name' => env('AUTOMERCY_ADMIN_NAME'),
        'email' => env('AUTOMERCY_ADMIN_EMAIL'),
        'password' => env('AUTOMERCY_ADMIN_PASSWORD'),
    ],
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
    'social' => [
        'instagram' => env('AUTOMERCY_INSTAGRAM_URL'),
        'tiktok' => env('AUTOMERCY_TIKTOK_URL'),
        'facebook' => env('AUTOMERCY_FACEBOOK_URL'),
    ],
    'locations' => [
        [
            'name' => 'Iju Road',
            'slug' => 'iju',
            'address' => '9 Moshalashi Alao Street, Oyemukun Bus Stop, Iju Road, Lagos',
            'map_url' => env('AUTOMERCY_IJU_MAP_URL', 'https://www.google.com/maps/search/?api=1&query=9%20Moshalashi%20Alao%20Street%2C%20Oyemukun%20Bus%20Stop%2C%20Iju%20Road%2C%20Lagos'),
        ],
        [
            'name' => 'Bamboo Plaza',
            'slug' => 'ogunnisi-road',
            'address' => '6/8 Ogunnisi Road, Bamboo Plaza, adjacent Omole Phase 1',
            'map_url' => env('AUTOMERCY_BAMBOO_MAP_URL', 'https://www.google.com/maps/search/?api=1&query=6%2F8%20Ogunnisi%20Road%2C%20Bamboo%20Plaza%2C%20Lagos'),
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
        'original_max_width' => 2400,
        'original_max_height' => 1800,
        'variants' => [
            'thumbnail' => 320,
            'card' => 720,
            'medium' => 1200,
            'large' => 1800,
        ],
        'webp_quality' => 82,
    ],
];
