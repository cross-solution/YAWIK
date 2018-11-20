<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2016 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace Core\Listener;

use Core\Controller\AdminControllerEvent;
use Core\ModuleManager\Feature\VersionProviderInterface;
use Zend\ModuleManager\ModuleManager;

/**
 * ${CARET}
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @todo write test
 */
class ModuleVersionAdminWidgetProvider
{
    /**
     *
     *
     * @var ModuleManager
     */
    private $moduleManager;

    public function __construct(ModuleManager $moduleManager)
    {
        $this->moduleManager = $moduleManager;
    }


    public function __invoke(AdminControllerEvent $event)
    {
        /* @var \Core\Controller\AdminController $controller */
        $modules = $this->moduleManager->getLoadedModules();
        $modules = array_filter(
            $modules,
            function($i) {
                return 0 !== strpos($i, 'Zend\\') && 0 !== strpos($i, 'Doctrine');
            },
            ARRAY_FILTER_USE_KEY
        );
        ksort($modules);

        $event->addViewTemplate('modules', 'core/admin/module-version-widget.phtml', ['modules' => $modules], 100);
    }
}

