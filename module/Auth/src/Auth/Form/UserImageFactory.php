<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013-2014 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

/**  */ 
namespace Auth\Form;

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
    protected $fileEntityClass = '\Auth\Entity\UserImage';
    
    protected function configureForm($form)
    {
        $form->get($this->fileName)->setViewHelper('FormImageUpload')
                                   ->setMaxSize(1000000)
                                   ->setAllowedTypes('image/');
                                   
    }
} 
