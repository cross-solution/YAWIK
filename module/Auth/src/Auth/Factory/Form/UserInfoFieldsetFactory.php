<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license       MIT
 */

namespace Auth\Factory\Form;

use Auth\Options\UserInfoFieldsetOptions;
use Core\Entity\FileMetadata;
use Core\Service\FileManager;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Core\Entity\Hydrator\Strategy\FileUploadStrategy;
use Core\Entity\Hydrator\EntityHydrator;
use Core\Factory\Form\AbstractCustomizableFieldsetFactory;
use Auth\Entity\UserImage;
use Auth\Form\UserInfoFieldset;

class UserInfoFieldsetFactory extends AbstractCustomizableFieldsetFactory implements FactoryInterface
{
    const OPTIONS_NAME = UserInfoFieldsetOptions::class;
    /**
     * Create an UserInfoFieldset.
     *
     * @param  ContainerInterface $container
     * @param  string             $requestedName
     * @param  null|array         $options
     *
     * @return UserInfoFieldset
     */
    protected function createFormInstance(ContainerInterface $container, $requestedName, array $options = null)
    {
        $user = $container->get('AuthenticationService')->getUser();
        $fileManager  = $container->get(FileManager::class);
        $fieldset     = new UserInfoFieldset();
        $strategy     = new FileUploadStrategy($fileManager, $user, FileMetadata::class, UserImage::class);
        $hydrator     = new EntityHydrator();
        $hydrator->addStrategy('image', $strategy);
        $fieldset->setHydrator($hydrator);
        return $fieldset;
    }
}
