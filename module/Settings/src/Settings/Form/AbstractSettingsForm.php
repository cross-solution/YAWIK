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
use Interop\Container\ContainerInterface;
use Settings\Entity\ModuleSettingsContainerInterface;
use Zend\Form\FormElementManager\FormElementManagerV3Polyfill as FormElementManager;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Form\FormInterface;
use Settings\Entity\Hydrator\SettingsEntityHydrator;
use Zend\View\HelperPluginManager;

class AbstractSettingsForm extends Form
{
    /**
     * @var bool
     */
    protected $isBuild = false;
	
	/**
	 * @var FormElementManager
	 */
    protected $formManager;
	
	/**
	 * @var array|HelperPluginManager
	 */
    protected $viewHelper;
	
	/**
	 * AbstractSettingsForm constructor.
	 *
	 * @param FormElementManager $formManager
	 * @param HelperPluginManager $viewHelper
	 */
    public function __construct(
    	FormElementManager $formManager,
	    HelperPluginManager $viewHelper
    )
    {
        parent::__construct();
        $this->formManager = $formManager;
        $this->viewHelper = $viewHelper;
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
        
        $this->add([
        	'type' => 'DefaultButtonsFieldset'
        ]);
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
        $urlHelper = $this->viewHelper->get('url');
        
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
	 * @param ContainerInterface $container
	 *
	 * @return AbstractSettingsForm
	 */
    public static function factory(ContainerInterface $container)
    {
        return new static(
        	$container->get('FormElementManager'),
	        $container->get('ViewHelperManager')
        );
    }
}
