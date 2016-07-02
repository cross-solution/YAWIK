<?php
/**
 * YAWIK
 *
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

return array(
    'doctrine' => [
        'eventmanager' => [
            'odm_default' => [
                'subscribers' => [
                    'Solr/Event/Listener/JobEventSubscriber'
                ]
            ]
        ]
    ],
    'listeners' => [
        'Solr\Event\Listener\CreatePaginatorListener',
    ],
    'options' => [
        'Solr/Options/Module' => [
            'class' => '\Solr\Options\ModuleOptions',
        ]
    ],
    'service_manager' => [
        'factories' => [
            'Solr\Event\Listener\CreatePaginatorListener' => 'Solr\Event\Listener\CreatePaginatorListener::factory',
            'Solr/Client' => 'Solr\Factory\SolrClientFactory',
            'Solr/Event/Listener/JobEventSubscriber' => 'Solr\Event\Listener\JobEventSubscriber::factory',
            'Solr/Manager' => 'Solr\Bridge\Manager::factory',
            'Solr/ResultConverter' => 'Solr\Bridge\ResultConverter::factory',
        ],
    ],
    'paginator_manager' => [
        'factories' => [
            // replace Jobs/Board paginator with this paginator
            'Solr/Jobs/Board' => 'Solr\Paginator\JobsBoardPaginatorFactory',
        ]
    ],
    'filters' => [
        'factories'=> array(
            'Solr/Jobs/PaginationQuery' => 'Solr\Filter\JobBoardPaginationQuery::factory',
        ),
    ],
);