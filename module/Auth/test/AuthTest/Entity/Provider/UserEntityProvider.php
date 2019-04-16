<?php

namespace AuthTest\Entity\Provider;

use PHPUnit\Framework\TestCase;

use Auth\Entity\Info;
use Auth\Entity\User;
use Organizations\Entity\OrganizationReference;

class UserEntityProvider
{
    /**
     * @param array $params
     *
     * @return User
     */
    public static function createEntityWithRandomData(array $params = array())
    {
        $withId = true;
        $entityId = bin2hex(substr(uniqid(), 1));
        $email = uniqid('email');
        $login = uniqid('login');
        $password = uniqid('password');
        $role = User::ROLE_RECRUITER;
        extract($params);

        $userEntity = new User();
        $userEntity->setEmail($email)
            ->setLogin($login)
            ->setPassword($password)
            ->setRole($role);

        $infoEntity = new Info();
        $infoEntity->setEmail($email);

        $userEntity->setInfo($infoEntity);

        if ($withId) {
            $userEntity->setId($entityId);
        }

        $organization = new OrganizationReferenceMock();

        $userEntity->setOrganization($organization);

        return $userEntity;
    }
}

class OrganizationReferenceMock extends \Organizations\Entity\OrganizationReference
{
    protected $isOwner = false;
    protected $isEmployee = false;
    protected $hasAssociation = false;

    public function __construct()
    {
    }

    public function isOwner()
    {
        return $this->hasAssociation && $this->isOwner;
    }

    public function isEmployee()
    {
        return $this->hasAssociation && !$this->isOwner;
    }

    public function hasAssociation()
    {
        return $this->hasAssociation;
    }
}
