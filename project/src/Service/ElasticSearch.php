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
     */
    public function createIndex(string $index)
    {
        $elasticIndex = $this->client->getIndex($index);

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

        error_log(sprintf('Index "%s" is created', $index));
    }

    /**
     * Delete index
     *
     * @param string $index
     */
    public function deleteIndex(string $index)
    {
        $elasticIndex = $this->client->getIndex($index);

        $elasticIndex->delete();

        error_log(sprintf('Index "%s" is dropped', $index));
    }

    /**
     * Set fields mapping
     *
     * @param string $index
     */
    public function defineMapping(string $index)
    {
        $mapping = new Mapping();

        $elasticIndex = $this->client->getIndex($index);

        $elasticType = $elasticIndex->getType($index);

        $mapping->setType($elasticType);
        $mapping->setProperties(
            [
                'code'     => [
                    'type' => 'keyword',
                ],
                'locale' => [
                    'type' => 'keyword',
                ],
                'title'  => [
                    'type' => 'text',
//                    'analyzer'        => 'standard_analyzer',
//                    'search_analyzer' => 'standard_search',
                ],
                'text'   => [
                    'type' => 'text',
//                    'analyzer'        => 'standard_analyzer',
//                    'search_analyzer' => 'standard_search',
                ],
            ]
        );

        $mapping->send();

        error_log(sprintf('Mapping of index "%s" is defined', $index));
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

        error_log(sprintf('Document with id = %s is saved', $id));
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

                error_log(sprintf('Document with id = %s is saved', $item['id']));
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

        error_log(sprintf('Document with id = %s is deleted', $id));
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

        error_log(sprintf('Documents by %s = %s are deleted', $index, $value));
    }

    private function setResult($results): array
    {
        $completions = [];
        foreach ($results as $result) {
            $source        = $result->getData();
            $completions[] = [
                'code'   => $source['code'],
                'title'  => $source['title'],
                'text'   => $source['text'],
                'locale' => $source['locale'],
            ];
        }

        return $completions;
    }
}
