<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright https://yawik.org/COPYRIGHT.php
 * @license       MIT
 */

namespace Organizations\Factory\Controller;

use Interop\Container\ContainerInterface;
use Organizations\Controller\IndexController;
use Organizations\Form;
use Laminas\ServiceManager\Factory\FactoryInterface;

class IndexControllerFactory implements FactoryInterface
{
    /**
     * Create a IndexController controller
     *
     * @param  ContainerInterface $container
     * @param  string             $requestedName
     * @param  null|array         $options
     *
     * @return IndexController
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $organizationRepository = $container->get('repositories')->get('Organizations/Organization');
        $form = new Form\Organizations(null);
        $formManager = $container->get('FormElementManager');
        $viewHelper = $container->get('ViewHelperManager');
        $translator = $container->get('translator');

        return new IndexController($form, $organizationRepository, $translator, $formManager, $viewHelper);
    }
}
