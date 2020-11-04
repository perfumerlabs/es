<?php

namespace Es\Controller;

use Es\Service\ElasticSearch;

class DocumentsController extends LayoutController
{
    public function post()
    {
        $index = $this->f('index');

        if (!$index) {
            $this->forward('error', 'pageNotFound', ["Index was not provided"]);
        }

        $documents = $this->f('documents');

        if (!is_array($documents) || !count($documents)) {
            $this->forward('error', 'pageNotFound', ["Documents were not provided"]);
        }

        /** @var ElasticSearch $elasticsearch */
        $elasticsearch = $this->s('elasticsearch');

        $elasticsearch->addDocuments($index, $documents);
    }
}
