<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license       MIT
 */

namespace Auth\Factory\Form;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;
use Auth\Form\UserStatusFieldset;
use Auth\Entity\Status;
class UserStatusFieldsetFactory implements FactoryInterface
{
    /**
     * Create an UserStatusFieldset
     *
     * @param  ContainerInterface $container
     * @param  string             $requestedName
     * @param  null|array         $options
     *
     * @return UserStatusFieldset
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $translator = $container->get('translator');
        $statusOptions = (new Status())->getOptions($translator);
        $fieldset = new UserStatusFieldset();
        $fieldset->setStatusOptions($statusOptions);

        return $fieldset;
    }
}
