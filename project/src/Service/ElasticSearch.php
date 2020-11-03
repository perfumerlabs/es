<?php

namespace Es\Service;

use Elastica\Client;
use Elastica\Document;
use Elastica\Query;
use Elastica\Search;
use Elastica\Type\Mapping;

class ElasticSearch
{
    protected bool                $dummy = false;

    protected Client              $client;

    public function __construct(string $host, string $port, bool $dummy)
    {
        $this->dummy = $dummy;

        if ($this->dummy) {
            return;
        }

        $this->client = new Client(
            [
                'host' => $host,
                'port' => $port,
            ]
        );
    }

    /**
     * Create index
     *
     * @param string $index
     * @return bool
     */
    public function createIndex(string $index): bool
    {
        if ($this->dummy) {
            return true;
        }

        $elasticIndex = $this->client->getIndex($index);

        try {
            $elasticIndex->create(
                [
                    'settings' => [
                        'analysis' => [
                            'analyzer' => [
                                'standard_analyzer' => [
                                    'char_filter' => ['html_strip'],
                                    'tokenizer'   => 'standard',
                                    'filter'      => ['lowercase'],
                                ],
                                'standard_search'   => [
                                    'char_filter' => ['html_strip'],
                                    'tokenizer'   => 'lowercase',
                                ],
                            ],
                        ],
                    ],
                ],
                false
            );

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Delete index
     *
     * @param string $index
     * @return bool
     */
    public function deleteIndex(string $index): bool
    {
        if ($this->dummy) {
            return true;
        }

        $elasticIndex = $this->client->getIndex($index);

        try {
            $elasticIndex->delete();

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Set fields mapping
     *
     * @param string $index
     * @return bool
     */
    public function defineMapping(string $index)
    {
        $mapping = new Mapping();

        $elasticIndex = $this->client->getIndex($index);

        $elasticType = $elasticIndex->getType($index);

        try {
            $mapping->setType($elasticType);
            $mapping->setProperties(
                [
                    'id'     => [
                        'type' => 'integer',
                    ],
                    'title'  => [
                        'type'            => 'string',
                        'analyzer'        => 'standard_analyzer',
                        'search_analyzer' => 'standard_search',
                    ],
                    'text'   => [
                        'type'            => 'text',
                        'analyzer'        => 'standard_analyzer',
                        'search_analyzer' => 'standard_search',
                    ],
                    'locale' => [
                        'type' => 'keyword',
                    ],
                ]
            );

            $mapping->send();

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Search in index by fields, search word and locale
     *
     * @param string $index
     * @param array|string $fields
     * @param string $searchWord
     * @param string $locale
     * @return array
     */
    public function search(string $index, $fields, string $searchWord, string $locale = 'ru'): array
    {
        if ($this->dummy) {
            return [];
        }

        $search = new Search($this->client);

        $search->addIndex($index)->addType($index);

        $queryShouldPart = [];
        if (is_array($fields)) {
            foreach ($fields as $field) {
                $queryShouldPart[] = [
                    'match' => [$field => $searchWord],
                ];
            }
        } else {
            $queryShouldPart[] = [
                'match' => [$fields => $searchWord],
            ];
        }

        $queryData = [
            'query' => [
                'bool' => [
                    'must'   => [
                        'bool' => [
                            'should' => $queryShouldPart,
                        ],
                    ],
                    'filter' => [
                        'term' => [
                            'locale' => $locale,
                        ],
                    ],
                ],
            ],
        ];

        $query = new Query(
            $queryData
        );

        $search->setQuery($query);

        return $this->setResult($search->search());
    }

    /**
     * Add document in index
     *
     * @param string $index
     * @param array|string $data
     * @param null   $id
     */
    public function addDocument(string $index, $data, $id = null): void
    {
        if ($this->dummy) {
            return;
        }

        $elasticIndex = $this->client->getIndex($index);

        $elasticType = $elasticIndex->getType($index);

        $elasticType->addDocument(new Document($id, $data));

        $elasticIndex->refresh();
    }

    /**
     * Delete document from index by id
     *
     * @param string $index
     * @param int|string $id
     */
    public function deleteDocumentById(string $index, $id): void
    {
        if ($this->dummy) {
            return;
        }

        $elasticIndex = $this->client->getIndex($index);

        $elasticType = $elasticIndex->getType($index);

        $elasticType->deleteById($id);
    }

    /**
     * Delete document by query
     *
     * @param string $index
     * @param string $field
     * @param        $value
     */
    public function deleteDocumentByQuery(string $index, string $field, $value): void
    {
        if ($this->dummy) {
            return;
        }

        $query = new Query(
            [
                'query' => [
                    'match' => [
                        $field => $value,
                    ],
                ],
            ]
        );

        $elasticIndex = $this->client->getIndex($index);

        $elasticType = $elasticIndex->getType($index);

        $elasticType->deleteByQuery($query);
    }

    private function setResult($results): array
    {
        if ($this->dummy) {
            return [];
        }

        $completions = [];
        foreach ($results as $result) {
            $source        = $result->getData();
            $completions[] = [
                'id'     => $source['id'],
                'title'  => $source['title'],
                'text'   => $source['text'],
                'locale' => $source['locale'],
            ];
        }

        return $completions;
    }
}

