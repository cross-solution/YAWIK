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
use Zend\Stdlib\Parameters;

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
     * Internal parameters.
     *
     * @var Parameters
     */
    protected $parameters;

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

        $stack = array_intersect_key($stack, ['params' => true, 'form' => true, 'paginator' => true]);
        foreach ($stack as $method => $args) {
            if (isset($args['as'])) {
                array_push($args, $args['as']);
                unset($args['as']);
            }
            call_user_func_array([$this, $method], $args);
        }

        return $returnResult ? $this->getResult() : $this;
    }

    /**
     * Add arguments for the call to the CreatePaginator plugin.
     *
     * @see \Core\Controller\Plugin\CreatePaginator::__invoke()
     *
     * @param string       $paginatorName
     * @param array  $defaultParams
     * @param string $as The name of the key in the result array.
     *
     * @return self
     */
    public function paginator($paginatorName, $defaultParams = [], $as = 'paginator')
    {
        if (is_string($defaultParams)) {
            $as = $defaultParams;
            $defaultParams = [];
        }

        $this->stack['paginator'] = ['as' => $as, $paginatorName, $defaultParams];
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
    public function form($form, $options = null, $as = 'searchform')
    {
        if (is_string($options)) {
            $as = $options;
            $options = null;
        }

        $this->stack['form'] = ['as' => $as, $form, $options];
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

        $this->setParameters($request->getQuery());

        if (isset($this->stack['params'])) {
            $this->callPlugin('paginationParams', $this->stack['params']);
        }

        if (isset($this->stack['form'])) {
            $form = $this->callPlugin('searchform', $this->stack['form']);
            if (!$request->isXmlHttpRequest()) {
                $result[$formAlias] = $form;
            }
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

        /* Inject the internal parameters as last argument.
         * This is needed to prevent messing with the original query params. */
        array_push($args, $this->parameters);

        return call_user_func_array($plugin, $args);
    }

    protected function setParameters($query)
    {
        $queryArray = $query->toArray();

        foreach ($queryArray as $key => $value) {
            if (preg_match('~^(?<separator>\W|_)(?<setArrayKeys>\W|_)?(?<name>.*)$~', $key, $match)) {
                $value = explode($match['separator'], $value);
                if ('' !== $match['setArrayKeys']) {
                    foreach ($value as $v) {
                        $queryArray[$match['name']][$v] = 1;
                    }
                } else {
                    $queryArray[ $match[ 'name' ] ] = $value;
                }
                unset($queryArray[$key]);
            }
        }

        $this->parameters = new Parameters($queryArray);
    }
}
