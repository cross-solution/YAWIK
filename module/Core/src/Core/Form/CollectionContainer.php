<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

namespace Core\Form;

use Core\Form\Element\ViewHelperProviderInterface;
use Doctrine\Common\Collections\Collection;
use Zend\Form\Form as ZendForm;
use Zend\EventManager\EventInterface as Event;
use ArrayIterator;

/**
 * @author fedys
 */
class CollectionContainer extends Container implements ViewHelperProviderInterface
{
    const TEMPLATE_PLACEHOLDER = '__index__';
    
    /**
     * @var string
     */
    protected $formService;
    
    /**
     * @var Collection
     */
    protected $collection;
    
    /**
     * @var mixed
     */
    protected $newEntry;
    
    /**
     * @var string
     */
    protected $viewHelper = 'formCollectionContainer';
    
    /**
     * @param string $formService
     * @param mixed $newEntry
     */
    public function __construct($formService, $newEntry)
    {
        $this->formService = $formService;
        $this->newEntry = $newEntry;
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
        return count($this->getCollection());
    }
    
    /**
     * @see \Core\Form\ContainerInterface::getForm()
     */
    public function getForm($key, $asInstance = true)
    {
        $collection = $this->getCollection();
        
        if (isset($collection[$key]))
        {
            return $this->buildForm($key, $collection[$key]);
        }
        
        $collection[$key] = $this->newEntry;
        $form = $this->buildForm($key, $collection[$key]);
        $form->getEventManager()->attach(\Core\Form\Form::EVENT_IS_VALID, function (Event $event) use ($collection, $key) {
            if (!$event->getParam('isValid')) {
                unset($collection[$key]);
            }
        });
        
        return $form;
    }

    /**
     * @see \Core\Form\Container::executeAction()
     */
    public function executeAction($name, array $data = array())
    {
        switch ($name) {
            case 'remove':
                $success = false;
                if (isset($data['key'])) {
                    $success = $this->getCollection()->remove($data['key']) !== null;
                }
                return [
                    'success' => $success
                ];
                break;
            
            default:
                return [];
                break;
        }
    }
    
    /**
     * @param string $name
     * @return string
     */
    public function formatActionName($name)
    {
        return sprintf('%s%s', $this->hasParent() ? $this->getName() . '.' : '', $name);
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
     * @see \Core\Form\Container::setEntity()
     */
    public function setEntity($entity, $key = '*')
    {
        if (!$entity instanceof Collection)
        {
            throw new \InvalidArgumentException(sprintf('$entity must be instance of %s', Collection::class));
        }
        
        $this->entities['*'] = $entity;
        
        return $this;
    }
    
    /**
     * @return ZendForm
     */
    public function getTemplateForm()
    {
        return $this->buildForm(static::TEMPLATE_PLACEHOLDER);
    }

    /**
     * @return []
     */
    protected function getForms()
    {
        $forms = [];
        
        foreach ($this->getCollection() as $key => $entry)
        {
            $form = $this->buildForm($key, $entry);
            
            $forms[$key] = $form;
        }
        
        return $forms;
    }
    
    /**
     * @throws \RuntimeException
     * @return Collection
     */
    protected function getCollection()
    {
        $collection = $this->getEntity();
        
        if (!$collection)
        {
            throw new \RuntimeException('Entity must be set');
        }
        
        return $collection;
    }
    
    /**
     * @param string $key
     * @param mixed $entry
     * @throws \RuntimeException
     * @return \Zend\Form\Form
     */
    protected function buildForm($key, $entry = null)
    {
        $form = $this->formElementManager->get($this->formService);
        
        if (!$form instanceof ZendForm)
        {
            throw new \RuntimeException(sprintf('$form must be instance of %s', ZendForm::class));
        }
        
        $form->setAttribute('action', sprintf('?form=%s%s', $this->hasParent() ? $this->getName() . '.' : '', $key));
        
        if (isset($entry)) {
            $form->bind($entry);
        }
            
        return $form;
    }
}