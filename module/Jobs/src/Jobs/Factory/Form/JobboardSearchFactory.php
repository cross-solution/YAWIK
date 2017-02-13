<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2016 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace Jobs\Factory\Form;

use Core\Factory\Form\AbstractCustomizableFieldsetFactory;
use Interop\Container\ContainerInterface;
use Jobs\Form\JobboardSearch;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Factory for the ListFilterLocation (Job Title and Location)
 *
 * @author Carsten Bleek <bleek@cross-solution.de>
 */
class JobboardSearchFactory extends AbstractCustomizableFieldsetFactory
{

    const OPTIONS_NAME = 'Jobs/JobboardSearchOptions';

    protected function createFormInstance(ContainerInterface $container, $name, array $options = null)
    {
        /* @var \Geo\Options\ModuleOptions $options */
        $options = $container->get('Geo/Options');
        /* @var \Jobs\Options\JobboardSearchOptions $jobboardSearchOptions */
        $fs = new JobboardSearch([
                                     'location_engine_type' => $options->getPlugin(),
                                     'button_element' => 'd',
                                 ]);


        return $fs;
    }

}
