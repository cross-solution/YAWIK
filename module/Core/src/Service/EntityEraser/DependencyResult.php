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

/**
 * DependencyResult ValueObject.
 *
 * There are three modes:
 *
 * MODE_LIST: The Object holds a collection of entities which are affected.
 * MODE_COUNT: Instead of all entities in a collection, just the count of affected entities is available.
 * MODE_DELETE: Like MODE_LIST, but the EntityEraser plugin should loop through the collection and delete each
 *              entity. (if you just do not want to do it in the listener.)
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @todo write test
 */
class DependencyResult
{
    const MODE_LIST = 'list';
    const MODE_COUNT = 'count';
    const MODE_DELETE = 'delete';

    /**
     * Name of this result.
     *
     * @var string
     */
    private $name;

    /**
     * Entities collection.
     *
     * @var array|\Traversable
     */
    private $entities;

    /**
     * ViewScript to render the entity list with.
     *
     * @var string
     */
    private $viewScript;

    /**
     * Description
     *
     * @var string
     */
    private $description;

    /**
     * Mode of this result.
     *
     * @var string
     */
    private $mode;

    /**
     * DependencyResult constructor.
     *
     * There are two valid options:
     * - 'description': Sets the description.
     * - 'viewScript': Sets the view script.
     *
     * @param string     $name
     * @param array|\Traversable  $entities
     * @param array|null $options
     */
    public function __construct($name, $entities, array $options = null)
    {
        $this->mode =
            isset($options['mode']) && in_array($options['mode'], [self::MODE_LIST, self::MODE_COUNT, self::MODE_DELETE])
                ? $options['mode']
                : self::MODE_LIST
        ;

        if (self::MODE_COUNT == $this->mode) {
            if (!is_int($entities)) {
                throw new \InvalidArgumentException('Expecting integer value in "count" mode.');
            }
        } elseif (!is_array($entities) && !$entities instanceof \Traversable) {
            throw new \InvalidArgumentException('Entities must be an array or an instance of \Traversable');
        }

        $this->name = (string) $name;
        $this->entities = $entities;
        $this->viewScript = isset($options['viewScript']) ? $options['viewScript'] : 'core/entity-eraser/dependency-list';
        $this->description = isset($options['description']) ? $options['description'] : '';
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return array|\Traversable
     */
    public function getEntities()
    {
        return $this->entities;
    }

    /**
     * @return string
     */
    public function getViewScript()
    {
        return $this->viewScript;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    public function isMode($mode)
    {
        return $this->mode == $mode;
    }

    public function isList()
    {
        return $this->isMode(self::MODE_LIST);
    }

    public function isCount()
    {
        return $this->isMode(self::MODE_COUNT);
    }

    public function isDelete()
    {
        return $this->isMode(self::MODE_DELETE);
    }
}
