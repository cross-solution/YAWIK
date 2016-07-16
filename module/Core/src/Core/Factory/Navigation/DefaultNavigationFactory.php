<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2016 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace Core\Factory\Navigation;

use Zend\Mvc\Router\RouteMatch;
use Zend\Mvc\Router\RouteStackInterface as Router;
use Zend\Navigation\Service\DefaultNavigationFactory as ZfDefaultNavigationFactory;

/**
 * Extends the ZF DefaultNavigationFactory to let it set
 * active flags on pages when the route matches an entry
 * in the "active_on" option.
 * 
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @since 0.26
 */
class DefaultNavigationFactory extends ZfDefaultNavigationFactory
{

    /**
     * Inject components into the pages.
     *
     * @internal
     *      Parses the pages options for the "active_on" property and
     *      sets the active flag if one of the routes match.
     *
     * @param array      $pages
     * @param RouteMatch $routeMatch
     * @param Router     $router
     * @param null       $request
     *
     * @return array
     */
    protected function injectComponents(
        array $pages,
        RouteMatch $routeMatch = null,
        Router $router = null,
        $request = null
    ) {
        $routeName = $routeMatch->getMatchedRouteName();

        foreach ($pages as &$page) {
            if (isset($page['active_on']) && in_array($routeName, (array) $page['active_on'])) {
                $page['active'] = true;
            }
        }

        return parent::injectComponents($pages, $routeMatch, $router, $request);
    }


}