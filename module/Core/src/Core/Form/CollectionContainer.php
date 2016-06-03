<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

namespace Core\Form;

use Zend\ServiceManager\ServiceLocatorInterface;
use Core\Form\FormParentInterface;
use Core\Entity\EntityInterface;
use Core\Form\Element\ViewHelperProviderInterface;
use Zend\Form\Element;
use Zend\Form\Fieldset;
use Zend\Hydrator\HydratorInterface;
use Zend\Form\Element\Collection as CollectionElement;
use Core\Form\SummaryForm;
use Zend\EventManager\EventInterface as Event;
use IteratorAggregate;
use Countable;
use ArrayIterator;

/**
 * @author fedys
 */
class CollectionContainer extends Element implements IteratorAggregate, Countable, FormParentInterface, ContainerInterface, ViewHelperProviderInterface
{
    /**
     * @var array
     */
    protected $forms;
    
    /**
     * @var array
     */
    protected $groups;
    
    /**
     * @var array
     */
    protected $collections;
    
    /**
     * @var \Zend\Form\FormElementManager
     */
    protected $formElementManager;
    
    /**
     * @var EntityInterface
     */
    protected $entity;
    
    /**
     * @var Fieldset
     */
    protected $fieldset;
    
    /**
     * @var mixed
     */
    protected $parent;
    
    /**
     * @var string
     */
    protected $viewHelper = 'formCollectionContainer';
    
    /**
     * @param ServiceLocatorInterface $formElementManager
     * @param Fieldset $fieldset
     */
    public function __construct(ServiceLocatorInterface $formElementManager, Fieldset $fieldset)
    {
        $this->formElementManager = $formElementManager;
        $this->fieldset = $fieldset;
    }

    /**
     * @see IteratorAggregate::getIterator()
     */
    public function getIterator()
    {
        return new ArrayIterator($this->getForms());
    }
    
    /**
     * @see Countable::count()
     */
    public function count()
    {
        return count($this->getForms());
    }
    
    /**
     * @param mixed $parent
     * @return CollectionContainer
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
	 * @param EntityInterface $entity
	 * @return CollectionContainer
	 */
	public function setEntity(EntityInterface $entity)
	{
		$this->entity = $entity;
		
		return $this;
	}
	
	/**
	 * @param array $params
	 * @return ContainerInterface
	 */
	public function setParams(array $params)
	{
	    return $this;
	}

	/**
	 * @see \Core\Form\ContainerInterface::getForm()
	 */
	public function getForm($key)
	{
		$forms = $this->getForms();
		
		if (isset($forms[$key])) {
            return $forms[$key];
        }
        
		list($name, $index) = explode('-', $key);
		$collections = $this->getCollections();
		
		if (!isset($collections[$name]))
		{
		    return;
		}
		
        $collection = $collections[$name];
		$hydrator = $this->fieldset->getHydrator();
		$values = $hydrator->extract($this->entity);
        $values[$name][$index] = $collection->getTargetElement()->getObject();
        $this->extractCollectionsValues($collections, $values);
		$this->fieldset->populateValues($values);
        $this->fieldset->bindValues($values);
        $fieldset = $collection->get($index);
        $form = $this->buildForm($collection, $fieldset, $hydrator);
        $form->getEventManager()->attach(\Core\Form\Form::EVENT_IS_VALID, function (Event $event) {
            if (!$event->getParam('isValid')) {
                $this->removeFromCollection($event->getTarget());
            }
        });

		return $form;
	}
	
	/**
	 * @see \Core\Form\Element\ViewHelperProviderInterface::getViewHelper()
	 */
	public function getViewHelper()
	{
		return $this->viewHelper;
	}

	/**
	 * @see \Core\Form\Element\ViewHelperProviderInterface::setViewHelper()
	 */
	public function setViewHelper($helper)
	{
	    $this->viewHelper = $helper;
	    
		return $this;
	}
	
	/**
	 * @return array
	 */
	public function getGroups()
	{
	    if (!isset($this->groups))
	    {
	        $this->groups = [];
	        
	        foreach ($this->getForms() as $key => $form) /* @var $form SummaryForm */
	        {
	            $name = $form->getBaseFieldset()->getName();
	            
	            if (!isset($this->groups[$name]))
	            {
	                $this->groups[$name] = [];
	            }
	            
	            $this->groups[$name][$key] = $form;
	        }
	    }
	    
	    return $this->groups;
	}
	
