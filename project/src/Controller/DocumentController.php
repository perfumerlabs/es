<?php

namespace Es\Controller;

use Es\Service\ElasticSearch;

class DocumentController extends LayoutController
{
    public function post()
    {
        $code = $this->f('code');
        $index = $this->f('index');
        $title = $this->f('title');
        $text = $this->f('text');
        $locale = $this->f('locale');

        $this->validateNotEmpty($index, 'index');
        $this->validateNotEmpty($code, 'code');
        $this->validateNotEmpty($locale, 'locale');
        $this->validateNotEmpty($text, 'text');

        $document_id = $code . '_' . $locale;

        /** @var ElasticSearch $elasticsearch */
        $elasticsearch = $this->s('elasticsearch');

        $elasticsearch->addDocument($index, [
            'code' => $code,
            'title' => $title,
            'text' => $text,
            'locale' => $locale
        ], $document_id);
    }

    public function delete()
    {
        $index = $this->f('index');
        $code = $this->f('code');
        $locale = $this->f('locale');

        $this->validateNotEmpty($index, 'index');
        $this->validateNotEmpty($code, 'code');

        /** @var ElasticSearch $elasticsearch */
        $elasticsearch = $this->s('elasticsearch');

        if ($locale) {
            $document_id = $code . '_' . $locale;

            $elasticsearch->deleteDocumentById($index, $document_id);
        } else {
            $elasticsearch->deleteDocumentByQuery($index, 'code', $code);
        }
    }
}
