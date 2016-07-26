<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
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
