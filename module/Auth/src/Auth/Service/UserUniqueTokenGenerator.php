<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013-2014 Cross Solution (http://cross-solution.de)
 * @license       MIT
 */

namespace Auth\Service;

use Auth\Entity\Token;
use Auth\Entity\UserInterface;
use Core\Repository\RepositoryService;
use Zend\Math\Rand;

class UserUniqueTokenGenerator
{
    private $charList = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUWXYZ1234567890';

    /**
     * @var RepositoryService
     */
    private $repositoryService;

    public function __construct(RepositoryService $repositoryService)
    {
        $this->repositoryService = $repositoryService;
    }

    public function generate(UserInterface $user)
    {
        $tokenHash = Rand::getString(64, $this->charList);

        $expirationDate = new \Datetime();
        $expirationDate->modify('+1 day');

        $token = new Token();
        $token->setHash($tokenHash)
            ->setExpirationDate($expirationDate);

        $user->getTokens()->add($token);

        $this->repositoryService->store($user);

        return $tokenHash;
    }
}