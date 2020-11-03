<?php

return [
    'es.request' => [
        'class' => 'Perfumer\\Framework\\Proxy\\Request',
        'arguments' => ['$0', '$1', '$2', '$3', [
            'prefix' => 'Es\\Command',
            'suffix' => 'Command'
        ]]
    ],
];
