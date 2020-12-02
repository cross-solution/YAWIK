<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright https://yawik.org/COPYRIGHT.php
 */

/** */
namespace Jobs\Factory\Paginator;

use Core\Paginator\Adapter\DoctrineMongoAdapter;
use Core\Repository\RepositoryService;
use Interop\Container\ContainerInterface;
use Laminas\Http\PhpEnvironment\Request;
use Laminas\Paginator\Paginator;
use Laminas\ServiceManager\Factory\FactoryInterface;

/**
 * Factory creates a paginator to paginate all active organizations.
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @since 0.30
 */
class ActiveOrganizationsPaginatorFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        /* @var RepositoryService $repositories
         * @var Request $request */
        $repositories   = $container->get('repositories');
        $repository     = $repositories->get('Jobs');
        $request        = $container->get('Request');
        $query          = $request->getQuery();
        $term           = $query->get('q');
        $qb             = $repository->findActiveOrganizations($term, false);
        $adapter        = new DoctrineMongoAdapter($qb);

        return new Paginator($adapter);
    }
}
