<?php
/**
 * YAWIK
 * 
 * @filesource
 * @copyright (c) 2013-2014 Cross Solution (http://cross-solution.de)
 * @license   MIT
 * @author    weitz@cross-solution.de
 */

namespace Jobs\Form\Hydrator\Strategy;

use Zend\Stdlib\Hydrator\Strategy\StrategyInterface;

class JobDescriptionRequirementsStrategy implements StrategyInterface
{
    public function extract($value) {
        $result = Null;
        if (isset($value->description)) {
            $result = $value->templateValues->requirements;
        }
        return $result;
    }

    public function hydrate($value, $object = Null) {
        if (isset($value['description-requirements'])) {
            $object->templateValues->requirements = $value['description-requirements'];
        }
        return;
    }
}