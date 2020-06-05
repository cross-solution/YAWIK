<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright https://yawik.org/COPYRIGHT.php
 * @license   MIT
 * @author    weitz@cross-solution.de
 */

namespace Jobs\Entity\Hydrator;

use Core\Entity\Hydrator\JsonEntityHydratorFactory;

/**
 * Class JsonJobsEntityHydratorFactory
 * @package Jobs\Entity\Hydrator
 */
class JsonJobsEntityHydratorFactory extends JsonEntityHydratorFactory
{
    /**
     *
     */
    protected function prepareHydrator()
    {
        $this->hydrator->setExcludeMethods(array('user', 'applications', 'termsAccepted', 'atsEnabled', 'permissions'));
        ;
    }
}
