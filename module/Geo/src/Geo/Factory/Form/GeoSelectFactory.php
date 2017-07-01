<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2017 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace Geo\Factory\Form;

use Geo\Form\GeoSelect;
use Geo\Form\GeoSelectHydratorStrategy;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

/**
 * ${CARET}
 * 
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @author Anthonius Munthi <me@itstoni.com>
 * @todo write test 
 */
class GeoSelectFactory implements FactoryInterface
{

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        /* @var \Geo\Options\ModuleOptions $geoOptions */
        $geoOptions = $container->get('Geo/Options');

        $select = new GeoSelect();

        //$select->setAttribute('data-type', $geoOptions->getPlugin());

        $client = $container->get('Geo/Client');
        $strategy = new GeoSelectHydratorStrategy($client);

        $select->setHydratorStrategy($strategy);

        return $select;
    }
}