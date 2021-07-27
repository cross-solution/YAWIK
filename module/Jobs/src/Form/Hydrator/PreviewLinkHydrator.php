<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright https://yawik.org/COPYRIGHT.php
 * @license   MIT
 * @author    weitz@cross-solution.de
 */

namespace Jobs\Form\Hydrator;

use Core\Entity\Hydrator\EntityHydrator;
use Laminas\ServiceManager\ServiceLocatorInterface;

class PreviewLinkHydrator extends EntityHydrator
{

    /**
     * @var ServiceLocatorInterface
     */
    protected $serviceManager;

    /**
     * @param ServiceLocatorInterface $serviceManager
     */
    public function __construct(ServiceLocatorInterface $serviceManager)
    {
        parent::__construct();
        $this->serviceManager = $serviceManager;
        $this->init();
    }

    protected function init()
    {
    }

    /* (non-PHPdoc)
     * @see \Laminas\Hydrator\HydratorInterface::extract()
     */
    public function extract($object): array
    {
        $controllerPluginManager = $this->serviceManager->get('controllerPluginManager');

        $data = parent::extract($object);
        $viewLink = $controllerPluginManager->get('url')->fromRoute(
            'lang/jobs/view',
            array(
              ),
            array(
                  'query' => array(
                      'id' => $data['id'],
                  )
              )
        );

        $data['previewLink']         = $viewLink;
        return $data;
    }

    public function hydrate(array $data, $object)
    {
        $object = parent::hydrate($data, $object);
        return $object;
    }

    /**
     * @param ServiceLocatorInterface $serviceManager
     * @return \Jobs\Form\Hydrator\PreviewLinkHydrator
     */
    public static function factory(ServiceLocatorInterface $serviceManager)
    {
        return new static($serviceManager);
    }
}
