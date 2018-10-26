<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2018 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace Core\Service\EntityEraser;

use Core\Entity\EntityInterface;

/**
 * Event for collecting dependencies.
 * 
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @todo write test 
 */
class DependencyResultEvent extends BaseEvent
{

    const CHECK_DEPENDENCIES = 'check-dependencies';
    const DELETE = 'delete';

    /**
     * @var EntityInterface
     */
    private $entity;

    /**
     * @var DependencyResultCollection
     */
    private $dependencyResultCollection;

    /**
     * DependencyResultEvent constructor.
     *
     * @param null|string $name
     * @param null|object $target
     * @param null|\Traversable|array $params
     */
    public function __construct($name = null, $target = null, $params = null)
    {
        parent::__construct($name, $target, $params);
        $this->resetDependencyResultCollection();

    }

    /**
     * @internal
     * this is needed, because Core\EventManager clones the event prototype
     * and internal object references are not cloned recursively.
     */
    public function __clone()
    {
        $this->resetDependencyResultCollection();
    }

    /**
     * Resets the dependencyResultCollection
     */
    private function resetDependencyResultCollection()
    {
        $this->dependencyResultCollection = new DependencyResultCollection();
    }


    /**
     * @return EntityInterface
     */
    public function getEntity()
    {
        return $this->entity;
    }

    /**
     * @param EntityInterface $entity
     *
     * @return self
     */
    public function setEntity($entity)
    {
        $this->entity = $entity;

        return $this;
    }

    /**
     * Gets the class name of the entity.
     *
     * @return string
     */
    public function getEntityClass()
    {
        return get_class($this->getEntity());
    }

    /**
     * Checks the entitys' type.
     *
     * @param string $class Type name to check against.
     *
     * @return bool
     */
    public function isEntityInstanceOf($class)
    {
        return $this->getEntity() instanceOf $class;
    }


    /**
     * @return DependencyResultCollection
     */
    public function getDependencyResultCollection()
    {
        return $this->dependencyResultCollection;
    }

    /**
     * Shortcut to add dependencies to the collection.
     *
     * @param string|\Traversable|array $name
     * @param null|array|\Traversable       $entities
     * @param array|null $options
     *
     * @return DependencyResultCollection
     */
    public function addDependencies($name, $entities = null, array $options = null)
    {
        return $this->dependencyResultCollection->add($name, $entities, $options);
    }

    /**
     * Returns true, if this is a delete event.
     *
     * @return bool
     */
    public function isDelete()
    {
        return self::DELETE == $this->getName();
    }

    public function setParam($name, $value)
    {
        if ('entity' == $name) {
            $this->setEntity($value);
            return;
        }

        parent::setParam($name, $value);
    }

    public function setParams($params)
    {
        if (is_array($params) || $params instanceOf \ArrayAccess) {
            if (isset($params['entity'])) {
                $this->setEntity($params['entity']);
                unset($params['entity']);
            }
        } else if (is_object($params)) {
            if (isset($params->entity)) {
                $this->setEntity($params->entity);
                unset($params->entity);
            }
        }

        parent::setParams($params);
    }
}
