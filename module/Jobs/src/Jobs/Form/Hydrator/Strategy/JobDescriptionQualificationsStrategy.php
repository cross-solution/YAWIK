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

class JobDescriptionQualificationsStrategy implements StrategyInterface
{
    public function extract($value) {
        $result = Null;
        if (isset($value->templateValues)) {
            $result = $value->templateValues->qualifications;
        }
        return $result;
    }

    public function hydrate($value, $object = Null) {
        if (isset($value['description-qualifications'])) {
            $object->templateValues->qualifications = $value['description-qualifications'];
        }
        return;
    }
}