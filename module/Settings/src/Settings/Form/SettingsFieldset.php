<?php

namespace Settings\Form;

use Zend\Form\Fieldset;
use Settings\Entity\SettingsContainerInterface;
use Settings\Entity\ModuleSettingsContainerInterface;
use Settings\Entity\Hydrator\SettingsEntityHydrator;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

//use Zend\InputFilter\InputFilterProviderInterface;

class SettingsFieldset extends Fieldset implements ServiceLocatorAwareInterface
{
    protected $forms;
    protected $isBuild = false;
    protected $labelMap = [];
    
    public function setServiceLocator(ServiceLocatorInterface $serviceLocator)
    {
        $this->forms = $serviceLocator;
        return $this;
    }
    
    public function getServiceLocator()
    {
        return $this->forms;
    }
    
    public function getHydrator()
    {
        if (!$this->hydrator) {
            $hydrator = new SettingsEntityHydrator();
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
        
        $skipProperties = array('_settings', 'isWritable');
        if ($settings instanceof ModuleSettingsContainerInterface) {
            $skipProperties[] = '_module';
        }
        $children = array();
        foreach ($properties as $property) {
            if (in_array($property->getName(), $skipProperties)) {
                continue;
            }
            $property->setAccessible(true);
            $value = $property->getValue($settings);
            if ($value instanceof SettingsContainerInterface) {
                $children[$property->getName()] = $value;
                continue;
            }

            $inputName = $property->getName();
            $inputLabel = isset($this->labelMap[$inputName]) ? $this->labelMap[$inputName] : $inputName;
            $input = array(
                    'name' => $inputName,
                    'options' => array(
                        'label' => $inputLabel
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
        
        foreach ($children as $name => $child) {
            $objectClass  = ltrim(get_class($settings), '\\');
            $moduleName   = substr($objectClass, 0, strpos($objectClass, '\\'));
            $fieldsetName = $moduleName . '/' . ucfirst($name) . 'SettingsFieldset';
            
            if ($this->forms->has($fieldsetName)) {
                $fieldset = $this->forms->get($fieldsetName);
                if (!$fieldset->getHydrator() instanceof SettingsEntityHydrator) {
                    $fieldset->setHydrator($this->getHydrator());
                }
            } else {
                $fieldset = new self();
                $label = preg_replace('~([A-Z])~', ' $1', $name);
                $fieldset->setLabel(ucfirst($label));
            }
            $fieldset->setName($name)
                     ->setObject($child);
            
            
            $this->add($fieldset);
        }
        $this->isBuild = true;
    }
}
