<?php

namespace Es\Controller;

use Es\Service\ElasticSearch;

class DocumentsController extends LayoutController
{
    public function get()
    {
        $search = $this->f('search');

        if (!$search) {
            $this->setContentAndExit([]);
        }

        $index = $this->f('index');

        if (!$index) {
            $this->forward('error', 'pageNotFound', ["Index was not provided"]);
        }

        $fields = $this->f('fields');

        if (!$fields || is_array($fields) && !count($fields)) {
            $this->forward('error', 'pageNotFound', ["Fields was not provided"]);
        }

        $locale = $this->f('locale', 'ru');

        /** @var ElasticSearch $elasticsearch */
        $elasticsearch = $this->s('elasticsearch');

        $data = $elasticsearch->search($index, $fields, $search, $locale);

        $this->setContent(['items' => $data]);
    }

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
