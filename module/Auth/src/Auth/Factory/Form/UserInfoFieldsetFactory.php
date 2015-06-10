<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013-2015 Cross Solution (http://cross-solution.de)
 * @license       MIT
 */

namespace Auth\Factory\Form;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Core\Entity\Hydrator\Strategy\FileUploadStrategy;
use Core\Entity\Hydrator\EntityHydrator;
use Auth\Entity\UserImage;
use Auth\Form\UserInfoFieldset;

class UserInfoFieldsetFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $services= $serviceLocator->getServiceLocator();
        $user = $services->get('AuthenticationService')->getUser();
        $fieldset     = new UserInfoFieldset();
        $imageEntity  = new UserImage();
        $imageEntity->setUser($user);
        $strategy     = new FileUploadStrategy($imageEntity);
        $hydrator     = new EntityHydrator();
        $hydrator->addStrategy('image', $strategy);
        $fieldset->setHydrator($hydrator);
        return $fieldset;
    }
}