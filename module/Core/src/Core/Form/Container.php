<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013-2014 Cross Solution (http://cross-solution.de)
 * @license   AGPLv3
 */

/** Core forms */ 
namespace Core\Form;

use Zend\Form\Element;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Core\Entity\EntityInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Manages a group of formulars.
 * 
 * The container is responsible for creating, populating and binding the formulars from or to
 * the corresponding entities.
 * 
 * Formulars are lazy loaded. So it is possible to only retrieve one formular from the container
 * for asynchronous saving using ajax calls.
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 */
class Container extends Element implements ServiceLocatorAwareInterface,
                                           \IteratorAggregate,
                                           \Countable
{
    /**
     * Available/Loaded forms or specification.
     * @var array
     */
    protected $forms = array();
    
    /**
     * Active formulars keys.
     * 
     * Formulars which key is herein are included in the iterator.
     * @see getIterator()
     * @var array
     */
    protected $activeForms = array();
    
    /**
     * The form element manager.
     * @var \Zend\Form\FormElementManager
     */
    protected $formElementManager;
    
    /**
     * Entity to bind to the formulars.
     * 
     * @var \Core\Entity\EntityInterface
     */
    protected $entity;
    
    /**
     * Parameters to pass to the formulars.
     * 
     * @var array
     */
    protected $params = array();

    /**
     * {@inheritDoc}
     * @return Container
     * @see \Zend\ServiceManager\ServiceLocatorAwareInterface::setServiceLocator()
     */
    public function setServiceLocator(ServiceLocatorInterface $serviceLocator)
    {
        $this->formElementManager = $serviceLocator;
        return $this;
    }
    
    /**
     * {@inheritDoc}
     * @see \Zend\ServiceManager\ServiceLocatorAwareInterface::getServiceLocator()
     */
    public function getServiceLocator()
    {
        return $this->formElementManager;
    }
    
    /**
     * Gets an iterator to iterate over the enabled formulars.
     * 
     * @return \ArrayIterator
     * @see IteratorAggregate::getIterator()
     */
    public function getIterator()
    {
        $self = $this;
        $forms = array_map(function($key) use ($self) {
            return $self->getForm($key);
        }, $this->activeForms);
        
        return new \ArrayIterator($forms);
    }
    
    /**
     * Gets the count of enabled formulars
     * 
     * @return int
     * @see Countable::count()
     */
    public function count()
    {
        return count($this->activeForms);
    }
    
    /**
     * Sets formular parameters.
     * 
     * @param array $params
     * @return \Core\Form\Container
     */
    public function setParams(array $params)
    {
        $this->params = array_merge($this->params, $params);
        
        foreach ($this->forms as $form) {
            if (isset($form['__instance__']) && is_object($form['__instance__'])) {
                $form['__instance__']->setParams($params);
            }
        }
        return $this;
    }
    
    /**
     * Gets the formular parameters.
     * 
     * @return array:
     */
    public function getParams()
    {
        return $this->params;
    }
    
    /**
     * Sets a formular parameter.
     * 
     * @param string $key
     * @param mixed $value
     * @return \Core\Form\Container
     */
    public function setParam($key, $value)
    {
        $this->params[$key] = $value;
        
        foreach ($this->forms as $form) {
            if (isset($form['__instance__']) && is_object($form['__instance__'])) {
                $form['__instance__']->setParam($key, $value);
            }
        }
        return $this;
    }
    
    /**
     * Gets the value of a formular parameter.
     * 
     * Returns the provided <b>$default</b> value or null, if parameter does
     * not  exist.
     * 
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function getParam($key, $default = null)
    {
        return isset($this->params[$key]) ? $this->params[$key] : $default;
    }
    
    /**
     * Gets a specific formular.
     * 
     * This formular will be created upon the first retrievement.
     * If created, the formular gets passed the formular parameters set in this container.
     * 
     * @param string $key
     * @return null|\Core\Form\ContainerInterface|\Zend\Form\FormInterface
     */
    public function getForm($key)
    {
        if (false !== strpos($key, '.')) {
            list($key, $childKey) = explode('.', $key, 2);
            $container = $this->getForm($key);
            return $container->getForm($childKey);
        }
        
        if (!isset($this->forms[$key])) {
            return null;
        }
        
        $form = $this->forms[$key];
        if (isset($form['__instance__']) && is_object($form['__instance__'])) {
            return $form['__instance__'];
        } 
        
        $usePostArray  = isset($form['use_post_array']) ? $form['use_post_array'] : true;
        $useFilesArray = isset($form['use_files_array']) ? $form['use_files_array'] : false;
        
        $options = array(
            'use_post_array' => $usePostArray,
            'use_files_array' => $useFilesArray,
        );
        
        $formInstance = $this->formElementManager->get($form['type'], $options);
        $formName     = (($name = $this->getName())
                         ? $name . '.' : '')
                        . $form['name'];
        $formInstance->setName($formName);
        
        if ($entity = $this->getEntity()) {
            $this->mapEntity($formInstance, isset($form['property']) ? $form['property'] : $key, $entity);
        }
        
        $formInstance->setParams($this->getParams());
        $this->forms[$key]['__instance__'] = $formInstance;
        return $formInstance;
    }
    
    /**
     * Sets a form or form specification.
     * 
     * if <b>$spec</b> is a string, it is used as form type, name is set to <b>$key</b>
     * 
     * @param string $key
     * @param string|array $spec
     * @param string $enabled Should the formular be enabled or not 
     * @return \Core\Form\Container
     */
    public function setForm($key, $spec, $enabled = true)
    {
        if (!is_array($spec)) {
            $spec = array('type' => $spec, 'name' => $key);
        }
        if (!isset($spec['name'])) {
            $spec['name'] = $key;
        }
        
        $this->forms[$key] = $spec;
        if ($enabled) {
            $this->enableForm($key);
        } else if (true === $this->activeForms) {
            $this->activeForms = false;
        }
        return $this;
    }
    
    /**
     * Sets formulars or specifications.
     * 
     * <b>$forms</b> must be in the format:
     * <pre>
     *    'name' => [spec]
     * </pre>
     * 
     * <b>$spec</b> must be compatible with {@link setForm}.
     * Additionally you can include a key 'enabled' in the spec, which will override 
     * <b>$enabled</b> only for the current formular.
     * 
     * @param array $forms
     * @param boolean $enabled
     * @return \Core\Form\Container
     */
    public function setForms(array $forms, $enabled = true)
    {
        foreach ($forms as $key => $spec) {
            if (is_array($spec) && isset($spec['enabled'])) {
                $currentEnabled = $spec['enabled'];
                unset($spec['enabled']);
            } else {
                $currentEnabled = $enabled;
            }
            $this->setForm($key, $spec, $currentEnabled);
        }
        return $this;
    }
    
    /**
     * Enables a formular.
     * 
     * Enabled formulars are included in the {@link getIterator()}
     * 
     * Traverses in child containers through .dot-Notation.
     * 
     * @param string $key
     * @return \Core\Form\Container
     */
    public function enableForm($key = null)
    {
        if (null === $key) {
            $this->activeForms = array_keys($this->forms);
            return $this;
        } 
        
        if (!is_array($key)) {
            $key = array($key);
        }
        
        foreach ($key as $k) {
            if (false !== strpos($k, '.')) {
                list($childKey, $childForm) = explode('.', $k, 2);
                $child = $this->getForm($childKey);
                $child->enableForm($childForm);
            } else {
                if (isset($this->forms[$k]) && !in_array($k, $this->activeForms)) {
                    $this->activeForms[] = $k;
                }
            }
        }
        
        return $this;
    }
    
    /**
     * Disables a formular.
     * 
     * @param string $key
     * @return \Core\Form\Container|boolean
     */
    public function disableForm($key = null)
    {
        if (null === $key) {
            $this->activeForms = array();
            return $this;
        } 
        
        if (!is_array($key)) {
            $key = array($key);
        }
        
        foreach ($key as $k) {
            if (false !== strpos($k, '.')) {
                list($childKey, $childForm) = explode('.', $k, 2);
                $child = $this->getForm($childKey);
                $child->disableForm($childForm);
            } else {
                
            }
        }
        $this->activeForms = array_filter($this->activeForms, function ($item) use ($key) {
            return !in_array($item, $key);
        });
        
        return $this;
    }
    
    /**
     * Sets the entity for formular binding.
     * 
     * @param EntityInterface $entity
     * @return \Core\Form\Container
     */
    public function setEntity(EntityInterface $entity)
    {
        $this->entity = $entity;
        
        foreach ($this->forms as $key => $form) {
            if (isset($form['__instance__']) && is_object($form['__instance__'])) {
                $this->mapEntity($form['__instance__'], isset($form['property']) ? $form['property'] : $key, $entity);
            }
        }
        return $this;
    }
    
    /**
     * Gets the entity.
     * 
     * @return \Core\Entity\EntityInterface
     */
    public function getEntity()
    {
        return $this->entity;
    }
    
    protected function mapEntity($form, $key, $entity)
    {
        
        if (true === $key) {
            $mapEntity = $entity;
        } else if (isset($entity->$key)) {
            $mapEntity = $entity->$key;
        } else {
            return;
        }
        
        if ($form instanceOf Container) {
            $form->setEntity($mapEntity);
        } else {
            $form->bind($mapEntity);
        }
        
    }
}
