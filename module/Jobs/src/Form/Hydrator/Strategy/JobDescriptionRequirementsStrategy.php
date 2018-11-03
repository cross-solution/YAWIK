<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 * @author    weitz@cross-solution.de
 */

namespace Jobs\Form\Hydrator\Strategy;

use Zend\Hydrator\Strategy\StrategyInterface;

class JobDescriptionRequirementsStrategy implements StrategyInterface
{
    public function extract($value)
    {
        /* @var \Jobs\Entity\Job $value */
        $result = null;
        if (method_exists($value, 'getTemplateValues')) {
            $result = $value->getTemplateValues()->getRequirements();
        }
        return $result;
    }

    public function hydrate($value, $object = null)
    {
        /* @var \Jobs\Entity\Job $object */
        if (isset($value['description-requirements'])) {
            $object->getTemplateValues()->setRequirements($value['description-requirements']);
        }
        return;
    }
}
