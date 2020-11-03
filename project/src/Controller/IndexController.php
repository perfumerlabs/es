<?php

namespace Es\Controller;

use Es\Service\ElasticSearch;

class IndexController extends LayoutController
{
    public function post()
    {
        $index = $this->f('index');

        if (!$index) {
            $this->forward('error', 'pageNotFound', ["Index was not provided"]);
        }

        /** @var ElasticSearch $elasticsearch */
        $elasticsearch = $this->s('elasticsearch');

        $elasticsearch->createIndex($index);

        $elasticsearch->defineMapping($index);
    }

    public function delete()
    {
        $index = $this->f('index');

        if (!$index) {
            $this->forward('error', 'pageNotFound', ["Index was not provided"]);
        }

        /** @var ElasticSearch $elasticsearch */
        $elasticsearch = $this->s('elasticsearch');

        $elasticsearch->deleteIndex($index);
    }
}
