<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license       MIT
 */

namespace Auth\Service;

use Auth\Entity\User;
use Auth\Listener\Events\AuthEvent;
use Auth\Service\Exception;
use Core\EventManager\EventManager;
use Core\Options\ModuleOptions;
use Core\Controller\Plugin;
use Zend\EventManager\EventManagerInterface;
use Zend\InputFilter\InputFilterInterface;
use Zend\Mvc\Controller\Plugin\Url;
use Auth\Repository\User as UserRepository;
use Core\Mail\MailService;

/**
 * Class Register
 * @package Auth\Service
 *
 * @author Rafael Ksiazek
 * @author Mathias Weitz
 * @author Carsten Bleek <bleek@cross-solution.de>
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 */
class Register
{
    /**
     * @var MailService
     */
    protected $mailService;

    /**
     * @var UserRepository
     */
    protected $userRepository;

    /**
     * @var InputFilterInterface
     */
    protected $filter;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $email;

    /**
     * @var Url
     */
    protected $urlPlugin;

    /**
     * @var Plugin\Mailer
     */
    protected $mailer;

    /**
     * @var ModuleOptions
     */
    protected $options;

    /**
     * @var User
     */
    protected $user;

    /**
     * Auth/Events
     *
     * @var EventManagerInterface
     */
    protected $events;

    public function __construct(UserRepository $userRepository, MailService $mailService, ModuleOptions $options)
    {
        $this->userRepository = $userRepository;
        $this->mailService = $mailService;
        $this->options = $options;
    }

    /**
     * @return UserRepository
     */
    protected function getUserRepository()
    {
        return $this->userRepository;
    }

    /**
     * @return MailService
     */
    protected function getMailService()
    {
        return $this->mailService;
    }

