<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

namespace JobsTest\Entity\Provider;

use PHPUnit\Framework\TestCase;

use Jobs\Entity\Job;
use OrganizationsTest\Entity\Provider\OrganizationEntityProvider;

class JobEntityProvider
{
    /**
     * @param array $params
     *
     * @return Job
     */
    public static function createEntityWithRandomData(array $params = array())
    {
        $params = static::createNewRelations($params);

        $withId = true;
        $entityId = bin2hex(substr(uniqid(), 1));
        // define here another variables

        extract($params);

        $entity = new Job();
        // here set another variables

        if (isset($organization)) {
            $entity->setOrganization($organization);
        }

        if ($withId) {
            $entity->setId($entityId);
        }

        return $entity;
    }

    private static function createNewRelations(array $params = array())
    {
        extract($params);

        if (!empty($createOrganization)) {
            $organization = OrganizationEntityProvider::createEntityWithRandomData((array)$createOrganization);
        }

        return array_merge($params, compact('organization'));
    }
}
