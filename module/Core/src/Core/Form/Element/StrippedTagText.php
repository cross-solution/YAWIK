<?php
/**
 * YAWIK
 * 
 * @filesource
 * @copyright (c) 2013-2015 Cross Solution (http://cross-solution.de)
 * @license   MIT
 * @author    weitz@cross-solution.de
 */

namespace Core\Form\Element;

use Zend\Form\Element\Text;
use Zend\InputFilter\InputProviderInterface;
use Zend\Stdlib\ArrayUtils;

class StrippedTagText extends Text implements InputProviderInterface
{
    public function __construct($name = null, $options = array())
    {
        parent::__construct($name, $options);
    }

    public function getInputSpecification()
    {
        $specs = array(
            'name' => $this->getName(),
            'filters' => array(
                array('name' => 'Core/StripTags'),
            ),
        );
        return $specs;
    }
}
