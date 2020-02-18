<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license       MIT
 */

namespace Auth\Service;

use Auth\Entity\Token;
use Auth\Entity\UserInterface;
use Auth\Repository;
use Auth\Service\Exception\TokenExpirationDateExpiredException;
use Auth\Service\Exception\UserNotFoundException;
use Core\Controller\Plugin;
use Core\Repository\RepositoryService;
use Laminas\Authentication\AuthenticationService;

class GotoResetPassword
{
    /**
     * @var RepositoryService
     */
    private $repositoryService;

    /**
     * @var AuthenticationService
     */
    private $authenticationService;

    /**
     * @var Repository\User
     */
    private $userRepository;

    public function __construct(RepositoryService $repositoryService, AuthenticationService $authenticationService)
    {
        $this->repositoryService = $repositoryService;
        $this->authenticationService = $authenticationService;

        $this->userRepository = $repositoryService->get('Auth/User');
    }

    public function proceed($userId, $tokenHash)
    {
        /** @var UserInterface $user */
        $user = $this->userRepository->findOneBy(
            array(
                'id' => new \MongoId($userId),
                'tokens.hash' => $tokenHash
            )
        );

        if (!$user) {
            throw new UserNotFoundException('User or token does not exists');
        }

        $this->checkAllTokens($user, $tokenHash);

        $this->authenticationService->getStorage()->write($user->getId());
    }

    private function checkAllTokens(UserInterface $user, $tokenHash)
    {
        $now = new \DateTime();
        $tokensToRemove = array();
        $throwExpirationDateException = false;

        /** @var Token $token */
        foreach ($user->getTokens() as $key => $token) {
            if ($token->getExpirationDate() < $now) {
                $tokensToRemove[$key] = $token;
            }

            if ($token->getHash() == $tokenHash) {
                if ($token->getExpirationDate() < $now) {
                    $throwExpirationDateException = true;
                }
            }
        }

        if (!empty($tokensToRemove)) {
            foreach ($tokensToRemove as $key => $token) {
                $user->getTokens()->remove($key);
                $this->repositoryService->remove($token);
            }
        }

        if ($throwExpirationDateException) {
            throw new TokenExpirationDateExpiredException();
        }
    }
}
