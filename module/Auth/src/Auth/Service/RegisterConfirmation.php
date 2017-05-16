<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license       MIT
 */

namespace Auth\Service;

use Auth\Listener\Events\AuthEvent;
use Auth\Repository;
use Auth\Service\Exception;
use Core\EventManager\EventManager;
use Zend\Authentication\AuthenticationService;
use Zend\EventManager\EventManagerInterface;

class RegisterConfirmation
{
    /**
     * @var Repository\User
     */
    private $userRepository;

    /**
     * @var AuthenticationService
     */
    private $authenticationService;

    /**
     * Auth/Events
     *
     * @var EventManagerInterface
     */
    private $events;

    public function __construct(Repository\User $userRepository, AuthenticationService $authenticationService)
    {
        $this->userRepository = $userRepository;
        $this->authenticationService = $authenticationService;
    }

    /**
     * @param \Zend\EventManager\EventManagerInterface $events
     *
     * @return self
     */
    public function setEventManager($events)
    {
        $this->events = $events;

        return $this;
    }

    /**
     * @return \Zend\EventManager\EventManagerInterface
     */
    public function getEventManager()
    {
        if (!$this->events) {
            $this->events = new EventManager();
            $this->events->setEventPrototype(new AuthEvent());
        }

        return $this->events;
    }

    public function proceed($userId)
    {
        if (!($user = $this->userRepository->find($userId))) {
            throw new Exception\UserNotFoundException('User cannot be found');
        }

        /* \Auth\Entity\Info */
        $user->getInfo()->setEmailVerified(true);
        $user->setEmail($user->getInfo()->getEmail()); // Set verified email as primary email.
        $this->userRepository->store($user);
        $this->authenticationService->getStorage()->write($user->getId());

        /* @var EventManager $events
         * @var \Auth\Listener\Events\AuthEvent $event
         */
        $events = $this->getEventManager();
        $event  = $events->getEvent(AuthEvent::EVENT_USER_CONFIRMED, $this);
        $event->setUser($user);
        $events->triggerEvent($event);
    }
}
