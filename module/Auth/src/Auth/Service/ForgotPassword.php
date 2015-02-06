<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013-2014 Cross Solution (http://cross-solution.de)
 * @license       MIT
 */

namespace Auth\Service;

use Auth\Repository;
use Auth\Service\Exception\UserDoesNotHaveAnEmailException;
use Auth\Service\Exception\UserNotFoundException;
use Core\Controller\Plugin;
use Zend\InputFilter\InputFilterInterface;
use Zend\Mvc\Controller\Plugin\Url;

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
     * @var Auth\Filter\LoginFilter
     */
    private $loginFilter;

    public function __construct(
        Repository\User $userRepository,
        UserUniqueTokenGenerator $tokenGenerator,
        Auth\Filter\LoginFilter $loginFilter
    ) {
        $this->userRepository = $userRepository;
        $this->tokenGenerator = $tokenGenerator;
        $this->loginFilter = $loginFilter;
    }

    public function setSuffix($suffix)
    {
        $this->siuffix = $suffix;
        return $this;
    }

    /**
     * @param InputFilterInterface $filter
     * @param Plugin\Mailer $mailer
     * @param Url $url
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

        $mailer->__invoke(
            'Auth\Mail\ForgotPassword',
            array(
                'user' => $user,
                'resetLink' => $resetLink
            ),
            true
        );
    }
}

