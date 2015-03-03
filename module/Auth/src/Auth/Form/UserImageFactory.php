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

use Zend\Stdlib\AbstractOptions;
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

    /**
     * Use abstract options defined in "Applications/Options"
     *
     * @var string
     */
    protected $options = 'Applications/Options';

    /**
     * Configure the file upload formular with Applications/Options
     *
     * @param \Core\Form\Form $form
     * @param AbstractOptions $options
     */
    protected function configureForm($form, AbstractOptions $options)
    {

        $form->get($this->fileName)->setViewHelper('FormImageUpload')
                                   ->setMaxSize($options->getContactImageMaxSize())
                                   ->setAllowedTypes($options->getContactImageMimeType())
                                   ->setForm($form);
                                   
    }
} 
