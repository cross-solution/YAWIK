<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2017 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace Core\View\Helper;

use Core\EventManager\EventManager;
use Zend\EventManager\EventManagerInterface;
use Zend\Stdlib\ArrayUtils;
use Zend\View\Exception;
use Zend\View\Helper\AbstractHelper;
use Zend\View\Helper\Partial;
use Zend\View\Model\ModelInterface;
use Zend\View\Model\ViewModel;

/**
 * ${CARET}
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @todo write test
 */
class Snippet extends AbstractHelper
{

    /**
     *
     *
     * @var array
     */
    private $config = [];

    /**
     *
     *
     * @var EventManager
     */
    private $events;

    /**
     *
     *
     * @var \Zend\View\Helper\Partial
     */
    private $partials;

    public function __construct(Partial $partialHelper, EventManager $eventManager, array $config)
    {
        $this->config   = $config;
        $this->events   = $eventManager;
        $this->partials = $partialHelper;
    }

    public function __invoke($name, $values = [])
    {
        $snippets = ArrayUtils::merge($this->fromConfig($name), $this->fromEvent($name, $values));

        uasort($snippets, function ($a, $b) {
            $prioA = is_array($a) && isset($a['priority']) ? $a['priority'] : 0;
            $prioB = is_array($b) && isset($b['priority']) ? $b['priority'] : 0;

            return $prioA == $prioB ? 0 : ($prioA < $prioB ? 1 : -1);
        });

        $content = '';
        foreach ($snippets as $snippet) {
            $content .= $this->process($snippet, $values);
        }

        return $content;
    }

    private function fromConfig($name)
    {
        $snippets = isset($this->config[$name]) ? $this->config[$name] : [];

        if (is_string($snippets)) {
            $snippets = [$snippets];
        }

        return $snippets;
    }

    private function fromEvent($name, $values)
    {
        $event  = $this->events->getEvent($name, $this, $values);
        $results = $this->events->triggerEvent($event);

        $snippets = [];

        foreach ($results as $result) {
            if (null !== $result) {
                $snippets[] = $result;
            }
        }

        return $snippets;
    }

    /**
     *
     *
     * @param $item
     * @param $values
     *
     * @return string
     * @throws \UnexpectedValueException
     */
    private function process($item, $values)
    {
        if ($item instanceof ViewModel) {
            return $this->partials->__invoke($item);
        }

        if (is_string($item)) {
            if (false === strpos($item, ' ')) {
                return $this->partials->__invoke($item, $values);
            }

            $item = ['content' => $item];
        }

        if (!is_array($values)) {
            $values = ArrayUtils::iteratorToArray($values);
        }

        if (isset($item['values'])) {
            $values = ArrayUtils::merge($item['values'], $values);
        }

        if (isset($item['content'])) {
            $content = $item['content'];

            foreach ($values as $key => $val) {
                $content = str_replace('%' . $key . '%', $val, $content);
            }

            return $content;
        }

        if (isset($item['template'])) {
            return $this->partials->__invoke($item['template'], $values);
        }

        throw new \UnexpectedValueException('Snippet item must either be a string or an array with at least the keys "content" or "template".');
    }
}
