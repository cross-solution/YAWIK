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
use Core\Form\Form;
use Core\Form\FileUploadFactory;
use Applications\Options\ModuleOptions;
use Auth\Entity\UserImage;

/**
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 *
 * Class UserImageFactory
 * @package Auth\Form
 */
class UserImageFactory extends FileUploadFactory
{
    /**
     * Attribute name of the image stored in UserEntity
     *
     * @var string
     */
    protected $fileName = 'image';
    /**
     * @var string
     */
    protected $fileEntityClass = 'Auth\Entity\UserImage';
    /**
     * @var string
     */
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
     * @param Form $form
     * @param AbstractOptions $options
     */
    protected function configureForm($form, AbstractOptions $options)
    {

        /** @var ModuleOptions $options */

        $form->get($this->fileName)->setViewHelper('FormImageUpload')
                                   ->setMaxSize($options->getContactImageMaxSize())
                                   ->setAllowedTypes($options->getContactImageMimeType())
                                   ->setForm($form);
                                   
    }
} 
