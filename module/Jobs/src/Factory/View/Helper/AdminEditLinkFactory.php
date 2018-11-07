<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2017 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace Jobs\Factory\View\Helper;

use Interop\Container\ContainerInterface;
use Jobs\View\Helper\AdminEditLink;
use Zend\ServiceManager\Factory\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Factory for AdminEditLink view helper
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @since 0.29
 */
class AdminEditLinkFactory implements FactoryInterface
{

    /**
     * Create service
     *
     * @param ContainerInterface $container
     * @param string             $requestedName
     * @param array              $options
     *
     * @return AdminEditLink
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $request = $container->get('Request');
        $urlHelper = $container->get('ViewHelperManager')->get('url');
        $returnUrl = $urlHelper(null, [], ['query' => $request->getQuery()->toArray()], true);

        return new AdminEditLink($urlHelper, $returnUrl);
    }
}
