<?php

namespace Es\Module;

use Perfumer\Framework\Controller\Module;

class HttpModule extends Module
{
    public $name = 'es';

    public $router = 'es.router';

    public $request = 'es.request';

    public $components = [
        'view' => 'view.status',
    ];
}