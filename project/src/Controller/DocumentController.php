<?php

namespace Es\Controller;

use Es\Service\ElasticSearch;

class DocumentController extends LayoutController
{
    public function post()
    {
        $index = $this->f('index');

        if (!$index) {
            $this->forward('error', 'pageNotFound', ["Index was not provided"]);
        }

        $id = $this->f('id');
        $title = $this->f('title');
        $text = $this->f('text');
        $locale = $this->f('locale', 'ru');

        if ($id === '') {
            $id = null;
        }

        /** @var ElasticSearch $elasticsearch */
        $elasticsearch = $this->s('elasticsearch');

        $elasticsearch->addDocument($index, [
            'id' => $id,
            'title' => $title,
            'text' => $text,
            'locale' => $locale
        ], $id);
    }

    public function delete()
    {
        $index = $this->f('index');

        if (!$index) {
            $this->forward('error', 'pageNotFound', ["Index was not provided"]);
        }

        $id = $this->f('id');

        if (!$id) {
            $this->forward('error', 'pageNotFound', ["ID was not provided"]);
        }

        /** @var ElasticSearch $elasticsearch */
        $elasticsearch = $this->s('elasticsearch');

        $elasticsearch->deleteDocumentById($index, $id);
    }
}
