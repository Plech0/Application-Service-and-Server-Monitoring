<?php

return [
    'components' => [
        'elasticsearch' => [
            'class' => yii\elasticsearch\Connection::class,
            'nodes' => [
                ['http_address' => 'elasticsearch:9200'],
                // configure more hosts if you have a cluster
            ],
        ],
    ]
];
