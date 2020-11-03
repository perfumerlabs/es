<?php

namespace Es\Command;

use Es\Service\ElasticSearch;
use Perfumer\Framework\Controller\PlainController;
use Perfumer\Framework\Router\ConsoleRouterControllerHelpers;

class SettingsCommand extends PlainController
{
    use ConsoleRouterControllerHelpers;

    public function action()
    {
        $index = 'forum_post_data';

        $param = $this->o('param');

        /** @var ElasticSearch $es */
        $es = $this->s('elasticsearch');

        switch ($param) {
            case 'init_index':
                if ($es->createIndex($index) && $es->defineMapping($index)) {
                    print_r('Index created successfully!');
                    return;
                }
                print_r('Index already exists');
                break;
            case 'delete_index':
                if ($es->deleteIndex($index)) {
                    print_r('Index deleted successfully!');

                    return;
                }
                print_r('Index does not exist');
                break;
            case 'search':
                print_r($es->search($index, ['title', 'text'], 'test', 'ru'));
                break;
            case 'push':
                $es->addDocument(
                    $index,
                    [
                        'id' => 1,
                        'title' => 'test title',
                        'text' => 'test text',
                        'locale' => 'ru',
                    ],
                    1
                );

                $es->addDocument(
                    $index,
                    [
                        'id' => 2,
                        'title' => 'new title',
                        'text' => 'new text',
                        'locale' => 'ru',
                    ],
                    2
                );

                $es->addDocument(
                    $index,
                    [
                        'id' => 3,
                        'title' => 'testing2 title',
                        'text' => 'testing2 text',
                        'locale' => 'ru',
                    ],
                    3
                );

                $es->addDocument(
                    $index,
                    [
                        'id' => 4,
                        'title' => 'test 4title',
                        'text' => 'dsdsd 4text',
                        'locale' => 'ru',
                    ],
                    4
                );
                break;
        }
    }
}