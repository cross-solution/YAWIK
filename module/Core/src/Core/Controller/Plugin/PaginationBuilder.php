<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2016 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace Core\Controller\Plugin;

use Zend\Mvc\Controller\Plugin\AbstractPlugin;

/**
 * ${CARET}
 * 
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @todo write test 
 */
class PaginationBuilder extends AbstractPlugin
{

    protected $result = [];

    public function __invoke($stack = null, $returnResult = true)
    {
        if (true === $stack) {
            $this->stack = [];
            return $this;
        }

        if (null === $stack) {
            return $this;
        }

        if (!is_array($stack)) {
            throw new \InvalidArgumentException('Expected Argument to be of type array, but received ' . gettype($stack));
        }

        $this->stack = $stack;

        return $returnResult ? $this->getResult() : $this;
    }

    public function paginator($paginatorName, $defaultParams = [], $usePostParams = false, $as = 'paginator')
    {
        if (is_string($defaultParams)) {
            $as = $defaultParams;
            $defaultParams = [];
        } else if (is_string($usePostParams)) {
            $as = $usePostParams;
            $usePostParams = false;
        }

        $this->stack['paginator'] = ['as' => $as, $paginatorName, $defaultParams, $usePostParams];
        return $this;
    }

    public function form($elementsFieldset, $buttonsFieldset = null, $as = 'searchform')
    {
        if (null !== $buttonsFieldset && 0 === strpos($buttonsFieldset, '@')) {
            $as = substr($buttonsFieldset, 1);
            $buttonsFieldset = null;
        }

        $this->stack['form'] = ['as' => $as, $elementsFieldset, $buttonsFieldset];
        return $this;
    }

    public function params($namespace, $defaults = [ 'page' => 1 ])
    {
        $this->stack['params'] = [$namespace, $defaults];
        return $this;
    }

    public function getResult($paginatorAlias = null, $formAlias = null)
    {
        if (null === $paginatorAlias) {
            $paginatorAlias = isset($this->stack['paginator']['as'])
                            ? $this->stack['paginator']['as']
                            : 'paginator';
        }

        if (null === $formAlias) {
            $formAlias = isset($this->stack['form']['as']) ? $this->stack['form']['as'] : 'searchform';
        }

        $result = [];

        if (isset($this->stack['params'])) {
            $this->callPlugin('paginationParams', $this->stack['params']);
        }

        if (isset($this->stack['form']) && !$this->getController()->getRequest()->isXmlHttpRequest()) {
            $result[$formAlias] = $this->callPlugin('searchform', $this->stack['form']);
        }

        if (isset($this->stack['paginator'])) {
            $result[$paginatorAlias] = $this->callPlugin('paginator', $this->stack['paginator']);
        }

        return $result;
    }

    protected function callPlugin($name, $args)
    {
        $controller = $this->getController();
        $plugin = $controller->plugin($name);

        /* We remove the array entry with the key "as" here.
         * because we want to keep it in the stack array */
        unset($args['as']);

        return call_user_func_array($plugin, $args);
    }
}