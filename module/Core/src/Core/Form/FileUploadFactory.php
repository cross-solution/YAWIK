<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013-2014 Cross Solution (http://cross-solution.de)
 * @license   AGPLv3
 */

/**  */ 
namespace Core\Form;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Core\Entity\Hydrator\EntityHydrator;
use Core\Entity\Hydrator\Strategy\FileUploadStrategy;
use Auth\Entity\AnonymousUser;

/**
 *
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 */
class FileUploadFactory implements FactoryInterface
{
    
    protected $fileName = 'file';
    protected $fileEntityClass = '\Core\Form\FileEntity';
    
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $form = new Form();
        $serviceLocator->injectFactory($form);
        $form->add(array(
            'type' => 'Core/FileUpload',
            'name' => $this->fileName,
            'attributes' => array(
                'class' => 'hide',
            ),
        ));
        
        $user = $serviceLocator->getServiceLocator()->get('AuthenticationService')->getUser();
        $fileEntity = new $this->fileEntityClass();
        if ($user instanceOf AnonymousUser) {
            $fileEntity->getPermissions()->grant($user, 'all');
        } else {
            $fileEntity->setUser($user);
        }
        
        $hydrator = new EntityHydrator();
        $hydrator->addStrategy($this->fileName, new FileUploadStrategy($fileEntity));
        
        $form->setHydrator($hydrator);
        $form->setOptions(array('use_files_array' => true));
        
        return $form;
    }
    
}
