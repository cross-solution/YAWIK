<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2016 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace Organizations\Controller\Plugin;

use Auth\AuthenticationService;
use Organizations\Repository\Organization as OrganizationRepository;
use Auth\Repository\User as UserRepository;
use Core\Exception\MissingDependencyException;
use Organizations\Entity\EmployeeInterface;
use Zend\Mvc\Controller\Plugin\AbstractPlugin;

/**
 * ${CARET}
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @todo write test
 */
class AcceptInvitationHandler extends AbstractPlugin
{

    const ERROR_ORGANIZATION_NOT_FOUND = 'ErrorOrganizationNotFound';
    const ERROR_TOKEN_INVALID = 'ErrorTokenInvalid';
    const OK_SET_PW = 'OK_SetPw';
    const OK = 'OK';

    protected $organizationRepository;
    protected $userRepository;
    protected $authenticationService;

    /**
     * Sets the authentication service.
     *
     * @param AuthenticationService $authenticationService
     *
     * @return self
     */
    public function setAuthenticationService(AuthenticationService $authenticationService)
    {
        $this->authenticationService = $authenticationService;

        return $this;
    }

    /**
     * Gets the Authentication Service.
     *
     * @return AuthenticationService
     * @throws MissingDependencyException
     */
    public function getAuthenticationService()
    {
        if (!$this->authenticationService) {
            throw new MissingDependencyException('\Auth\AuthenticationService', $this);
        }

        return $this->authenticationService;
    }

    /**
     * Sets the organizations repository.
     *
     * @param OrganizationRepository $organizationRepository
     *
     * @return self
     */
    public function setOrganizationRepository(OrganizationRepository $organizationRepository)
    {
        $this->organizationRepository = $organizationRepository;

        return $this;
    }

    /**
     * Gets the organization repository
     *
     * @return OrganizationRepository
     * @throws MissingDependencyException
     */
    public function getOrganizationRepository()
    {
        if (!$this->organizationRepository) {
            throw new MissingDependencyException('\Organizations\Repository\Organization', $this);
        }

        return $this->organizationRepository;
    }

    /**
     * Sets the user repository.
     *
     * @param UserRepository $userRepository
     *
     * @return self
     */
    public function setUserRepository(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;

        return $this;
    }

    /**
     * Gets the user repository.
     *
     * @return UserRepository
     * @throws MissingDependencyException
     */
    public function getUserRepository()
    {
        if (!$this->userRepository) {
            throw new MissingDependencyException('\Auth\Repository\User', $this);
        }

        return $this->userRepository;
    }




    public function process($token, $organizationId)
    {
        $organizationRepository   = $this->getOrganizationRepository();
        $organization = $organizationRepository->find($organizationId); /* @var $organization \Organizations\Entity\OrganizationInterface */

        if (!$organization) {
            return self::ERROR_ORGANIZATION_NOT_FOUND;
        }

        $userRepository = $this->getUserRepository();
        $user = $userRepository->findByToken($token); /* @var $user \Auth\Entity\User */

        if (!$user) {
            return self::ERROR_TOKEN_INVALID;
        }

        if ($user->isDraft()) {
            $user->setIsDraft(false);
            $user->getInfo()->setEmailVerified(true);
            $mustSetPassword = true;
        } else {
            $mustSetPassword = false;
            $userOrg = $user->getOrganization(); /* @var $userOrg \Organizations\Entity\OrganizationReference */
            if ($userOrg->hasAssociation()) {
                $userEmp = $userOrg->getEmployee($user->getId());
                $userEmp->setStatus(EmployeeInterface::STATUS_UNASSIGNED);
            }
        }

        $employee = $organization->getEmployee($user->getId());
        $employee->setStatus(EmployeeInterface::STATUS_ASSIGNED);
        

        foreach ($organizationRepository->findPendingOrganizationsByEmployee($user->getId()) as $pendingOrg) {
            /* @var $pendingOrg \Organizations\Entity\OrganizationInterface */
            if ($pendingOrg->getId() == $organization->getId()) {
                continue;
            }

            $pendingOrgEmp = $pendingOrg->getEmployee($user->getId());
            if (!$pendingOrgEmp->isUnassigned(/*strict*/ true)) {
                $pendingOrgEmp->setStatus(EmployeeInterface::STATUS_REJECTED);
            }
        }

        $this->getAuthenticationService()->getStorage()->write($user->getId());
        return $mustSetPassword ? self::OK_SET_PW : self::OK;
    }
}
