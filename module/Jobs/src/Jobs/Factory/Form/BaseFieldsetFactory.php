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
use Jobs\Form\BaseFieldset;


/**
 * Factory for the BaseFieldset (Job Title and Location)
 *
 * @author Carsten Bleek <bleek@cross-solution.de>
 */
class BaseFieldsetFactory extends AbstractCustomizableFieldsetFactory
{

    const OPTIONS_NAME = 'Jobs/BaseFieldsetOptions';

    protected function createFormInstance(ContainerInterface $container, $name, array $options = null) {
        /* @var \Geo\Options\ModuleOptions $options */
        $options = $container->get('Geo/Options');

        $fs = new BaseFieldset(
            [
                'location_engine_type' => $options->getPlugin(),
            ]
        );

        $fs->setLocationEngineType($options->getPlugin());

        return $fs;
    }
}
