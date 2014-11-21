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
    protected $configKey = 'user_image';
    
    protected function configureForm($form)
    {
        $size = isset($this->config['max_size']) ? $this->config['max_size'] : 100000;
        $type = isset($this->config['mimetype']) ? $this->config['mimetype'] : 'image';

        $form->get($this->fileName)->setViewHelper('FormImageUpload')
                                   ->setMaxSize($size)
                                   ->setAllowedTypes($type)
                                   ->setForm($form);
                                   
    }
} 
