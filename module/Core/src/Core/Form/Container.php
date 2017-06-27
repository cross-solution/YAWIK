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
use Zend\Stdlib\PriorityList;
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
     * @var \Zend\Form\FormElementManager\FormElementManagerV3Polyfill
     */
    protected $formElementManager;
    
    /**
     * Entity to bind to the formulars.
     *
     * @var \Core\Entity\EntityInterface[]
     */
    protected $entities;
    
    /**
     * Parameters to pass to the formulars.
     *
     * @var array
     */
    protected $params = array();

    protected $parent;

    /**
     * @param ServiceLocatorInterface $formElementManager
     * @return Container
     */
    public function setFormElementManager(ServiceLocatorInterface $formElementManager)
    {
        $this->formElementManager = $formElementManager;
        return $this;
    }

    /**
     * Gets an iterator to iterate over the enabled formulars.
     *
     * @return \ArrayIterator
     * @see IteratorAggregate::getIterator()
     */
    public function getIterator()
    {
        $iterator = new PriorityList();
        $iterator->isLIFO(false);

        foreach ($this->activeForms as $key) {
            $spec = $this->forms[$key];
            $priority = isset($spec['priority']) ? $spec['priority'] : 0;

            $iterator->insert($key, $this->getForm($key), $priority);
        }

        return $iterator;
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
     * @param bool $flag
     * @return $this
     */
    public function setIsDisableCapable($flag)
    {
        $this->options['is_disable_capable'] = $flag;

        return $this;
    }

    /**
     * @return bool
     */
    public function isDisableCapable()
    {
        return isset($this->options['is_disable_capable'])
               ? $this->options['is_disable_capable'] : true;
    }

    /**
     * @param bool $flag
     * @return $this
     */
    public function setIsDisableElementsCapable($flag)
    {
        $this->options['is_disable_elements_capable'] = $flag;

        return $this;
    }

    /**
     * @return bool
     */
    public function isDisableElementsCapable()
    {
        return isset($this->options['is_disable_elements_capable'])
               ? $this->options['is_disable_elements_capable'] : true;
    }

    /**
     * @param array $map
     */
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

    public function setOptions($options)
    {
        parent::setOptions($options);

        if (isset($this->options['forms'])) {
            $this->setForms($this->options['forms']);
        }

        return $this;
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
     * @param bool $asInstance if set to false, the specification array is returned, and no instance created.
     *
     * @return null|\Core\Form\Container|\Zend\Form\FormInterface
     * @since 0,25 added $asInstance parameter
     */
    public function getForm($key, $asInstance = true)
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

        if (!$asInstance) {
            return $form;
        }

        if (isset($form['__instance__']) && is_object($form['__instance__'])) {
            return $form['__instance__'];
        }

        $options = isset($form['options']) ? $form['options'] : array();
        if (!isset($options['name'])) {
            $options['name'] = isset($form['name']) ? $form['name'] : $key;
        }
        if (!isset($options['use_post_array'])) {
            $options['use_post_array'] = true;
        }
        if (!isset($options['use_files_array'])) {
            $options['use_files_array'] = false;
        }
	    
        //@TODO: [ZF3] Passing options in $formElementManager->get is not working need to do manually set options
        $formInstance = $this->formElementManager->get($form['type'], $options);
        $formInstance->setOptions(array_merge($formInstance->getOptions(),$options));
        $formInstance->setParent($this);
        
        if (isset($form['attributes'])) {
            $formInstance->setAttributes($form['attributes']);
        }

        $formName = $this->formatAction($form['name']);
        $formInstance->setName($formName);
        $formAction = $formInstance->getAttribute('action');

        if (empty($formAction)) {
            $formInstance->setAttribute('action', '?form=' . $formName);
        }

        // @TODO: [ZF3] which one is correct? $form[options][label] or $form[options]
	    if(isset($form['label'])){
		    $formLabel = $form['label'];
	    }elseif(isset($form['options']['label'])){
		    $formLabel = $form['options']['label'];
	    }
	    
        if (isset($formLabel)) {
            $formInstance->setLabel($formLabel);
        }

        if (isset($form['disable_elements'])
            && $formInstance instanceof DisableElementsCapableInterface
            && $formInstance->isDisableElementsCapable()
        ) {
            $formInstance->disableElements($form['disable_elements']);
        }

        $entity = $this->getEntity($form['entity']);
        if ($entity) {
            $this->mapEntity($formInstance, $entity, isset($form['property']) ? $form['property'] : $key);
        }

        $formInstance->setParams($this->getParams());

        $this->forms[$key]['__instance__'] = $formInstance;
        $this->forms[$key]['options'] = $options;
        return $formInstance;
    }
    
    /**
     * Execute an arbitrary action
     *
     * @param string $name Name of an action
     * @param array $data Arbitrary data
     * @return array
     */
    public function executeAction($name, array $data = [])
    {
        if (false !== strpos($name, '.')) {
            list($name, $childKey) = explode('.', $name, 2);
            $container = $this->getForm($name);
            
            // execute child container's action
            return $container->executeAction($childKey, $data);
        }
        
        // this container defines no actions
        return [];
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
        if (is_object($spec)) {
            if ($spec instanceof FormParentInterface) {
                $spec->setParent($this);
            }

            $spec = [ '__instance__' => $spec, 'name' => $key, 'entity' => '*' ];
        }

        if (!is_array($spec)) {
            $spec = array('type' => $spec, 'name' => $key);
        }
        if (!isset($spec['name'])) {
            $spec['name'] = $key;
        }
        if (!isset($spec['entity'])) {
            $spec['entity'] = '*';
        }
        
        $this->forms[$key] = $spec;

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
     * @param mixed $entity
     * @param string $key
     * @throws \InvalidArgumentException
     * @return Container
     */
    public function setEntity($entity, $key='*')
    {
        if (!$entity instanceof EntityInterface)
        {
            throw new \InvalidArgumentException(sprintf('$entity must be instance of %s', EntityInterface::class));
        }
        
        $this->entities[$key] = $entity;
        
        foreach ($this->forms as $formKey => $form) {
            if (isset($form['__instance__']) && is_object($form['__instance__']) && $key == $form['entity']) {
                $this->mapEntity($form['__instance__'], $entity, isset($form['property']) ? $form['property'] : $formKey);
            }
        }
        return $this;
    }


    /**
     * Gets the entity.
     *
     * @return \Core\Entity\EntityInterface
     */
    public function getEntity($key='*')
    {
        return isset($this->entities[$key]) ? $this->entities[$key] : null;
    }
    
    /**
     * Maps entity property to forms or child containers.
     *
     * @param \Zend\Form\FormInterface $form
     * @param \Core\Entity\EntityInterface $entity
     * @param string $property
     * @return void
     */
    protected function mapEntity($form, $entity, $property)
    {
        if (false === $property) {
            return;
        }

        if (true === $property) {
            $mapEntity = $entity;
        } else if ($entity->hasProperty($property) || is_callable([$entity, "get$property"])) {
            $getter = "get$property";
            $mapEntity = $entity->$getter();
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
     * Return isValid
     *
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
            } elseif (is_array($searchIn) || $searchIn instanceof \Traversable) {
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

    /**
     * @param $data
     * @return $this
     */
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

    /**
     * @param $parent
     * @return $this
     */
    public function setParent($parent)
    {
        $this->parent = $parent;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getParent()
    {
        return $this->parent;
    }


    /**
     * @return bool
     */
    public function hasParent()
    {
        return isset($this->parent);
    }

    /**
     * @param Renderer $renderer
     * @return string
     */
    public function renderPre(Renderer $renderer)
    {
        return '';
    }

    /**
     * @param Renderer $renderer
     * @return string
     */
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
     * Gets the form after the actual active
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
    
    /**
     * Format an action name
     *
     * @param string $name Name of an action
     * @return string Formatted name of an action
     */
    public function formatAction($name)
    {
        return sprintf('%s%s', $this->hasParent() ? $this->getName() . '.' : '', $name);
    }

    /**
     * @param $key
     * @return string|null
     */
    public function getActionFor($key)
    {
        if (isset($this->forms[$key])) {
            return '?form=' . $this->formatAction($this->forms[$key]['name']);
        }
    }
}