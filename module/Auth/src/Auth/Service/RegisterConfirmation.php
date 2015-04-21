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
use Zend\Authentication\AuthenticationService;

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

    public function __construct(Repository\User $userRepository, AuthenticationService $authenticationService)
    {
        $this->userRepository = $userRepository;
        $this->authenticationService = $authenticationService;
    }

    public function proceed($userId)
    {
        /** @var User $user */
        if (!($user = $this->userRepository->find($userId))) {
            throw new Exception\UserNotFoundException('User cannot be found');
        }

        $user->getInfo()->setEmailVerified(true);
        $this->userRepository->store($user);
        $this->authenticationService->getStorage()->write($user->getId());
    }
}