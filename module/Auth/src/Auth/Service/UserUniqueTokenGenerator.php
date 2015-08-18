<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013-2015 Cross Solution (http://cross-solution.de)
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

    public function generate(UserInterface $user, $daysToLive = 1, $storeUser = true)
    {
        $tokenHash = Rand::getString(64, $this->charList);
        $dateStr   = sprintf('+ %d day', $daysToLive);

        $expirationDate = new \Datetime($dateStr);

        /* @todo We should consider using the Prototype Design Pattern here. */
        $token = new Token();
        $token->setHash($tokenHash)
            ->setExpirationDate($expirationDate);

        $user->getTokens()->add($token);

        if ($storeUser) {
            $this->repositoryService->store($user);
        }

        return $tokenHash;
    }
}
