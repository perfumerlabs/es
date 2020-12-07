<?php

namespace Es\Controller;

use Es\Service\ElasticSearch;

class IndexController extends LayoutController
{
    public function post()
    {
        $name = $this->f('name');

        $this->validateNotEmpty($name, 'name');

        /** @var ElasticSearch $elasticsearch */
        $elasticsearch = $this->s('elasticsearch');

        $elasticsearch->createIndex($name);

        $elasticsearch->defineMapping($name);
    }

    public function delete()
    {
        $name = $this->f('name');

        $this->validateNotEmpty($name, 'name');

        /** @var ElasticSearch $elasticsearch */
        $elasticsearch = $this->s('elasticsearch');

        $elasticsearch->deleteIndex($name);
    }
}
