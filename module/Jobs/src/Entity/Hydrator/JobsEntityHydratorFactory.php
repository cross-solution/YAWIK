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

use Core\Entity\Hydrator\EntityHydratorFactory;

/**
 * Class JsonJobsEntityHydratorFactory
 * @package Jobs\Entity\Hydrator
 */
class JobsEntityHydratorFactory extends EntityHydratorFactory
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
