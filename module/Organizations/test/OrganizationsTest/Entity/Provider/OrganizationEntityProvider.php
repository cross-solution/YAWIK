<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

namespace OrganizationsTest\Entity\Provider;

use PHPUnit\Framework\TestCase;

use Organizations\Entity\Organization;

class OrganizationEntityProvider
{
    /**
     * @param array $params
     *
     * @return Organization
     */
    public static function createEntityWithRandomData(array $params = array())
    {
        $params = static::createNewRelations($params);

        $withId = true;
        $entityId = bin2hex(substr(uniqid(), 1));
        // define here another variables

        extract($params);

        $organizationEntity = new Organization();
        // here set another variables

        if (!empty($organizationName)) {
            $organizationEntity->setOrganizationName($organizationName);
        }

        if ($withId) {
            $organizationEntity->setId($entityId);
        }

        return $organizationEntity;
    }

    private static function createNewRelations(array $params = array())
    {
        extract($params);

        if (!empty($createOrganizationName)) {
            $organizationName = OrganizationNameEntityProvider::createEntityWithRandomData((array)$createOrganizationName);
        }

        return array_merge($params, compact('organizationName'));
    }
}
