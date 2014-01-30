<?php
/**
 * Cross Applicant Management
 *
 * @filesource
 * @copyright (c) 2013 Cross Solution (http://cross-solution.de)
 * @license   AGPLv3
 */

/** AbstractSettingsForm.php */ 
namespace Settings\Form;

use Zend\Form\Form;
use Core\Entity\Hydrator\EntityHydrator;
use Settings\Entity\SettingsContainerInterface;
use Settings\Entity\ModuleSettingsContainerInterface;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

abstract class AbstractSettingsForm extends Form implements ServiceLocatorAwareInterface
{
    
    protected $isBuild = false;
    protected $forms;
    
    public function __construct()
    {
        $this->setAttribute('method', 'post');
    }
 
    /* (non-PHPdoc)
     * @see \Zend\ServiceManager\ServiceLocatorAwareInterface::setServiceLocator()
     */
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
            $hydrator = new EntityHydrator();
            $this->setHydrator($hydrator);
        }
        return $this->hydrator;
    }
    
    public function init()
    {
        $baseFieldset = $this->forms->has('');
    }
    
    public function setObject($object)
    {
        if (!$object instanceOf ModuleSettingsContainerInterface) {
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
        $urlHelper = $this->forms->getServiceLocator()
                     ->get('ViewHelperManager')
                     ->get('url');
        
        $url = $urlHelper('lang/settings/form', array('module' => $name), true);
        $this->setAttribute('action', $url);
    }   

    
    public function bind($object)
    {
        /** Ensure the form is build prior to binding */
        $this->setObject($object);
        return parent::bind($object);
    }
    
    public function build()
    {
        $settings = $this->getObject();
        $this->setAttribute('method', 'post');
        
        
        
    }
    
    protected function getModuleName()
    {
        $refl       = new \ReflectionClass($this);
        $namespace  = ltrim($refl->getNamespaceName(), '\\');
        $moduleName = substr($namespace, 0, strpos($namespace, '\\'));

        return strtolower($moduleName);
    }
}

