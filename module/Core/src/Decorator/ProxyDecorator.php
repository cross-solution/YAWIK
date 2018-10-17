<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2016 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace Core\Decorator;

/**
 * Proxy Decorator boilerplate
 *
 * Allows proxying to the wrapped object and returns self, if wrapped object method returns itself.
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @since 0.20
 */
class ProxyDecorator extends Decorator
{

    /**
     * Proxies to the wrapped entity class.
     *
     * The first argument must be the method name to proxy to, all further arguments
     * passed in are passed to the proxied method.
     *
     * If the wrapped object returns itself, this method will return this decorator instead.
     *
     * @return Decorator|mixed
     * @throws \BadMethodCallException If wrapped entity does not have the specified method.
     */
    protected function proxy()
    {
        $args = func_get_args();
        $method = array_shift($args);
        $callback = array($this->object, $method);

        if (!is_callable($callback)) {
            throw new \BadMethodCallException(
                sprintf(
                    'Cannot proxy "%s" to "%s": Unknown method.',
                    $method,
                    get_class($this->object)
                )
            );
        }

        $return = call_user_func_array($callback, $args);

        if ($return === $this->object) {
            // Return this decorator instead of the wrapped entity.
            $return = $this;
        }

        return $return;
    }
}
