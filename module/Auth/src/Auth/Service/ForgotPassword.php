<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license       MIT
 */

namespace Auth\Service;

use Auth\Repository;
use Auth\Service\Exception\UserDoesNotHaveAnEmailException;
use Auth\Service\Exception\UserNotFoundException;
use Auth\Options\ModuleOptions;
use Core\Controller\Plugin;
use Zend\InputFilter\InputFilterInterface;
use Zend\Mvc\Controller\Plugin\Url;
use Zend\EventManager\EventManagerAwareInterface;
use Zend\EventManager\EventManagerInterface;
use Auth\Filter\LoginFilter;
use Auth\Listener\Events\AuthEvent;

/**
 * Class ForgotPassword
 * @package Auth\Service
 */
class ForgotPassword
{
    /**
     * @var Repository\User
     */
    private $userRepository;

    /**
     * @var UserUniqueTokenGenerator
     */
    private $tokenGenerator;

    /**
     * @var LoginFilter
     */
    private $loginFilter;

    /**
     * @var EventManagerInterface
     */
    protected $eventManager;

    /**
     * @var string
     */
    protected $suffix;

    /**
     * @var \Auth\Options\ModuleOptions
     */
    protected $options;

    /**
     * @param Repository\User $userRepository
     * @param UserUniqueTokenGenerator $tokenGenerator
     * @param LoginFilter $loginFilter
     */
    public function __construct(
        Repository\User $userRepository,
        UserUniqueTokenGenerator $tokenGenerator,
        LoginFilter $loginFilter,
        ModuleOptions $options
    ) {
        $this->userRepository = $userRepository;
        $this->tokenGenerator = $tokenGenerator;
        $this->loginFilter = $loginFilter;
        $this->options = $options;
    }

    public function setSuffix($suffix)
    {
        $this->suffix = $suffix;
        return $this;
    }

    /**
     * @param EventManagerInterface $eventManager
     * @return $this|void
     */
    public function setEventManager(EventManagerInterface $eventManager)
    {
        $this->eventManager = $eventManager;
        return $this;
    }

    /**
     * @return EventManagerInterface
     */
    public function getEventManager()
    {
        return $this->eventManager;
    }

    /**
     * @todo remove unused $mailer parameter an fix tests
     *
     * @param InputFilterInterface $filter
     * @param Plugin\Mailer $mailer
     * @param Url $url
     * @throws \LogicException
     * @throws UserDoesNotHaveAnEmailException
     * @throws UserNotFoundException
     */
    public function proceed(InputFilterInterface $filter, Plugin\Mailer $mailer, Url $url)
    {
        if (!$filter->isValid()) {
            throw new \LogicException('Form is not valid');
        }

        $identity = $filter->getValue('identity');

        $suffix = $this->loginFilter->filter();

        if (!($user = $this->userRepository->findByLoginOrEmail($identity, $suffix))) {
            throw new UserNotFoundException('User is not found');
        }

        if (!($email = $user->getInfo()->getEmail())) {
            throw new UserDoesNotHaveAnEmailException('User does not have an email');
        }

        $tokenHash = $this->tokenGenerator->generate($user);

        $resetLink = $url->fromRoute(
            'lang/goto-reset-password',
            array('token' => $tokenHash, 'userId' => $user->getId()),
            array('force_canonical' => true)
        );

        $e = new AuthEvent();
        $e->setResetLink($resetLink);
        $e->setUser($user);

        $this->eventManager->trigger(AuthEvent::EVENT_AUTH_NEWPASSWORD, $e);
    }
}
