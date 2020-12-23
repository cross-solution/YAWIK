<?php

/**
 * YAWIK
 *
 * @see       https://github.com/cross-solution/YAWIK for the canonical source repository
 * @copyright https://github.com/cross-solution/YAWIK/blob/master/COPYRIGHT
 * @license   https://github.com/cross-solution/YAWIK/blob/master/LICENSE
 */

declare(strict_types=1);

namespace Core\Queue\Strategy;

use Core\Mail\MailService;
use Laminas\EventManager\EventManagerInterface;
use SlmQueue\Strategy\AbstractStrategy;
use SlmQueue\Worker\Event\AbstractWorkerEvent;

/**
 * TODO: description
 *
 * @author Mathias Gelhausen
 * TODO: write tests
 */
class SendMailStrategy extends AbstractStrategy
{
    private $mailService;

    public function __construct(MailService $mailService)
    {
        $this->mailService = $mailService;
    }

    /**
     * Registers itself with an EventManager
     *
     * @param EventManagerInterface $events
     * @param int                   $priority
     */
    public function attach(EventManagerInterface $events, $priority = 1) : void
    {
        $this->listeners[] = $events->attach(AbstractWorkerEvent::EVENT_PROCESS_JOB, [$this, 'logJobStart'], 1000);
        $this->listeners[] = $events->attach(AbstractWorkerEvent::EVENT_PROCESS_JOB, [$this, 'logJobEnd'], -1000);
        $this->listeners[] = $events->attach(AbstractWorkerEvent::EVENT_PROCESS_IDLE, [$this, 'injectLoggerInEvent'], 1000);
        $this->listeners[] = $events->attach(AbstractWorkerEvent::EVENT_PROCESS_STATE, [$this, 'injectLoggerInEvent'], 1000);
    }
}
