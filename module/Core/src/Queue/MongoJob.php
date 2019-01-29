<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2019 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace Core\Queue;

use Core\Queue\Exception\RecoverableJobException;
use Psr\Log\NullLogger;
use SlmQueue\Job\AbstractJob;
use Zend\Log\LoggerAwareInterface;
use Zend\Log\LoggerAwareTrait;

/**
 * ${CARET}
 * 
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @todo write test 
 */
class MongoJob extends AbstractJob implements LoggerAwareInterface
{
    use LoggerAwareTrait;

    public function getLogger()
    {
        if (null === $this->logger) {
            $this->logger = new class() extends \Zend\Log\Logger {
                public function __construct() { }
                public function __destruct() { }

                public function log($priority, $message, $extra = [])
                {
                    //noop
                }
            };
        }

        return $this->logger;
    }

    public function execute()
    {
        $log = $this->getLogger();
        $log->info('Working inside Job now.');
        $log->warn('There will be problems!');

        echo $this->getContent() . PHP_EOL;

        $log->err('Upsi');
        $this->setMetadata('log.reason', 'Ich habs gesagt!');
        throw new RecoverableJobException('Upsi', ['delay' => 10]);
    }
}
