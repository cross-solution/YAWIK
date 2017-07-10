<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2016 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace Cv\Factory\Controller;

use Cv\Controller\ViewController;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;;

/**
 * ${CARET}
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @author Miroslav Fedeleš <miroslav.fedeles@gmail.com>
 * @author Anthonius Munthi <me@itstoni.com>
 */
class ViewControllerFactory implements FactoryInterface
{

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $repository = $container->get('repositories')->get('Cv/Cv');
        $translator = $container->get('translator');

        return new ViewController($repository, $translator);
    }
}