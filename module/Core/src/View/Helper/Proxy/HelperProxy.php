<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2017 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace Core\View\Helper\Proxy;

use Zend\View\Helper\AbstractHelper;

/**
 * Proxies to a view helper which may or may not exists.
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @since 0.29
 */
class HelperProxy
{

    /**#@+
     * Special expected result types.
     * @var string
     */
    const EXPECT_SELF = '__SELF__';
    const EXPECT_ARRAY = '__ARRAY__';
    const EXPECT_ITERATOR = '__ITERATOR__';
    /**#@-*/

    /**
     * The helper instance.
     *
     * @var false|AbstractHelper
     */
    private $helper;

    /**
     * @param false|AbstractHelper $helper
     */
    public function __construct($helper)
    {
        $this->helper = $helper;
    }

    /**
     * Proxy invokation to the helper.
     *
     * Note:
     *      This will always return NULL.
     *
     * @return mixed
     */
    public function __invoke()
    {
        $args = func_get_args();

        return $this->call('__invoke', $args, null);
    }

    /**
     * Proxy all methods calls to the helper.
     *
     * Note:
     *      The expected value is NULL.
     *
     *      You may specify an alternate expect value when prefixing
     *      the method name with "call" and provide the expected value
     *      as the last argument. (This is popped of the args array before
     *      proxy to call(),)
     *
     *      e.g. callOriginalMethod(arg1, arg2, expectedValue)
     *
     * @param string $method
     * @param array  $args
     *
     * @return null|mixed
     */
    public function __call($method, $args)
    {
        if (0 === strpos($method, 'call')) {
            $method = substr($method, 4);
            $expect = array_pop($args);
        } else {
            $expect = null;
        }

        return $this->call($method, $args, $expect);
    }

    /**
     * Get the helper instance.
     *
     * @return false|AbstractHelper
     */
    public function helper()
    {
        return $this->helper;
    }

    /**
     * Call a method on the proxied plugin.
     *
     * If a helper instance exists, the returned value from the method call is returned,
     * otherwise the $expect value is returned,
     *
     * If the $args is no array, it is used as $expect.
     *
     * If you need to expect an array, you must pass in an empty array for $args and
     * specify your expected array in the $expect variable, however if you expect an
     * empty array, you can pass {@link EXPECT_ARRAY}.
     *
     * @param string $method
     * @param array|mixed  $args
     * @param mixed $expect
     *
     * @return mixed
     */
    public function call($method, $args = [], $expect = self::EXPECT_SELF)
    {
        if (!is_array($args)) {
            $expect = $args;
            $args   = [];
        }

        if (!$this->helper) {
            return $this->expected($expect);
        }

        return call_user_func_array([$this->helper, $method], $args);
    }

    /**
     * Call consecutive methods on this proxied helper.
     *
     * This means, every specified method is called on this
     * proxy instance (which proxies further to the helper)
     * and this proxy is returned.
     *
     * If no helper instance exists, this is a noop and this proxy instance is returned.
     *
     * <pre>
     * $methods = [
     *      'methodName',                   // <=> 'methodName' => []
     *      'methodName' => [arguments,...],
     *      ['invokeArg',...]               // omitting method name, but provide arguments will call "__invoke"
     * ];
     * </pre>
     *
     * @param array $methods
     *
     * @return self
     */
    public function consecutive($methods)
    {
        return $this->doStackedCalls($methods, self::EXPECT_SELF, 'consecutive');
    }

    /**
     * Chain method calls on this proxied helper.
     *
     * This means, every method in the stack is called on
     * the return value ofthe previous method and the returned value
     * from the last method is returned.
     *
     * If no helper instance exists, the $expect value is returned.
     *
     * <pre>
     * $stack = [
     *      'methodName',                   // <=> 'methodName' => []
     *      'methodName' => [arguments,...]
     *      ['invokeArg',...]               // omitting method name but provide arguments, will call "__invoke"
     * ];
     * </pre>
     *
     * @param array $methods
     * @param mixed $expect
     *
     * @return mixed
     */
    public function chain($methods, $expect = self::EXPECT_SELF)
    {
        return $this->doStackedCalls($methods, $expect, 'chain');
    }

    private function doStackedCalls($methods, $expect, $mode)
    {
        if (!$this->helper) {
            return $this->expected($expect);
        }

        $plugin = $result = $this->helper;
        foreach ($methods as $method => $args) {
            if (is_numeric($method)) {
                if (is_array($args)) {
                    $method = '__invoke';
                } else {
                    $method = $args;
                    $args   = [];
                }
            }

            if (!is_array($args)) {
                $args = [$args];
            }

            $result = call_user_func_array([$plugin, $method], $args);

            if ('chain' == $mode) {
                $plugin = $result;
            }
        }

        return 'consecutive' == $mode ? $this : $result;
    }

    private function expected($expect)
    {
        if (self::EXPECT_SELF == $expect) {
            return $this;
        }

        if (self::EXPECT_ARRAY == $expect) {
            return [];
        }

        if (self::EXPECT_ITERATOR == $expect) {
            return new \ArrayIterator();
        }

        return $expect;
    }
}
