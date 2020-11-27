<?php

namespace Es\Service;

use Elastica\Client;
use Elastica\Document;
use Elastica\Query;
use Elastica\Search;
use Elastica\Type\Mapping;

class ElasticSearch
{
    protected Client $client;

    public function __construct(string $host, string $port)
    {
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
     * @param string       $index
     * @param array|string $fields
     * @param string       $searchWord
     * @param string       $locale
     * @return array
     */
    public function search(
        string $index,
        string $searchWord,
        string $locale,
        int $from = 0,
        int $size = 50
    ): array {
        $search = new Search($this->client);

        $search->addIndex($index)->addType($index);

        $queryData = [
            'from'  => $from,
            'size'  => $size,
            'query' => [
                'bool' => [
                    'must'   => [
                        'bool' => [
                            'should' => [
                                [
                                    'match' => ['title' => $searchWord],
                                ],
                                [
                                    'match' => ['text' => $searchWord],
                                ]
                            ],
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

        $query = new Query($queryData);

        $search->setQuery($query);

        return $this->setResult($search->search());
    }

    /**
     * Add document in index
     *
     * @param string       $index
     * @param array|string $data
     * @param null         $id
     */
    public function addDocument(string $index, $data, $id = null): void
    {
        $elasticIndex = $this->client->getIndex($index);

        $elasticType = $elasticIndex->getType($index);

        $elasticType->addDocument(new Document($id, $data));

        $elasticIndex->refresh();
    }

    /**
     * Add array of documents in index
     *
     * @param string $index
     * @param array  $data
     */
    public function addDocuments(string $index, array $data): void
    {
        $elasticIndex = $this->client->getIndex($index);

        $elasticType = $elasticIndex->getType($index);

        foreach (array_chunk($data, 500) as $chunk) {
            $documents = [];
            foreach ($chunk as $item) {
                $documents[] = new Document($item['id'] ?? null, $item);
            }

            $elasticType->addDocuments($documents);
        }

        $elasticIndex->refresh();
    }

    /**
     * Delete document from index by id
     *
     * @param string     $index
     * @param int|string $id
     */
    public function deleteDocumentById(string $index, $id): void
    {
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
