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
 * Collects pagination related configuration and passes it to the appropriate
 * controller plugin.
 *
 * In the controller, you only have to call one plugin to do all the YAWIK pagination magic.
 *
 * @see \Core\Controller\Plugin\PaginationParams
 * @see \Core\Controller\Plugin\SearchForm
 * @see \Core\Controller\Plugin\CreatePaginator
 *
 * 
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @since 0.25
 */
class PaginationBuilder extends AbstractPlugin
{

    /**
     * The internal configuration stack.
     *
     * @var array
     */
    protected $stack = [];

    /**
     * The generated result array.
     *
     * @var array
     */
    protected $result = [];

    /**
     * Entry point.
     *
     * if $stack is provided, the internal stack is set to exact that value, so
     * please be sure what you're doing.
     *
     * If you pass a boolean TRUE as $stack, the internal stack is reset.
     *
     * @param null|array|bool $stack
     * @param bool $returnResult Should the result be immediately be returned instead of
     *                           self. Only affective when $stack is an array.
     *
     * @return self|array
     * @throws \InvalidArgumentException
     */
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
            throw new \InvalidArgumentException('Expected argument to be of type array, but received ' . gettype($stack));
        }

        $this->stack = $stack;

        return $returnResult ? $this->getResult() : $this;
    }

    /**
     * Add arguments for the call to the CreatePaginator plugin.
     *
     * @see \Core\Controller\Plugin\CreatePaginator::__invoke()
     *
     * @param string       $paginatorName
     * @param array  $defaultParams
     * @param bool   $usePostParams
     * @param string $as The name of the key in the result array.
     *
     * @return self
     */
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

    /**
     * Add arguments for the call to the SearchForm plugin.
     *
     * @see \Core\Controller\Plugin\SearchForm::get()
     *
     * @param        $elementsFieldset
     * @param null   $buttonsFieldset
     * @param string $as The name of the key in the result array.
     *
     * @return self
     */
    public function form($elementsFieldset, $buttonsFieldset = null, $as = 'searchform')
    {
        if (null !== $buttonsFieldset && 0 === strpos($buttonsFieldset, '@')) {
            $as = substr($buttonsFieldset, 1);
            $buttonsFieldset = null;
        }

        $this->stack['form'] = ['as' => $as, $elementsFieldset, $buttonsFieldset];
        return $this;
    }

    /**
     * Add arguments for the call to the PaginatorParams plugin.
     *
     * @see \Core\Controller\Plugin\PaginationParams::getParams()
     *
     * @param       $namespace
     * @param array $defaults
     *
     * @return self
     */
    public function params($namespace, $defaults = [ 'page' => 1 ])
    {
        $this->stack['params'] = [$namespace, $defaults];
        return $this;
    }

    /**
     * Calls the stacked plugins in the right order and returns the result array.
     *
     * The returned array can directly be returned from the controller or be used to populate a
     * view model.
     *
     * The search form plugin is only called (and thus the form only present in the result array)
     * if the request is NOT an ajax request. (as the  form is never rerendered on ajax requests)
     *
     * @param null|string $paginatorAlias Name of the paginator in the result array
     * @param null|string $formAlias Name of the search form in the result array
     *
     * @return array
     */
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

        /* @var \Zend\Mvc\Controller\AbstractController $controller
         * @var \Zend\Http\Request $request */
        $result = [];
        $controller = $this->getController();
        $request = $controller->getRequest();

        if (isset($this->stack['params'])) {
            $this->callPlugin('paginationParams', $this->stack['params']);
        }

        if (isset($this->stack['form']) && !$request->isXmlHttpRequest()) {
            $result[$formAlias] = $this->callPlugin('searchform', $this->stack['form']);
        }

        if (isset($this->stack['paginator'])) {
            $result[$paginatorAlias] = $this->callPlugin('paginator', $this->stack['paginator']);
        }

        return $result;
    }

    /**
     * Calls an invokable controller plugin.
     *
     * @param string $name
     * @param array $args
     *
     * @return mixed The return value of the called plugin.
     */
    protected function callPlugin($name, $args)
    {
        /* @var \Zend\Mvc\Controller\AbstractController $controller */
        $controller = $this->getController();
        $plugin = $controller->plugin($name);

        /* We remove the array entry with the key "as" here.
         * because we want to keep it in the stack array */
        unset($args['as']);

        return call_user_func_array($plugin, $args);
    }
}