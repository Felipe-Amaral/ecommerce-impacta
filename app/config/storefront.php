<?php

return [
    'auth' => [
        'social' => [
            // Supported out of the box in this project: google, facebook, github
            'providers' => array_values(array_filter(array_map(
                fn (string $provider) => trim($provider),
                explode(',', (string) env('STOREFRONT_SOCIAL_PROVIDERS', 'google,facebook,github'))
            ))),
            'stateless' => (bool) env('STOREFRONT_SOCIAL_STATELESS', false),
        ],
    ],

    'payments' => [
        'driver' => env('STOREFRONT_PAYMENT_DRIVER', 'manual'), // manual|mercadopago
        'success_redirect' => env('STOREFRONT_PAYMENT_SUCCESS_REDIRECT', 'gateway'), // gateway|success_page
    ],

    'shipping' => [
        'driver' => env('STOREFRONT_SHIPPING_DRIVER', 'local'), // local|melhor_envio
        'origin' => [
            'zipcode' => env('STOREFRONT_ORIGIN_ZIPCODE', '01001000'),
            'city' => env('STOREFRONT_ORIGIN_CITY', 'Sao Paulo'),
            'state' => env('STOREFRONT_ORIGIN_STATE', 'SP'),
        ],
        'pickup' => [
            'enabled' => (bool) env('STOREFRONT_PICKUP_ENABLED', true),
            'label' => env('STOREFRONT_PICKUP_LABEL', 'Retirada no balcão'),
            'lead_time_days' => (int) env('STOREFRONT_PICKUP_LEAD_TIME_DAYS', 1),
            'address' => env('STOREFRONT_PICKUP_ADDRESS', 'Retirada no balcão mediante confirmação do pedido'),
        ],
        'package_defaults' => [
            'width_cm' => (int) env('STOREFRONT_PACKAGE_WIDTH_CM', 20),
            'height_cm' => (int) env('STOREFRONT_PACKAGE_HEIGHT_CM', 6),
            'length_cm' => (int) env('STOREFRONT_PACKAGE_LENGTH_CM', 28),
            'weight_grams' => (int) env('STOREFRONT_PACKAGE_WEIGHT_GRAMS', 500),
        ],
    ],
];
