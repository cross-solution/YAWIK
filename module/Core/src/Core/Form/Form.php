<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 Cross Solution (http://cross-solution.de)
 * @license   AGPLv3
 */

namespace Core\Form;

use Zend\Form\Form as ZendForm;
use Zend\Form\FieldsetInterface;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;
use Zend\InputFilter\InputFilterProviderInterface;
use Zend\InputFilter\InputProviderInterface;
use Core\Entity\Hydrator\EntityHydrator;

class Form extends ZendForm
{
    public function getHydrator() {
        if (!$this->hydrator) {
            $hydrator = new EntityHydrator();
            $this->addHydratorStrategies($hydrator);
            $this->setHydrator($hydrator);
        }
        return $this->hydrator;
    }
    
    protected function addHydratorStrategies($hydrator)
    { }
    
}