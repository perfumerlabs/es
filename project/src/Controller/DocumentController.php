<?php

namespace Es\Controller;

use Es\Service\ElasticSearch;

class DocumentController extends LayoutController
{
    public function post()
    {
        $id = $this->f('id');
        $index = $this->f('index');
        $title = $this->f('title');
        $text = $this->f('text');
        $locale = $this->f('locale');

        $this->validateNotEmpty($index, 'index');
        $this->validateNotEmpty($id, 'id');
        $this->validateNotEmpty($locale, 'locale');
        $this->validateNotEmpty($text, 'text');

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
        $id = $this->f('id');

        $this->validateNotEmpty($index, 'index');
        $this->validateNotEmpty($id, 'id');

        /** @var ElasticSearch $elasticsearch */
        $elasticsearch = $this->s('elasticsearch');

        $elasticsearch->deleteDocumentById($index, $id);
    }
}
