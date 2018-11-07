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
use Core\Form\Form as CoreForm;
use Zend\EventManager\EventInterface as Event;
use ArrayIterator;

/**
 * Manages CRUD operations for the given collection by creating a group of independent forms
 *
 * It is recommended to use a collection indexed by entry identifiers
 * You can use the \Core\Collection\IdentityWrapper as an adapter for a numerically
 * indexed collection
 *
 * @author fedys
 */
class CollectionContainer extends Container implements ViewHelperProviderInterface
{
    const NEW_ENTRY = '__new_entry__';
    
    /**
     * @var string
     */
    protected $formService;
    
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
        
        if ($key === static::NEW_ENTRY) {
            $collection[] = $this->newEntry;
            $form = $this->buildForm($key, $this->newEntry);
            $eventManager = $form->getEventManager();
            $eventManager->attach(CoreForm::EVENT_IS_VALID, function (Event $event) use ($collection) {
                if (!$event->getParam('isValid')) {
                    $collection->removeElement($this->newEntry);
                }
            });
            $eventManager->attach(CoreForm::EVENT_PREPARE, function (Event $event) use ($collection) {
                $this->setupForm($event->getTarget(), $collection->indexOf($this->newEntry));
            });
            
            return $form;
        } elseif (isset($collection[$key])) {
            return $this->buildForm($key, $collection[$key]);
        }
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
        if (!$entity instanceof Collection) {
            throw new \InvalidArgumentException(sprintf('$entity must be instance of %s', Collection::class));
        }
        
        $this->entities['*'] = $entity;
        
        return $this;
    }
    
    /**
     * Returns the template form for creating a new form via JavaScript
     *
     * @return CoreForm
     */
    public function getTemplateForm()
    {
        return $this->buildForm(static::NEW_ENTRY);
    }

    /**
     * @return CoreForm[]
     */
    protected function getForms()
    {
        $forms = [];
        
        foreach ($this->getCollection() as $key => $entry) {
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
        
        if (!$collection) {
            throw new \RuntimeException('Entity must be set');
        }
        
        return $collection;
    }
    
    /**
     * @param string $key
     * @param mixed $entry
     * @throws \RuntimeException
     * @return CoreForm
     */
    protected function buildForm($key, $entry = null)
    {
        $form = $this->formElementManager->get($this->formService);
        
        if (!$form instanceof CoreForm) {
            throw new \RuntimeException(sprintf('$form must be instance of %s', CoreForm::class));
        }
        
        $this->setupForm($form, $key);
        
        if (isset($entry)) {
            $form->bind($entry);
        }
            
        return $form;
    }
    
    /**
     * @param CoreForm $form
     * @param string $key
     */
    protected function setupForm(CoreForm $form, $key)
    {
        $form->setAttribute('action', sprintf('?form=%s', $this->formatAction($key)))
            ->setAttribute('data-entry-key', $key)
            ->setOption('control_buttons', [[
                'class' => 'btn-danger form-collection-container-remove-button',
                'icon' => 'delete-o',
                'label' => /*@translate */ 'Remove',
            ]]);
    }
}
