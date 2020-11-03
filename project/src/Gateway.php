<?php

namespace Project;

use Perfumer\Framework\Gateway\CompositeGateway;

class Gateway extends CompositeGateway
{
    protected function configure(): void
    {
        $this->addModule('es', 'ES_HTML_HOST', null, 'http');
        $this->addModule('es', 'es_html',      null, 'cli');
    }
}
