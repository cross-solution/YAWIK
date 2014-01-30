<?php

namespace Settings\Form;

use Core\Entity\Hydrator\AnonymEntityHydrator;
use Zend\Form\Fieldset;
use Settings\Entity\SettingsContainerInterface;
use Settings\Entity\ModuleSettingsContainerInterface;
//use Zend\InputFilter\InputFilterProviderInterface;

class SettingsFieldset extends Fieldset
{
    
    protected $isBuild = false;
    
    public function getHydrator()
    {
        if (!$this->hydrator) {
            $hydrator = new AnonymEntityHydrator();
            $this->setHydrator($hydrator);
        }
        return $this->hydrator;
    }
    
    public function setObject($object)
    {
        parent::setObject($object);
        $this->build();
        return $this;
    }
    
	public function build()
    {
        
        if ($this->isBuild) {
            return;
        }
        
        $settings = $this->getObject();
        $reflection = new \ReflectionClass($settings);
        $properties = $reflection->getProperties();
        
        $skipProperties = array('settings', 'isWritable');
        if ($settings instanceOf ModuleSettingsContainerInterface) {
            $skipProperties[] = 'module';
        }
        $children = array();
        foreach ($properties as $property) {
            if (in_array($property->getName(), $skipProperties)) {
                continue;
            }
            $property->setAccessible(true);
            $value = $property->getValue($settings);
            if ($value instanceOf SettingsContainerInterface) {
                $children[] = $value;
                continue;
            }
            
            $input = array(
                    'name' => $property->getName(),
                    'options' => array(
                        'label' => $property->getName()
                    ),
            );
            if (is_bool($value)) {
                $input['type']= 'Checkbox';
                $input['attributes']['checked'] = $value;
            } else {
                $input['attributes']['value'] = $value;
            }
            $this->add($input);
            
        }
        $this->isBuild = true;
    }
    
    
}