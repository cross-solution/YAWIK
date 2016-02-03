<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

/** Core forms */
namespace Core\Form;

use Zend\Form\Element;
use Zend\Form\FieldsetInterface;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\View\Renderer\PhpRenderer as Renderer;
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
class Container extends Element implements
    DisableElementsCapableInterface,
    ServiceLocatorAwareInterface,
    FormParentInterface,
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

    protected $parent;

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
        $forms = array_map(
            function ($key) use ($self) {
                return $self->getForm($key);
            },
            $this->activeForms
        );
        
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

    public function setIsDisableCapable($flag)
    {
        $this->options['is_disable_capable'] = $flag;

        return $this;
    }

    public function isDisableCapable()
    {
        return isset($this->options['is_disable_capable'])
               ? $this->options['is_disable_capable'] : true;
    }

    public function setIsDisableElementsCapable($flag)
    {
        $this->options['is_disable_elements_capable'] = $flag;

        return $this;
    }

    public function isDisableElementsCapable()
    {
        return isset($this->options['is_disable_elements_capable'])
               ? $this->options['is_disable_elements_capable'] : true;
    }

    public function disableElements(array $map)
    {
        foreach ($map as $key => $name) {
            if (is_numeric($key)) {
                if (isset($this->forms[$name])) {
                    $form = $this->getForm($name);
                    if (false !== $form->getOption('is_disable_capable')) {
                        $this->disableForm($name);
                    }
                }
                continue;
            }

            if (!isset($this->forms[$key])) {
                continue;
            }

            if (isset($this->forms[$key]['__instance__'])) {
                $form = $this->forms[$key]['__instance__'];

                if ($form instanceof DisableElementsCapableInterface
                    && $form->isDisableElementsCapable()
                ) {
                    $form->disableElements($name);
                }
            }
            $this->forms[$key]['disable_elements'] = $name;
        }
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
            if (isset($form['__instance__'])
                && is_object($form['__instance__'])
                && method_exists($form['__instance__'], 'setParams')
            ) {
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
            if (isset($form['__instance__'])
                && is_object($form['__instance__'])
                && method_exists($form['__instance__'], 'setParam')
            ) {
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
     * @return null|\Core\Form\Container|\Zend\Form\FormInterface
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

        $options = isset($form['options']) ? $form['options'] : array();
        if (!isset($options['use_post_array'])) {
            $options['use_post_array'] = true;
        }
        if (!isset($options['use_files_array'])) {
            $options['use_files_array'] = false;
        }
        
        $formInstance = $this->formElementManager->get($form['type'], $options);
        $formInstance->setParent($this);

        $formName = '';
        if (!empty($this->parent)) {
            $name = $this->getName();
            if (!empty($name)) {
                $formName .= $name . '.';
            }
        }
        $formName .= $form['name'];
        $formInstance->setName($formName)
                     ->setAttribute('action', '?form=' . $formName);

        //$testKey = $this->getActionFor($form['type']);
        
        if (isset($form['label'])) {
            $formInstance->setLabel($form['label']);
        }

        if (isset($form['disable_elements'])
            && $formInstance instanceof DisableElementsCapableInterface
            && $formInstance->isDisableElementsCapable()
        ) {
            $formInstance->disableElements($form['disable_elements']);
        }

        if ($entity = $this->getEntity()) {
            $this->mapEntity($formInstance, isset($form['property']) ? $form['property'] : $key, $entity);
        }

        $formInstance->setParams($this->getParams());

        $this->forms[$key]['__instance__'] = $formInstance;
        $this->forms[$key]['options'] = $options;
        return $formInstance;
    }

    /**
     * Sets a form or form specification.
     *
     * if <b>$spec</b> is a string, it is used as form type, name is set to <b>$key</b>
     *
     * @param string       $key
     * @param string|array $spec
     * @param boolean      $enabled Should the formular be enabled or not
     *
     * @return self
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
        if ($spec instanceof FormParentInterface) {
            $spec->setParent($this);
        }
        if ($enabled) {
            $this->enableForm($key);
        } elseif (true === $this->activeForms) {
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
                // this seems not to be childkey.childform but actualkey.childkey
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
     *
     * @return self
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
            } elseif (isset($this->forms[$k]['__instance__'])) {
                unset($this->forms[$k]['__instance__']);
            }
        }
        $this->activeForms = array_filter(
            $this->activeForms,
            function ($item) use ($key) {
                return !in_array($item, $key);
            }
        );
        
        return $this;
    }
    
    /**
     * Sets the entity for formular binding.
     *
     * @param EntityInterface $entity
     * @return self
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
    
    /**
     * Maps entity property to forms or child containers.
     *
     * @param \Zend\Form\FormInterface $form
     * @param string $key
     * @param \Core\Entity\EntityInterface $entity
     * @return void
     */
    protected function mapEntity($form, $key, $entity)
    {
        
        if (true === $key) {
            $mapEntity = $entity;
        } elseif (isset($entity->$key)) {
            $mapEntity = $entity->$key;
        } else {
            return;
        }
        
        if ($form instanceof Container) {
            $form->setEntity($mapEntity);
        } else {
            $form->bind($mapEntity);
        }
        
    }

    /**
     * @return bool
     */
    public function isValid()
    {
        $isValid = true;
        foreach ($this->activeForms as $activeFormKey) {
            $activeForm = $this->getForm($activeFormKey);
            $isValid &= $activeForm->isValid();
        }
        return $isValid;
    }

    /**
     * if fieldsets there is get method to have access to any element by name
     * this method is similar
     * get('form') gets a form
     * get('element') gets an element, if an element has the same name as a form, the form get's first access
     * get('form.element') gets an element of a form, this is more efficent because it doesn't expand all forms in the container,
     *      but just the one adressed
     * @param $key string
     * @return null|\Zend\Form\ElementInterface
     */
    public function get($key)
    {
        $return   = null;
        $lastKey  = null;
        $searchIn = $this->activeForms;
        $keySplit = explode('.', $key);

        while (0 < count($keySplit)) {
            $lastKey = array_shift($keySplit);
            foreach ($searchIn as $activeFormKey) {
                if ($lastKey == $activeFormKey) {
                    $searchIn = $this->getForm($activeFormKey);
                    unset($lastKey);
                    break;
                }
            }
        }
        if (!isset($lastKey) && !empty($keySplit)) {
            $lastKey = array_shift($keySplit);
        }
        if (isset($lastKey) && empty($keySplit)) {
            if ($searchIn instanceof FieldsetInterface) {
                // has reached a fieldset to search in
                $return = $searchIn->get($lastKey);
                unset($lastKey);
            } elseif (is_array($searchIn) || $searchIn instanceof Traversable) {
                // is probably still in the container
                foreach ($searchIn as $activeKey) {
                    $activeForm = $this->getForm($activeKey);
                    if ($activeForm instanceof FieldsetInterface) {
                        $return = $activeForm->get($lastKey);
                    }
                }
            }
        }
        if (!isset($lastKey) && empty($keySplit) && !isset($return)) {
            $return = $searchIn;
        }
        return $return;
    }

    public function setData($data)
    {
        $filteredData = array();
        foreach ($data as $key => $elem) {
            if (!array_key_exists($key, $this->params) && $key != 'formName') {
                $filteredData[$key] = $elem;
            }
            if ($key == 'formName' && is_string($elem)) {
                // you can activate a specific form with postData
                foreach ($this->activeForms as $activeFormKey) {
                    if ($activeFormKey == $elem) {
                        $this->enableForm($activeFormKey);
                    } else {
                        $this->disableForm($activeFormKey);
                    }
                }
            }
        }
        foreach ($this->activeForms as $activeFormKey) {
            $activeForm = $this->getForm($activeFormKey);
            $activeForm->setData($filteredData);
        }
        return $this;
    }

    public function setParent($parent)
    {
        $this->parent = $parent;
        return $this;
    }

    public function getParent()
    {
        return $this->parent;
    }


    public function hasParent()
    {
        return isset($this->parent);
    }

    public function renderPre(Renderer $renderer)
    {
        return '';
    }

    public function renderPost(Renderer $renderer)
    {
        return '';
    }

    /**
     * get the actual active Form
     * @param bool $setDefault
     * @return mixed|null
     */
    public function getActiveFormActual($setDefault = true)
    {
        $key = null;
        if (!empty($this->activeForms)) {
            $key = $this->activeForms[0];
        }
        if (!isset($key) && $setDefault) {
            $formsAvailable = array_keys($this->forms);
            $key = array_shift($formsAvailable);
        }
        return $key;
    }

    /**
     * get the form before the actual active
     * @return null
     */
    public function getActiveFormPrevious()
    {
        $key = null;
        $actualKey = $this->getActiveFormActual();
        if (isset($actualKey)) {
            $forms = array_keys($this->forms);
            $formsFlip =  array_flip($forms);
            $index = $formsFlip[$actualKey];
            if (0 < $index) {
                $key = $forms[$index-1];
            }
        }
        return $key;
    }


    /**
     * get the form after the actual active
     * @return null
     */
    public function getActiveFormNext()
    {
        $key = null;
        $actualKey = $this->getActiveFormActual();
        if (isset($actualKey)) {
            $forms = array_keys($this->forms);
            $formsFlip =  array_flip($forms);
            $index = $formsFlip[$actualKey];
            if ($index < count($forms) - 1) {
                $key = $forms[$index+1];
            }
        }
        return $key;
    }

    public function getActionFor($key)
    {
        $form               = $this->forms[$key];
        $options            = isset($form['options']) ? $form['options'] : array();
        $formElementManager = $this->formElementManager;
        if (!isset($options['use_post_array'])) {
            $options['use_post_array'] = true;
        }
        if (!isset($options['use_files_array'])) {
            $options['use_files_array'] = false;
        }

        //$formInstance = $this->formElementManager->get($form['type'], $options);
        $formName     = (($name = $this->getName()) ? $name . '.' : '') . $form['name'];
        $action = '?form=' . $formName;

        return $action;

    }
}
