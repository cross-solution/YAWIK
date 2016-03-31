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

use ArrayAccess;
use Zend\EventManager\Event;
use Zend\Stdlib\PriorityList;
use Zend\View\Model\ViewModel;

/**
 * ${CARET}
 * 
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @todo write test 
 */
class AdminControllerEvent extends Event
{
    const EVENT_DASHBOARD = 'DASHBOARD_FETCH_MODELS';

    protected $models;
    protected $data;

    public function __construct($name = null, $target = null, $params = null)
    {
        parent::__construct($name, $target, $params);
        $this->models = new PriorityList();
    }

    public function addViewModel($name, $model, $priority=0)
    {
        $this->models->insert($name, $model, $priority);

        return $this;
    }

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

    public function getViewModels()
    {
        return $this->models->getIterator();
    }

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

        }

        if (!isset($data['name'])) {
            $data['name'] = $name;
        }

        return $this->addViewTemplate($name, "core/admin/dashboard-widget", $data, $priority);
    }


}