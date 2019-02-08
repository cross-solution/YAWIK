<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2019 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace Core\Queue\Controller;

use Core\Queue\MongoQueue;
use SlmQueue\Queue\QueuePluginManager;
use Zend\Mvc\Console\Controller\AbstractConsoleController;

/**
 * Console controller for list queue jobs.
 * 
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 */
class MongoQueueListController extends AbstractConsoleController
{
    /**
     * The queue plugin manager
     *
     * @var QueuePluginManager
     */
    private $queuePluginManager;

    /**
     * MongoQueueListController constructor.
     *
     * @param QueuePluginManager $queuePluginManager
     */
    public function __construct(QueuePluginManager $queuePluginManager)
    {
        $this->queuePluginManager = $queuePluginManager;
    }

    /**
     * List jobs from a queue
     *
     * @return string
     */
    public function listAction()
    {
        $queue   = $this->params()->fromRoute('queue');
        $queue   = $this->queuePluginManager->get($queue);

        if (!$queue instanceOf MongoQueue) {
            return 'Unsupported queue type.';
        }

        $statusMap = function($stat) {
            static $map = [
                'pending' => MongoQueue::STATUS_PENDING,
                'running' => MongoQueue::STATUS_RUNNING,
                'failed'  => MongoQueue::STATUS_FAILED,
            ];

            return $map[$stat] ?? $map['pending'];
        };

        $console = $this->getConsole();
        $jobs    = $queue->listing([
            'limit'  => (int) $this->params()->fromRoute('limit', 0),
            'status' => $statusMap($this->params()->fromRoute('status')),
        ]);

        if (!$jobs) {
            return 'Queue is empty.';
        }

        $lineTmpl = '%-20s %s';
        foreach ($jobs as $job) {
            $console->writeLine(get_class($job['job']) . ' [ ' . $job['job']->getId() . ' ]');

            foreach (['created', 'executed', 'scheduled'] as $key) {
                $console->writeLine(sprintf($lineTmpl, ucFirst($key), $job[$key]->toDateTime()->format('Y-m-d H:i:s')));
            }
            $console->writeLine(sprintf($lineTmpl, 'Tries', $job['tried']));
            $console->writeLine();
            $console->writeLine();
        }

        return '';
    }

}
