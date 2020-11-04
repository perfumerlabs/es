<?php

return [
    'fast_router' => [
        'shared' => true,
        'init' => function(\Perfumer\Component\Container\Container $container) {
            return \FastRoute\simpleDispatcher(function(\FastRoute\RouteCollector $r) {
                $r->addRoute('POST',   '/index', 'index.post');
                $r->addRoute('DELETE', '/index', 'index.delete');

                $r->addRoute('GET',    '/document', 'document.get');
                $r->addRoute('POST',   '/document', 'document.post');
                $r->addRoute('DELETE', '/document', 'document.delete');

                $r->addRoute('POST',   '/documents', 'documents.post');
            });
        }
    ],

    'es.router' => [
        'shared' => true,
        'class' => 'Perfumer\\Framework\\Router\\Http\\FastRouteRouter',
        'arguments' => ['#gateway.http', '#fast_router', [
            'data_type' => 'json',
            'allowed_actions' => ['get', 'post', 'delete'],
        ]]
    ],

    'es.request' => [
        'class' => 'Perfumer\\Framework\\Proxy\\Request',
        'arguments' => ['$0', '$1', '$2', '$3', [
            'prefix' => 'Es\\Controller',
            'suffix' => 'Controller'
        ]]
    ]
];
