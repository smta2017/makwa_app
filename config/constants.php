<?php

return [
    'vendor_filter_attributes' => [
        'free_delivery',
        'new',
        'offers'
    ],
    'variants_ratings' => [
        [
            "title" => "Vendor",
            "elemnts" => [
                [
                    'rate_id' => 1,
                    "title" => 'Order packaging',
                ], [
                    'rate_id' => 2,
                    "title" => 'Delivery time rating',
                ],
            ]
        ],
        [
            "title" => "Service",
            "elemnts" => [
                [
                    'rate_id' => 3,
                    "title" => 'Pricing rating',
                ], [
                    'rate_id' => 4,
                    "title" => 'Service quality',
                ],
            ]
        ],
        [
            "title" => "Agent",
            "elemnts" => [
                [
                    'rate_id' => 5,
                    "title" => 'Customer service',
                ], [
                    'rate_id' => 6,
                    "title" => 'Friendly',
                ],
            ]
        ]

    ],
];
