<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013-2014 Cross Solution (http://cross-solution.de)
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

    public function __construct(Repository\User $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function proceed(InputFilterInterface $filter, Plugin\Mailer $mailer, Url $url)
    {
        if (!$filter->isValid()) {
            throw new \LogicException('Form is not valid');
        }

        $registerFilter = $filter->get('register');

        $name = $registerFilter->getValue('name');
        $email = $registerFilter->getValue('email');

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