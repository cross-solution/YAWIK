<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2017 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace Core\View\Helper {

use Zend\ServiceManager\Exception\ServiceNotCreatedException;
use Zend\ServiceManager\Exception\ServiceNotFoundException;
use Zend\View\Helper\AbstractHelper;

/**
 * View helper to safely use module specific view helpers in other modules.
 *
 * In most scenarios, to make something failsafe, it takes a lot of (redundant) code.
 * With this proxy helper, this can be simplified by just adding a few characters.
 *
 * <pre>
 *
 *      //
 *      // ORIGINAL
 *      //
 *      <?=$this->helper()->someOutput()?>
 *
 *      //
 *      // Failsafe code
 *      //
 *      <? if ($this->getHelperPluginManager()->has('plugin')):
 *              $plugin = $this->getHelperPluginManager()->get('helper');
 *
 *              // do something with the plugin, i.E. call some method
 *              echo $plugin->someOutput();
 *         else:
 *              // helper does not exist.
 *              // do something to hanlde this case.
 *              echo '';
 *         endif;
 *      ?>
 *
 *      //
 *      // Failsafe with Proxy:
 *      //
 *      <?=$this->proxy('helper')->someOutput()?>
 *
 *      // If 'helper' does not exist, a ProxyNoopHelper is returned, which
 *      // simply returns NULL on any method call and whose string representation is
 *      // an empty string.
 * <pre>
 *
 * Advanced usage:
 *
 * - Load a helper without risking an exception:
 *   This will return FALSE, if the helper does not exist.
 *   <pre><? $helper = $this->proxy()->plugin('HelperServiceName', [options])?></pre>
 *
 * - Call an invokable helper and expect an array returned.
 *   This will return an empty array, if the helper does not exist.
 *   <pre><? $array = $this->proxy('helper', Proxy::EXPECT_ARRAY)?></pre>
 *
 * - Expect other return types:
 *   <pre>
 *      // Object (which is default)
 *      $helper = $this->proxy('helper', Proxy::EXPECT_OBJECT);
 *
 *      // Iterator (returns an IteratorAggregate which returns an empty ArrayIterator.
 *      $helper = $this->proxy('helper', Proxy::EXPECT_ITERATOR);
 *
 *      // Skalar values: Simply specify the expected result, which will be returned,
 *      // if the helper does not exist.
 *      $value = $this->proxy('helper', ''); // return empty string.
 *      $value = $this->proxy('helper', false) // return FALSE
 *   </pre>
 *
 * - Directly calls an invokable helper with Arguments
 *   <pre>
 *      <?=$this->proxy('invokableHelper', ['arg1', 'arg2'], false)?>
 *   </pre>
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @since 0.29
 */
class Proxy extends AbstractHelper
{
    /**#@+
     * Special expected result types.
     * @var string
     */
    const EXPECT_ARRAY  = '__ARRAY__';
    const EXPECT_ITARATOR = '__ITERATOR__';
    const EXPECT_OBJECT = '__OBJECT__';

    /**#@-*/

    /**
     * Loads and possiblity executes a view helper.
     *
     * @param null|string   $plugin
     * @param array|string  $args
     * @param mixed         $expect
     *
     * @return mixed|self|ProxyNoopHelper|ProxyNoopIterator
     */
    public function __invoke($plugin = null, $args = array(), $expect = self::EXPECT_OBJECT)
    {
        if (null === $plugin) {
            return $this;
        }

        if (!is_array($args)) {
            $expect = $args;
            $args = [];
        }

        $plugin = $this->plugin($plugin);

        if ($plugin) {
            return is_callable($plugin)
            ? call_user_func_array($plugin, $args)
            : $plugin;
        }

        if (self::EXPECT_OBJECT == $expect) {
            return new Proxy\NoopHelper();
        }

        if (self::EXPECT_ITARATOR == $expect) {
            return new Proxy\NoopIterator();
        }

        if (self::EXPECT_ARRAY == $expect) {
            return [];
        }

        return $expect;
    }

    /**
     * Loads a plugin from the plugin helper manager.
     *
     * Returns false, if either the helper plugin manager cannot be
     * retrieved from the renderer or the requested plugin does not exist.
     *
     * @param string $plugin
     * @param true|array  $options if true, only return if plugin exists or not.
     *
     * @return bool|object
     */
    public function plugin($plugin, $options = null)
    {
        $renderer = $this->getView();

        if (!method_exists($renderer, 'getHelperPluginManager')) {
            return false;
        }

        /* @var \Zend\View\HelperPluginManager $manager */
        $manager   = $renderer->getHelperPluginManager();
        $hasPlugin = $manager->has($plugin);

        if (!$hasPlugin || true === $options) {
            return $hasPlugin;
        }

        return $manager->get($plugin, $options);
    }

    /**
     * Does a plugin exists?
     *
     * @param string $plugin
     *
     * @return bool
     */
    public function exists($plugin)
    {
        return $this->plugin($plugin, true);
    }
}

} // end namespace

namespace Core\View\Helper\Proxy {

/**
 * NoopHelper
 *
 * View helper which does nothing but returns null on any method call, and which
 * represents as empty string.
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @since 0.29
 */
class NoopHelper
{
    public function __call($method, $args)
    {
        return null;
    }

    public function __get($name)
    {
        return null;
    }

    public function __set($name, $value)
    {

    }

    public function __isset($name)
    {
        return false;
    }

    public function __toString()
    {
        return '';
    }
}

/**
 * NoopIterator
 *
 * View helper which returns an empty ArrayIterator when used as Iterator.
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @since 0.29
 */
class NoopIterator extends NoopHelper implements \IteratorAggregate
{
    public function getIterator()
    {
        return new \ArrayIterator([]);
    }
}

} // end namespace