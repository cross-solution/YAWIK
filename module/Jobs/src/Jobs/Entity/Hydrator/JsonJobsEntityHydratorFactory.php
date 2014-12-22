<?php
/**
 * YAWIK
 * 
 * @filesource
 * @copyright (c) 2013-2015 Cross Solution (http://cross-solution.de)
 * @license   MIT
 * @author    weitz@cross-solution.de
 */

namespace Jobs\Entity\Hydrator;

use Core\Entity\Hydrator\JsonEntityHydratorFactory;

class JsonJobsEntityHydratorFactory extends JsonEntityHydratorFactory
{
    protected function prepareHydrator() {
        $this->hydrator->setExcludeMethods(array('user', 'applications', 'termsAccepted', 'camEnabled', 'permissions', 'portals'));;
    }
}