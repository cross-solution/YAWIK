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

use Core\View\Helper\Proxy\HelperProxy;
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
 *      // Proxy view helper will return a {@link \Core\View\Helper\Proxy\HelperProxy} instance
 *      // which will return null on every method call, if the helper does not exist.
 * <pre>
 *
 * Advanced usage:
 *
 * - Load a helper without risking an exception:
 *   This will return a {@link \Core\View\Helper\Proxy\HelperProxy} instance.
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
 * - Call a method on a plugin
 *   (see {@link HelperProxy::call()})
 *   <pre>
 *      $this->proxy('helper')->call('method', ['arg1',...], 'expectedValue');
 *   </pre>
 *
 * - Call consecutive methods on plugin
 *   (see {@link HelperProxy::consecutive()})
 *   <pre>
 *      $this->proxy('helper')->consecutive(['method1', 'method2' => ['arg1', ... ], ...]);
 *   </pre>
 *
 * - Call chained methods on plugin
 *   (see {@link HelperProxy::chain()})
 *   <pre>
 *      $this->proxy('helper')->chain(['method' => ['arg'], 'method2'], 'expectedValue');
 *   </pre>
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @since 0.29
 */
class Proxy extends AbstractHelper
{
    /**
     * Loads and possiblity executes a view helper.
     *
     * @param null|string   $plugin
     * @param array|string  $args
     * @param mixed         $expect
     *
     * @return mixed|self|ProxyNoopHelper|ProxyNoopIterator
     */
    public function __invoke($plugin = null, $args = null, $expect = HelperProxy::EXPECT_SELF)
    {
        if (null === $plugin) {
            return $this;
        }

        $plugin = $this->plugin($plugin);

        if (null === $args) {
            return $plugin;
        }

        return $plugin->call('__invoke', $args, $expect);
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
     * @return bool|HelperProxy
     */
    public function plugin($plugin, $options = null)
    {
        $renderer = $this->getView();

        if (!method_exists($renderer, 'getHelperPluginManager')) {
            return true === $options ? false : new HelperProxy(false);
        }

        /* @var \Zend\View\HelperPluginManager $manager */
        $manager   = $renderer->getHelperPluginManager();
        $hasPlugin = $manager->has($plugin);

        if (true === $options) {
            return $hasPlugin;
        }

        if ($hasPlugin) {
            $pluginInstance = $manager->get($plugin, $options);
        } else {
            $pluginInstance = false;
        }

        return new HelperProxy($pluginInstance);
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
