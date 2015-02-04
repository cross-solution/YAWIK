<?php
/**
 * YAWIK
 * 
 * @filesource
 * @copyright (c) 2013-2015 Cross Solution (http://cross-solution.de)
 * @license   MIT
 * @author    weitz@cross-solution.de
 */

namespace Jobs\Form\Hydrator\Strategy;

use Zend\Stdlib\Hydrator\Strategy\StrategyInterface;

class JobDescriptionDescriptionStrategy implements StrategyInterface
{
    public function extract($value) {
        $result = Null;
        if (isset($value->templateValues)) {
            $result = $value->templateValues->description;
        }
        return $result;
    }

    public function hydrate($value, $object = Null) {
        if (isset($value['description-description'])) {
            $object->templateValues->description = $value['description-description'];
        }
        return;
    }
}