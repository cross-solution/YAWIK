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
 * DependecyResult ValueObject.
 * 
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @todo write test 
 */
class DependencyResult 
{
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
        if (!is_array($entities) && !$entities instanceOf \Traversable) {
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
}
