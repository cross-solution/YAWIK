<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013-2015 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

/**  */ 
namespace Auth\Form;

use Applications\Options\ModuleOptions;
use Core\Form\FileUploadFactory;
use Auth\Entity\UserImage;
/**
 *
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 */
class UserImageFactory extends FileUploadFactory
{
    protected $fileName = 'image';
    protected $fileEntityClass = 'Auth\Entity\UserImage';
    protected $configKey = 'user_image';
    
    protected function configureForm($form, ModuleOptions $options)
    {

        $form->get($this->fileName)->setViewHelper('FormImageUpload')
                                   ->setMaxSize($options->getContactImageMaxSize())
                                   ->setAllowedTypes($options->getContactImageMimeType())
                                   ->setForm($form);
                                   
    }
} 