	/**
	 * @param CollectionElement $collection
	 * @return array
	 */
	public function getGroup(CollectionElement $collection)
	{
	    $groups = $this->getGroups();
	    $name = $collection->getName();
	    
	    return isset($groups[$name]) ? $groups[$name] : [];
	}
	
	/**
	 * @param CollectionElement $collection
	 * @return SummaryForm|null
	 */
	public function getTemplateForm(CollectionElement $collection)
	{
	    if (!$collection->shouldCreateTemplate())
	    {
	        return;
	    }
	    
	    return $this->buildForm($collection, $collection->getTemplateElement());
	}

	/**
	 * @return array
	 */
	public function getCollections()
	{
	    if (!isset($this->collections))
	    {
	        $this->collections = array_filter($this->fieldset->getFieldsets(), function ($collection) {
                return $collection instanceof CollectionElement;
            });
	    }
	    
		return $this->collections;
	}

	/**
     * @return array
     */
    protected function getForms()
    {
        if (!isset($this->forms))
        {
            $this->forms = $this->buildForms();
        }
        
        return $this->forms;
    }
    
	/**
     * @return array
     */
    protected function buildForms()
    {
        if (!$this->entity)
        {
            throw new \RuntimeException('Entity must be set');
        }
        
        $forms = [];
        $this->fieldset->setObject($this->entity);
        $hydrator = $this->fieldset->getHydrator();
        $values = $hydrator->extract($this->entity);
        $collections = $this->getCollections();
        $this->extractCollectionsValues($collections, $values);
        $this->fieldset->populateValues($values);
        
        foreach ($collections as $collection) { /* @var $collection CollectionElement */
            foreach ($collection->getFieldsets() as $fieldset) /* @var $fieldset Fieldset */
            {
                $key = sprintf('%s-%s', $collection->getName(), $fieldset->getName());
                $forms[$key] = $this->buildForm($collection, $fieldset, $hydrator);
            }
        }
        
        return $forms;
    }

    /**
     * Recursively extract and values for collections
     *
     * @param array $collections
     * @param array $values
     */
    protected function extractCollectionsValues(array $collections, array &$values)
    {
        foreach ($collections as $collection) {
            $name = $collection->getName();
            
            if (isset($values[$name])) {
                $object = $values[$name];
                
                if ($collection->allowObjectBinding($object)) {
                    $collection->setObject($object);
                    $values[$name] = $collection->extract();
                }
            }
        }
    }
    
    /**
     * @param CollectionElement $collection
     * @param Fieldset $fieldset
     * @param HydratorInterface $hydrator
     * @return SummaryForm
     */
    protected function buildForm(CollectionElement $collection, Fieldset $fieldset, HydratorInterface $hydrator = null)
    {
        $form = new SummaryForm(sprintf('%s-%s-%s', $this->fieldset->getName(), $collection->getName(), $fieldset->getName()));
        $this->formElementManager->injectFactory($form, $this->formElementManager);
        
        $baseFieldset = new Fieldset($collection->getName());
        $this->formElementManager->injectFactory($baseFieldset, $this->formElementManager);
        $baseFieldset->setUseAsBaseFieldset(true);
        $baseFieldset->add($fieldset);
        
        $form->add($baseFieldset);
        $form->init();
        
        if ($hydrator)
        {
            $form->setHydrator($hydrator);
            $form->bind($this->entity);
        }
        
        $key = sprintf('%s-%s', $collection->getName(), $fieldset->getName());
        $form->setAttribute('action', sprintf('?form=%s%s', $this->hasParent() ? $this->getName() . '.' : '', $key));
        
        return $form;
    }

    /**
     *
     * @param Form $form
     */
    protected function removeFromCollection(SummaryForm $form)
    {
        $collections = $this->getCollections();
        $hydrator = $this->fieldset->getHydrator();
        $values = $hydrator->extract($this->entity);
        
        foreach ($form->getData(\Zend\Form\FormInterface::VALUES_AS_ARRAY) as $name => $array) {
            if (!isset($collections[$name])) {
                continue;
            }
            
            foreach ($array as $index => $value) {
                unset($values[$name][$index]);
            }
        }
        
        $this->extractCollectionsValues($collections, $values);
        $this->fieldset->bindValues($values);
    }
}