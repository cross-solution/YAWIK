<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2019 Cross Solution <http://cross-solution.de>
 */

declare(strict_types=1);

/** */
namespace Core\Queue;

use Zend\Log\LoggerInterface;

/**
 * Trait implementing LoggerAwareInterface.
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @todo write test 
 */
trait LoggerAwareJobTrait
{
    /**
     *
     *
     * @var LoggerInterface
     */
    private $logger;

    /**
     * Set the logger instance
     *
     * @param LoggerInterface $logger
     */
    public function setLogger(LoggerInterface $logger) : void
    {
        $this->logger = $logger;
    }

    /**
     * Get the logger instance.
     *
     * If no logger is set, it will create and return a null logger.
     *
     * @return LoggerInterface
     */
    public function getLogger() : LoggerInterface
    {
        if (!$this->logger) {
            $this->logger = new class implements LoggerInterface
            {
                public function emerg($message, $extra = []) {}
                public function alert($message, $extra = []) {}
                public function crit($message, $extra = []) {}
                public function err($message, $extra = []) {}
                public function warn($message, $extra = []) {}
                public function notice($message, $extra = []) {}
                public function info($message, $extra = []) {}
                public function debug($message, $extra = []) {}
            };
        }

        return $this->logger;
    }
}
