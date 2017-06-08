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

class JobDescriptionBenefitsStrategy implements StrategyInterface
{
    public function extract($value)
    {
        /* @var \Jobs\Entity\Job $value */
        $result = null;
        if (method_exists($value, 'getTemplateValues')) {
            $result = $value->getTemplateValues()->getBenefits();
        }
        return $result;
    }

    /**
     * @param mixed $value
     * @param null  $object
     */
    public function hydrate($value, $object = null)
    {
        /* @var \Jobs\Entity\Job $object */
        if (isset($value['description-benefits'])) {
            $object->getTemplateValues()->setBenefits($value['description-benefits']);
        }
        return;
    }
}
