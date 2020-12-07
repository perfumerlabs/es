<?php

namespace Project;

use Perfumer\Framework\Gateway\CompositeGateway;

class Gateway extends CompositeGateway
{
    protected function configure(): void
    {
        $this->addModule('es', null, null, 'http');
        $this->addModule('es', 'es', null, 'cli');
    }
}