    /**
     * @param InputFilterInterface $filter
     * @return $this
     */
    public function setFormFilter(InputFilterInterface $filter)
    {
        $this->filter = $filter;
        return $this;
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



    /**
     * @param Plugin\Mailer $mailer
     * @return $this
     */
    protected function setMailer(Plugin\Mailer $mailer)
    {
        $this->mailer = $mailer;
        return $this;
    }

    /**
     * @param $user
     * @return $this
     */
    protected function setUser($user)
    {
        $this->user = $user;
        return $this;
    }

    /**
     * @return User
     */
    protected function getUser()
    {
        return $this->user;
    }

    /**
     * @param Url $urlPlugin
     * @return $this
     */
    protected function setUrlPlugin($urlPlugin)
    {
        $this->urlPlugin = $urlPlugin;
        return $this;
    }

    /**
     * @return Url
     */
    protected function getUrlPlugin()
    {
        return $this->urlPlugin;
    }

    /**
     * @return $this
     * @throws \LogicException
     * @throws \InvalidArgumentException
     */
    protected function extractUserMailFromFormular()
    {
        if (!isset($this->filter)) {
            throw new \InvalidArgumentException('No Form set');
        }
        if (!$this->filter->isValid()) {
            throw new \LogicException('Form is not valid');
        }

        $registerFilter = $this->filter->get('register');

        $this->setName($registerFilter->getValue('name'));
        $this->setEmail($registerFilter->getValue('email'));
        $this->setRole($registerFilter->getValue('role'));

        return $this;
    }

    /**
     * @return null|string
     */
    protected function getName()
    {
        if (!isset($this->name)) {
            $this->extractUserMailFromFormular();
        }
        return $this->name;
    }

    /**
     * @param $name
     * @return string
     */
    protected function setName($name)
    {
        $this->name = $name;
        return $this->name;
    }

    /**
     * @return null|string
     */
    protected function getEmail()
    {
        if (!isset($this->email)) {
            $this->extractUserMailFromFormular();
        }
        return $this->email;
    }

    /**
     * Email-Address
     * @param $email string
     * @return mixed
     */
    protected function setEmail($email)
    {
        $this->email = $email;
        return $this;
    }

    /**
     * Get the user role.
     *
     * @return string
     * @since 0.26
     */
    protected function getRole()
    {
        if (!isset($this->role)) {
            $this->extractUserMailFromFormular();
        }

        return $this->role;
    }

    /**
     * set the user role
     *
     * @param string $role
     *
     * @return self
     * @since 0.26
     */
    protected function setRole($role)
    {
        $this->role = $role;

        return $this;
    }

    /**
     * @return User
     * @throws Exception\UserAlreadyExistsException
     */
    protected function proceedUser()
    {
        if (!isset($this->user)) {
            $userRepository = $this->getUserRepository();
            $name = $this->getName();
            $email = $this->getEmail();
            $role = $this->getRole() ?: User::ROLE_RECRUITER;

            if (($userRepository->findByLoginOrEmail($email))) {
                //return Null;
                throw new Exception\UserAlreadyExistsException('User already exists');
            }

            $user = $userRepository->create(
                array(
                'login' => $email,
                'role' => $role,
                )
            );

            $info = $user->getInfo();
            $info->setEmail($email);
            $info->setFirstName($name);
            $info->setEmailVerified(false);

            if (strstr($name, ' ') !== false) {
                $nameParts = explode(' ', $name);
                $firstName = array_shift($nameParts);
                $lastName = implode(' ', $nameParts);

                $info->setFirstName($firstName);
                $info->setLastName($lastName);
            }

            $user->setPassword(uniqid('credentials', true));

            $userRepository->store($user);
            $this->setUser($user);

            /* @var \Core\EventManager\EventManager $events */
            /* @var \Auth\Listener\Events\AuthEvent $event */
            $events = $this->getEventManager();
            $event  = $events->getEvent(AuthEvent::EVENT_USER_REGISTERED, $this);
            $event->setUser($user);
            $events->triggerEvent($event);
        }

        return $this->getUser();
    }

    /**
     * @param InputFilterInterface $filter
     * @param Plugin\Mailer $mailer
     * @param Url $url
     * @return null|User
     * @throws Exception\UserAlreadyExistsException
     */
    public function proceed(InputFilterInterface $filter, Plugin\Mailer $mailer, Url $url)
    {
        $this->setFormFilter($filter);
        $this->setMailer($mailer);
        $this->setUrlPlugin($url);
        if ($this->proceedUser()) {
            $this->proceedMail();
            if (isset($this->user)) {
                return $this->user;
            }
        }
        return null;
    }

    /**
     * @param $name
     * @param $email
     * @param Plugin\Mailer $mailer
     * @param Url $url
     * @return User|null
     */
    public function proceedWithEmail($name, $email, Plugin\Mailer $mailer, Url $url)
    {
        $this->setName($name);
        $this->setEmail($email);
        $this->setMailer($mailer);
        $this->setUrlPlugin($url);
        if ($this->proceedUser()) {
            $this->proceedMail();
            if (isset($this->user)) {
                return $this->user;
            }
        }
        return null;
    }

    /**
     *
     * @since 0.29 Replace call to deprecated setFormattedSubject with setSubject
     */
    public function proceedMail()
    {
        $siteName = $this->options->getSiteName();
        $url = $this->getUrlPlugin();
        $user = $this->getUser();
        if (isset($user)) {
            $confirmationLink = $url->fromRoute(
                'lang/register-confirmation',
                array('userId' => $user->getId()),
                array('force_canonical' => true)
            );

            $userEmail              = $user->getInfo()->getEmail();
            $userName               = $user->getInfo()->getDisplayName();
            $mailService            = $this->getMailService();
            /* @var \Core\Mail\HTMLTemplateMessage $mail */
            $mail                   = $mailService->get('htmltemplate');
            $mail->user             = $user;
            $mail->name             = $userName;
            $mail->confirmationlink = $confirmationLink;
            $mail->siteName         = $siteName;
            $mail->setTemplate('mail/register');
            $mail->setSubject('your registration on %s', $siteName);
            $mail->setTo($userEmail);
            $mailService->send($mail);
        }
    }
}
