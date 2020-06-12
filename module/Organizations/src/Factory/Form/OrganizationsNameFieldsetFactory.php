<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright https://yawik.org/COPYRIGHT.php
 */

namespace Organizations\Factory\Form;

use Interop\Container\ContainerInterface;
use Organizations\Form\OrganizationsNameFieldset;
use Laminas\ServiceManager\Factory\FactoryInterface;

/**
 * Class OrganizationsNameFieldsetFactory
 *
 * @author Anthonius Munthi <me@itstoni.com>
 * @package Organizations\Factory\Form
 * @since 0.29
 */
class OrganizationsNameFieldsetFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array|null $options
     *
     * @inheritdoc
     * @return OrganizationsNameFieldset
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $fieldset = new OrganizationsNameFieldset($requestedName, $options);

        $fieldset->setRepositories($container->get('repositories'));
        return $fieldset;
    }
}
