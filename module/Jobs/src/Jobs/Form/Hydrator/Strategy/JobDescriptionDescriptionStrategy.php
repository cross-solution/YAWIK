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

class JobDescriptionDescriptionStrategy implements StrategyInterface
{
    public function extract($value)
    {
        $result = null;
        if (isset($value->templateValues)) {
            $result = $value->templateValues->description;
        }
        return $result;
    }

    public function hydrate($value, $object = null)
    {
        if (isset($value['description-description'])) {
            $object->templateValues->description = $value['description-description'];
        }
        return;
    }
}
