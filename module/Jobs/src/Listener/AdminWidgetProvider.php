<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2016 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace Jobs\Listener;

use Core\Controller\AdminControllerEvent;
use Jobs\Entity\StatusInterface;

/**
 * ${CARET}
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @todo write test
 */
class AdminWidgetProvider
{
    protected $repository;

    public function __construct($repository)
    {
        $this->repository = $repository;
    }

    public function __invoke(AdminControllerEvent $event)
    {
        $total = $this->repository->count(['isDraft' => false]);
        $active = $this->repository->count(['status.name' => StatusInterface::ACTIVE]);
        $pending = $this->repository->count(['status.name' => StatusInterface::CREATED]);

        $event->addViewVariables('jobs', [
                                     'title' => 'Jobs',
                                     'data' => [
                                        /*@translate*/ 'Total jobs' => [
                                            'url' => [ 'lang/admin/jobs', [], true ],
                                            'value' => $total,
                                        ],
                                        /*@translate*/ 'Active jobs' => [
                                             'url' => [ 'lang/admin/jobs', [], ['query' => [ 'status' => 'active' ]], true ],
                                             'value' => $active
                                         ],
                                        /*@translate*/ 'Pending jobs' => [
                                             'url' => [ 'lang/admin/jobs', [], ['query' => [ 'status' => 'created' ]], true ],
                                             'value' => $pending
                                         ]
                                     ],
                                 ]);
    }
}
