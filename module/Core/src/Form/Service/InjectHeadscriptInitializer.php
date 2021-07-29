<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2016 Cross Solution <http://cross-solution.de>
 */

/** */
namespace Core\Form\Service;

use Core\Form\HeadscriptProviderInterface;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Initializer\InitializerInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

/**
 * This initializer inject the scripts provided by form elements
 * which implements HeadscriptProviderInterface in the Headscript view helper.
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @since 0.19
 */
class InjectHeadscriptInitializer implements InitializerInterface
{
    public function __invoke(ContainerInterface $container, $instance)
    {
        /* @var $serviceLocator \Laminas\Form\FormElementManager\FormElementManager */

        if (!$instance instanceof HeadscriptProviderInterface) {
            return;
        }

        $scripts = $instance->getHeadscripts();

        if (!is_array($scripts) || empty($scripts)) {
            return;
        }

        /* @var $basepath \Laminas\View\Helper\BasePath
         * @var $headscript \Laminas\View\Helper\HeadScript */
        $helpers  = $container->get('ViewHelperManager');
        $basepath = $helpers->get('basepath');
        $headscript = $helpers->get('headscript');

        foreach ($scripts as $script) {
            $headscript->appendFile($basepath($script));
        }
    }

    /**
     * Injects scripts to the headscript view helper.
     *
     * If the created instance implements {@link HeadscriptProviderInterface},
     * the provided scripts will be injected in the Headscript view helper, prepended
     * with the base path.
     *
     * @param \Laminas\Form\ElementInterface | HeadscriptProviderInterface $instance
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return void
     */
    public function initialize($instance, ServiceLocatorInterface $serviceLocator)
    {
        /* @var $serviceLocator \Laminas\Form\FormElementManager\FormElementManager */

        if (!$instance instanceof HeadscriptProviderInterface) {
            return;
        }

        $scripts = $instance->getHeadscripts();

        if (!is_array($scripts) || empty($scripts)) {
            return;
        }

        /* @var $basepath \Laminas\View\Helper\BasePath
         * @var $headscript \Laminas\View\Helper\HeadScript */
        $services = $serviceLocator;
        $helpers  = $services->get('ViewHelperManager');
        $basepath = $helpers->get('basepath');
        $headscript = $helpers->get('headscript');

        foreach ($scripts as $script) {
            $headscript->appendFile($basepath($script));
        }
    }
}
