<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2017 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace Applications\Listener;

use Applications\Paginator\JobSelectPaginator;
use Core\Listener\Events\AjaxEvent;

/**
 * Listener to load the job title for JobSelect element via Ajax
 * 
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @since 0.29.2
 */
class JobSelectValues 
{
    /**
     * The job paginator.
     *
     * @var JobSelectPaginator
     */
    private $paginator;

    public function __construct($paginator)
    {
        $this->paginator = $paginator;
    }

    public function __invoke(AjaxEvent $event)
    {
        $request = $event->getRequest();
        $query   = $request->getQuery();
        $q       = $query->get('q');
        $p       = $query->get('page');

        $this->paginator->search($q)
                        ->setCurrentPageNumber($p)
                        ->setItemCountPerPage(30);

        $options = [['id' => 0, 'text' => '']];
        foreach ($this->paginator as $job) {
            /* @var \Jobs\Entity\Job $job */
            $options[] = [
                'id' => $job->getId(),
                'text' => $job->getTitle()
            ];
        }

        return [
            'items' => $options,
            'count' => $this->paginator->getTotalItemCount()
        ];
    }
}