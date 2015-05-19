<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013-2015 Cross Solution (http://cross-solution.de)
 * @license       MIT
 */

namespace Auth\Service;

use Auth\Entity\User;
use Auth\Service\Exception;
use Auth\Options\ModuleOptions;
use Core\Controller\Plugin;
use Zend\InputFilter\InputFilterInterface;
use Zend\Mvc\Controller\Plugin\Url;
use Auth\Repository\User as UserRepository;
use Core\Mail\MailService;

/**
 * Class Register
 * @package Auth\Service
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
     * @var \Auth\Options\ModuleOptions
     */
    protected $options;

    /**
     * @var User
     */
    protected $user;

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
    protected function setFormFilter(InputFilterInterface $filter)
    {
        $this->filter = $filter;
        return $this;
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
     * Email-Adress
     * @param $email string
     * @return mixed
     */
    protected function setEmail($email)
    {
        $this->email = $email;
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

            if (($userRepository->findByLoginOrEmail($email))) {
                //return Null;
                throw new Exception\UserAlreadyExistsException('User already exists');
            }

            $user = $userRepository->create(array(
                'login' => $email,
                'role' => User::ROLE_RECRUITER
            ));

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
            $mail                   = $mailService->get('htmltemplate');
            $mail->user             = $user;
            $mail->name             = $userName;
            $mail->confirmationlink = $confirmationLink;
            $mail->siteName         = $siteName;
            $mail->setTemplate('mail/register');
            $mail->setSubject( sprintf( /*@translate*/ 'your registration on %', $siteName));
            $mail->setTo($userEmail);
            $mailService->send($mail);
        }
    }
}
