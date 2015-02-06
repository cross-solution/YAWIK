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
use Auth\Repository;
use Auth\Service\Exception;
use Core\Controller\Plugin;
use Zend\InputFilter\InputFilterInterface;
use Zend\Mvc\Controller\Plugin\Url;

class Register
{
    /**
     * @var Repository\User
     */
    private $userRepository;
    protected $filter;
    protected $name;
    protected $email;
    protected $mailer;
    protected $user;

    public function __construct(Repository\User $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    protected function setFormFilter(InputFilterInterface $filter)
    {
        $this->filter = $filter;
        return $this;
    }

    protected function setMailer(Plugin\Mailer $mailer)
    {
        $this->mailer = $mailer;
        return $this;
    }

    protected function getMailer()
    {
        return $this->mailer;
    }

    protected function setUser($user)
    {
        $this->user = $user;
        return $this;
    }

    protected function getUser()
    {
        return $this->user;
    }

    protected function setUrlPlugin($urlPlugin)
    {
        $this->urlPlugin = $urlPlugin;
        return $this;
    }

    protected function getUrlPlugin()
    {
        return $this->urlPlugin;
    }


    protected function extractUserMailFromFormular()
    {
        if (!isset($this->filter)) {
            throw new \InvalidArgumentException('No Form set');
        }
        if (!$this->filter->isValid()) {
            throw new \LogicException('Form is not valid');
        }

        $registerFilter = $this->filter->get('register');

        $this->name = $registerFilter->getValue('name');
        $this->email = $registerFilter->getValue('email');

        return $this;
    }

    protected function getName()
    {
        if (!isset($this->name)) {
            $this->extractUserMailFromFormular();
        }
        return $this->name;
    }

    protected function setName($name)
    {
        $this->name = $name;
        return $this->name;
    }

    protected function getEmail()
    {
        if (!isset($this->email)) {
            $this->extractUserMailFromFormular();
        }
        return $this->email;
    }

    protected function setEmail($email)
    {
        $this->email = $email;
        return $this->email;
    }

    protected function proceedUser()
    {
        if (!isset($this->user)) {
            $name = $this->getName();
            $email = $this->getEmail();

            if (($user = $this->userRepository->findByLoginOrEmail($email))) {
                throw new Exception\UserAlreadyExistsException('User already exists');
            }

            $user = $this->userRepository->create(array(
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

            $this->userRepository->store($user);
            $this->setUser($user);
        }

        return $this->getUser();
    }

    /**
     * @param InputFilterInterface $filter
     * @param Plugin\Mailer $mailer
     * @param Url $url UrlPlugin
     * @throws Exception\UserAlreadyExistsException
     */
    public function proceed(InputFilterInterface $filter, Plugin\Mailer $mailer, Url $url)
    {
        $this->setFormFilter($filter);
        $this->setMailer($mailer);
        $this->setUrlPlugin($url);
        $this->proceedUser();
        $this->proceedMail();
        if (isset($this->user)) {
            return $this->user;
        }
        return null;
    }

    public function proceedWithEmail($name, $email, Plugin\Mailer $mailer, Url $url)
    {
        $this->setName($name);
        $this->setEmail($email);
        $this->setMailer($mailer);
        $this->setUrlPlugin($url);
        $this->proceedUser();
        $this->proceedMail();
        if (isset($this->user)) {
            return $this->user;
        }
        return null;
    }

    public function proceedMail()
    {
        $mailer = $this->getMailer();
        $url = $this->getUrlPlugin();
        $user = $this->getUser();

        $confirmationLink = $url->fromRoute(
                                'lang/register-confirmation',
                                    array('userId' => $user->getId()),
                                    array('force_canonical' => true)
        );

        $mailer->__invoke(
               'Auth\Mail\RegisterConfirmation',
                   array(
                       'user' => $user,
                       'confirmationLink' => $confirmationLink
                   ),
                   true
        );

    }
}