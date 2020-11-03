<?php

namespace Project;

use Perfumer\Framework\Gateway\CompositeGateway;

class Gateway extends CompositeGateway
{
    protected function configure(): void
    {
        $this->addModule('es_html', 'ES_HTML_HOST', null, 'http');
        $this->addModule('es_html', 'es_html',      null, 'cli');
    }
}
