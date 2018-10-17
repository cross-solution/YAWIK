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

use Zend\Navigation\Service\DefaultNavigationFactory as ZfDefaultNavigationFactory;
use Zend\Mvc\Router as MvcRouter;
use Zend\Mvc\Router\RouteMatch;

/**
 * Extends the ZF DefaultNavigationFactory to let it set
 * active flags on pages when the route matches an entry
 * in the "active_on" option.
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @since 0.26
 * @since 0.30 - will do nothing if $routeMatch is null
 *               (prevent fatal error)
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
     * {@inheritDoc}
     * @since 0.30 add check, if $routeMatch is null
     */
    protected function injectComponents(
        array $pages,
        $routeMatch = null,
        $router = null,
        $request = null
    ) {
        if ($routeMatch) {
            /* @var RouteMatch|MvcRouter\RouteMatch $routeMatch */
            $routeName = $routeMatch->getMatchedRouteName();

            foreach ($pages as &$page) {
                if (isset($page['active_on']) && in_array($routeName, (array) $page['active_on'])) {
                    $page['active'] = true;
                }
            }
        }

        return parent::injectComponents($pages, $routeMatch, $router, $request);
    }
}
