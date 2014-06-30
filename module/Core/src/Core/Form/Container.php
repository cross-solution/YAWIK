<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013-2014 Cross Solution (http://cross-solution.de)
 * @license   AGPLv3
 */

/**  */ 
namespace Core\Form;

use Zend\Form\Element;
use Zend\Form\FormInterface;
use Zend\Form\Form;
use Core\Entity\Hydrator\EntityHydrator;
/**
 *
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 */
class Container extends Form
{
    protected $wrapElements = true;
    protected $deferredItems = array();
    protected $areDeferredItemsAdded = false;
    protected $params = array();
    
    public function setParams(array $params)
    {
        $this->params = $params;
        return $this;
    }
    
    public function getParams()
    {
        return $this->params;
    }
    
    public function getHydrator() {
        if (!$this->hydrator) {
            $hydrator = new EntityHydrator();
            //$this->addHydratorStrategies($hydrator);
            $this->setHydrator($hydrator);
        }
        return $this->hydrator;
    }
    
    public function addLazy($formOrContainer, $flags = array())
    {
        if (!is_array($flags)) {
            $flags = array('name' => $flags);
        }
        if (!isset($flags['name'])) {
            throw new \InvalidArgumentException('No name provided.');
        }
        if (!is_array($formOrContainer)) {
            $formOrContainer = array ('type' => $formOrContainer);
        }
        
        $this->deferredItems[$flags['name']] = array($formOrContainer, $flags);
        return $this; 
    }
    
    public function add($formOrContainer, array $flags = array())
    {
        if (is_array($formOrContainer)
        || ($formOrContainer instanceof Traversable && !$formOrContainer instanceof ElementInterface)
        ) {
            $factory = $this->getFormFactory();
            $formOrContainer = $factory->create($formOrContainer);
        }
        
        if (!$formOrContainer instanceOf FormInterface) {
            throw new \InvalidArgumentException('Container must only contain other Containers or Forms.');
        }
        
        parent::add($formOrContainer, $flags);
        return $this;
    }
    
    public function setName($name)
    {
        parent::setName($name);
        $name = str_replace(array('[', ']'), array('.', ''), $name);
        $this->setAttribute('data-container', $name);
        return $this;
    }
    
    public function get($elementOrFieldset)
    {
        if (false !== strpos($elementOrFieldset, '.')) {
            list($child, $name) = explode('.', $elementOrFieldset, 2);
            $container = $this->get($child);
            return $container->get($name);
        } 
        
        if (!$this->has($elementOrFieldset)) {
            if (isset($this->deferredItems[$elementOrFieldset])) {
                $item = $this->deferredItems[$elementOrFieldset];
                $this->add($item[0], $item[1]);
            }
        }
        return parent::get($elementOrFieldset);
    }
    
    public function count()
    {
        $this->addDeferredItems();
        return $this->count();
    }
    
    public function getIterator()
    {
        $this->addDeferredItems();
        return parent::getIterator();
    }
    
    protected function addDeferredItems()
    {
        foreach ($this->deferredItems as $name => $spec) {
            if (!$this->has($name)) {
                $this->add($spec[0], $spec[1]);
                if (null !== $this->object) {
                    try {
                        $value = $this->object->$name;
                        $this->get($name)->bind($value);
                    } catch (\OutOfBoundsException $e) {}
                }
            }
        }
    }
    
    public function prepareElement(FormInterface $form)
    {
        $this->addDeferredItems();
        return parent::prepareElement($form);
    }
}
