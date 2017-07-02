<?php

namespace Settings\Form;

use Zend\Form\Fieldset;
use Settings\Entity\SettingsContainerInterface;
use Settings\Entity\ModuleSettingsContainerInterface;
use Settings\Entity\Hydrator\SettingsEntityHydrator;
use Zend\Form\FormElementManager\FormElementManagerV3Polyfill as FormElementManager;

class SettingsFieldset extends Fieldset
{
    /**
     * @var FormElementManager
     */
    protected $formManager;
    
    protected $isBuild = false;
    protected $labelMap = [];
    
    /**
     * @param FormElementManager $formManager
     */
    public function __construct(FormElementManager $formManager)
    {
        parent::__construct();
        $this->formManager = $formManager;
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
            if (in_array($property->getName(), $skipProperties) || $this->has($property->getName())) {
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

            if (is_array($inputLabel)){
                $priority = isset($inputLabel[1])?$inputLabel[1]:0;
                $inputLabel = $inputLabel[0];
            }else{
                $priority = 0;
            }

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
            $this->add($input,['priority'=>$priority]);
            
        }
        
        foreach ($children as $name => $child) {
            $objectClass  = ltrim(get_class($settings), '\\');
            $moduleName   = substr($objectClass, 0, strpos($objectClass, '\\'));
            $fieldsetName = $moduleName . '/' . ucfirst($name) . 'SettingsFieldset';
            
            if ($this->formManager->has($fieldsetName)) {
                $fieldset = $this->formManager->get($fieldsetName);
                if (!$fieldset->getHydrator() instanceof SettingsEntityHydrator) {
                    $fieldset->setHydrator($this->getHydrator());
                }
            } else {
                $fieldset = new self($this->formManager);
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
