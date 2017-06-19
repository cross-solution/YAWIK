<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2017 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace Jobs\Factory\Listener;

use Jobs\Listener\LoadActiveOrganizations;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

/**
 * Factory for \Jobs\Listener\LoadActiveOrganizations
 * 
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @since 0.30
 */
class LoadActiveOrganizationsFactory implements FactoryInterface
{
    
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $paginators = $container->get('Core/PaginatorService');
        $paginator  = $paginators->get('Jobs\Paginator\ActiveOrganizations');
        $listener   = new LoadActiveOrganizations($paginator);

        return $listener;

    }
}
