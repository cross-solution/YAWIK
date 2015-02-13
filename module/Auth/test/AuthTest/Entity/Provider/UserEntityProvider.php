<?php

namespace AuthTest\Entity\Provider;

use Auth\Entity\Info;
use Auth\Entity\User;

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

        return $userEntity;
    }
}