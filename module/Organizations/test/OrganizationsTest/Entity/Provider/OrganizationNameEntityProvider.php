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

use Organizations\Entity\OrganizationName;

class OrganizationNameEntityProvider
{
    /**
     * @param array $params
     *
     * @return OrganizationName
     */
    public static function createEntityWithRandomData(array $params = array())
    {
        $withId = true;
        $entityId = bin2hex(substr(uniqid(), 1));
        $name = uniqid('name');
        $rankingByCompany = mt_rand(1, 100);

        extract($params);

        $entity = new OrganizationName();
        $entity->setName($name);
        $entity->setRankingByCompany($rankingByCompany);

        if ($withId) {
            $entity->setId($entityId);
        }

        return $entity;
    }
}
