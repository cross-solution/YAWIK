<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2016 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace Core\Controller;

use Zend\EventManager\Event;
use Zend\Stdlib\PriorityList;
use Zend\View\Model\ViewModel;

/**
 * Admin Controller Event
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @since 0.25
 */
class AdminControllerEvent extends Event
{
    /**
     * Fetch dashboard widgets event.
     *
     * @var string
     */
    const EVENT_DASHBOARD = 'DASHBOARD';

    /**
     * List of dashboard widget view models.
     *
     * @var \Zend\Stdlib\PriorityList
     */
    protected $models;


    /**
     * Creates an instance.
     *
     * Instantiates a new PriorityList for the view models.
     *
     * @param string|null $name
     * @param string|null $target
     * @param array|null $params
     */
    public function __construct($name = null, $target = null, $params = null)
    {
        parent::__construct($name, $target, $params);
        $this->models = new PriorityList();
    }

    /**
     * Adds a view model.
     *
     * @param string    $name Name of the captureTo key.
     * @param ViewModel    $model
     * @param int $priority
     *
     * @return self
     */
    public function addViewModel($name, $model, $priority=0)
    {
        $this->models->insert($name, $model, $priority);

        return $this;
    }

    /**
     * Creates a view model with template and adds it to the list.
     *
     * @param string      $name Name of the captureTo key.
     * @param string      $template Name of the template (resolvable from view template resolver)
     * @param array $vars   additional variables for the view.
     * @param int   $priority
     *
     * @return AdminControllerEvent
     */
    public function addViewTemplate($name, $template, $vars = [], $priority = 0)
    {
        if (is_int($vars)) {
            $priority = $vars;
            $vars = [];
        }

        $model = new ViewModel($vars);
        $model->setTemplate($template);

        return $this->addViewModel($name, $model, $priority);
    }

    /**
     * Gets the iterator of the view model list.
     *
     * @return PriorityList Sorted list for iteration.
     */
    public function getViewModels()
    {
        return $this->models->getIterator();
    }

    /**
     * Creates a view model with default template,
     *
     * For syntax of the $data array, see the default template
     * .. Core/view/core/admin/dashboard-widget.phtml
     *
     * @param string      $name Name of the captureTo key.
     * @param array $data
     * @param int   $priority
     *
     * @return AdminControllerEvent
     * @throws \DomainException if no name is passed.
     */
    public function addViewVariables($name, $data = [], $priority = 0)
    {
        if (is_array($name)) {
            if (!isset($name['name'])) {
                throw new \DomainException('Key "name" must be specified, if array is passed as first parameter.');
            }
            if (is_int($data)) {
                $priority = $data;
            }
            $data = $name;
            $name = $data['name'];
        } elseif (is_int($data)) {
            $priority = $data;
            $data = [];
        }

        if (!isset($data['name'])) {
            $data['name'] = $name;
        }

        return $this->addViewTemplate($name, "core/admin/dashboard-widget", $data, $priority);
    }
}
