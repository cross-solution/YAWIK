<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013-2016 Cross Solution (http://cross-solution.de)
 * @author Carsten Bleek <bleek@cross-solution.de>
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @author Miroslav Fedeleš <miroslav.fedeles@gmail.com>
 * @license   MIT
 */

/** */
namespace Organizations\Controller\Plugin;

use Organizations\Exception\MissingParentOrganizationException;
use Zend\Mvc\Controller\Plugin\AbstractPlugin;
use Core\Repository\RepositoryService;
use Auth\AuthenticationService;
use Auth\Exception\UnauthorizedAccessException;
use Zend\Mvc\Controller\Plugin\Params;
use Acl\Controller\Plugin\Acl;
use Core\Entity\Exception\NotFoundException;

/**
 * Class GetOrganizationHandler
 *
 * @package Organization\Controller\Plugin
 */
class GetOrganizationHandler extends AbstractPlugin
{

    /**
     * @var RepositoryService
     */
    protected $repositoryService;

    /**
     * @var AuthenticationService
     */
    protected $auth;

    /**
     * @var \Acl\Controller\Plugin\Acl
     */
    protected $acl;

    public function __construct(RepositoryService $repositoryService, AuthenticationService $auth, Acl $acl)
    {
        $this->repositoryService=$repositoryService;
        $this->auth=$auth;
        $this->acl=$acl;
    }

    public function __invoke()
    {
        return $this;
    }

    /**
     * @param Params $params
     * @param bool   $allowDraft
     *
     * @return object|\Organizations\Entity\Organization
     * @throws UnauthorizedAccessException
     * @throws \Doctrine\ODM\MongoDB\LockException
     * @throws NotFoundException
     */
    public function process(Params $params, $allowDraft = true)
    {
        $repositories   = $this->repositoryService;
        /* @var \Organizations\Repository\Organization $organizationRepository */
        $organizationRepository = $this->repositoryService->get('Organizations/Organization');

        $idFromRoute = $params('id', 0);
        $idFromSubForm = $params()->fromPost('id', 0);
        $user = $this->auth->getUser(); /* @var $user \Auth\Entity\UserInterface */

        /* @var $organizationId string */
        $organizationId = empty($idFromRoute)?$idFromSubForm:$idFromRoute;

        $editOwnOrganization = '__my__' === $organizationId;

        if ($editOwnOrganization) {
            /* @var $userOrg \Organizations\Entity\OrganizationReference */
            $userOrg = $user->getOrganization();
            if ($userOrg->hasAssociation() && !$userOrg->isOwner()) {
                throw new UnauthorizedAccessException('You may not edit this organization as you are only employer.');
            }
            $organizationId = $userOrg->hasAssociation() ? $userOrg->getId() : 0;
        }

        if (empty($organizationId) && $allowDraft) {
            /* @var $organization \Organizations\Entity\Organization */
            $organization = $organizationRepository->findDraft($user);
            if (empty($organization)) {
                $organization = $organizationRepository->create();
                $organization->setIsDraft(true);
                $organization->setUser($user);
                if (!$editOwnOrganization) {
                    /* @var $parent \Organizations\Entity\OrganizationReference */
                    $parent = $user->getOrganization();
                    if (!$parent->hasAssociation()) {
                        throw new MissingParentOrganizationException('You cannot create organizations, because you do not belong to a parent organization. Use "User menu -> create my organization" first.');
                    }
                    $organization->setParent($parent->getOrganization());
                }

                $repositories->store($organization);
            }
            return $organization;
        }

        $organization      = $organizationRepository->find($organizationId);
        if (!$organization) {
            throw new NotFoundException($organizationId);
        }

        $this->acl->check($organization, 'edit');

        return $organization;
    }
}
