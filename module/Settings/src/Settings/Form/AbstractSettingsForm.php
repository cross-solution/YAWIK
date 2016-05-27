<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

/** AbstractSettingsForm.php */
namespace Settings\Form;

use Core\Form\Form;
use Settings\Entity\ModuleSettingsContainerInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Form\FormInterface;
use Settings\Entity\Hydrator\SettingsEntityHydrator;

class AbstractSettingsForm extends Form
{
    /**
     * @var bool
     */
    protected $isBuild = false;
    
    /**
     * @var ServiceLocatorInterface
     */
    protected $formManager;
    
    /**
     * @param ServiceLocatorInterface $formManager
     */
    public function __construct(ServiceLocatorInterface $formManager)
    {
        parent::__construct();
        $this->formManager = $formManager;
    }
    
    /**
     * @see \Core\Form\Form::getHydrator()
     */
    public function getHydrator()
    {
        if (!$this->hydrator) {
            $hydrator = new SettingsEntityHydrator();
            $this->setHydrator($hydrator);
        }
        return $this->hydrator;
    }
    
    public function build()
    {
        if ($this->isBuild) {
            return;
        }
        $this->setAttribute('method', 'post');
        $object = $this->getObject();
        $fieldsetName = $object->getModuleName() . '/SettingsFieldset';
        
        if ($this->formManager->has($fieldsetName)) {
            $fieldset = $this->formManager->get($fieldsetName);
        } else {
            $fieldset = $this->formManager->get('Settings/Fieldset');
            $fieldset->setLabel($object->getModuleName());
        }
        
        $fieldset->setUseAsBaseFieldset(true)
                 ->setName('base');
        
        $fieldset->setObject($object);
        $this->add($fieldset);
        
        $this->add($this->formManager->get('DefaultButtonsFieldset'));
        $this->isBuild=true;
    }
        

    public function setObject($object)
    {
        if (!$object instanceof ModuleSettingsContainerInterface) {
            throw new \InvalidArgumentException('Object must implement ModuleSettingsContainerInterface');
        }
        parent::setObject($object);
        $moduleName = $object->getModuleName();
        $this->setName($moduleName);
        $this->build();
        return $this;
    }
    
    public function setName($name)
    {
        parent::setName(strtolower($name) . '-settings');
        $urlHelper = $this->formManager->getServiceLocator()
                     ->get('ViewHelperManager')
                     ->get('url');
        
        $url = $urlHelper('lang/settings', array('module' => $name), true);
        $this->setAttribute('action', $url);
    }

    
    public function bind($object, $flags = FormInterface::VALUES_NORMALIZED)
    {
        /** Ensure the form is build prior to binding */
        $this->setObject($object);
        return parent::bind($object);
    }
    
    
    protected function getModuleName()
    {
        $refl       = new \ReflectionClass($this);
        $namespace  = ltrim($refl->getNamespaceName(), '\\');
        $moduleName = substr($namespace, 0, strpos($namespace, '\\'));

        return strtolower($moduleName);
    }
    
    /**
     * @param ServiceLocatorInterface $formManager
     * @return AbstractSettingsForm
     */
    public static function factory(ServiceLocatorInterface $formManager)
    {
        return new static($formManager);
    }
}
