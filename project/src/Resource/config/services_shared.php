<?php

return [
    'gateway' => [
        'shared' => true,
        'class' => 'Project\\Gateway',
        'arguments' => ['#application', '#gateway.http', '#gateway.console']
    ],
    'elasticsearch' => [
        'shared' => true,
        'class' => 'Es\\Service\\ElasticSearch',
        'arguments' => [
            '@elasticsearch/host',
            '@elasticsearch/port',
        ]
    ],
];