<?php

namespace Es\Module;

use Perfumer\Framework\Controller\Module;

class CliModule extends Module
{
    public $name = 'es';

    public $router = 'router.console';

    public $request = 'es.request';
}