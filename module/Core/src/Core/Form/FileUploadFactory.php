<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013-2014 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

/**  */ 
namespace Core\Form;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Core\Entity\Hydrator\EntityHydrator;
use Core\Entity\Hydrator\Strategy\FileUploadStrategy;
use Auth\Entity\AnonymousUser;
use Core\Entity\Hydrator\FileCollectionUploadHydrator;

/**
 *
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 */
class FileUploadFactory implements FactoryInterface
{
    protected $fileElement = 'Core/FileUpload';
    protected $fileName = 'file';
    protected $fileEntityClass = '\Core\Form\FileEntity';
    protected $multiple = false;
    
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $form = new Form();
        $serviceLocator->injectFactory($form);
        $form->add(array(
            'type' => $this->fileElement,
            'name' => $this->fileName,
            'attributes' => array(
                'class' => 'hide',
            ),
        ));
        $form->get($this->fileName)->setIsMultiple($this->multiple);
        
        $user = $serviceLocator->getServiceLocator()->get('AuthenticationService')->getUser();
        $fileEntity = new $this->fileEntityClass();
        if ($user instanceOf AnonymousUser) {
            $fileEntity->getPermissions()->grant($user, 'all');
        } else {
            $fileEntity->setUser($user);
        }
        
        $strategy = new FileUploadStrategy($fileEntity);
        if ($this->multiple) {
            $hydrator = new FileCollectionUploadHydrator($this->fileName, $strategy);
            $form->add(array(
                'type' => 'button',
                'name' => 'remove',
                'options' => array(
                    'label' => /*@translate*/ 'Remove all',
                ),
                'attributes' => array(
                    'class' => 'fu-remove-all btn btn-danger btn-xs pull-right'
                ),
            ));
        } else {
            $hydrator = new EntityHydrator();
            $hydrator->addStrategy($this->fileName, $strategy);
        }
        
        $form->setHydrator($hydrator);
        $form->setOptions(array('use_files_array' => true));
        
        $this->configureForm($form);
        return $form;
    }
    
    protected function configureForm($form)
    { }
    
}
