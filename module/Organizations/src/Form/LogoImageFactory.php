<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

namespace Organizations\Form;

use Core\Form\FileUploadFactory;
use Organizations\Form\Hydrator\ImageHydrator;
use Zend\Stdlib\AbstractOptions;

/**
 * Class LogoImageFactory
 *
 * @package Organizations\Form
 */
class LogoImageFactory extends FileUploadFactory
{
    protected $fileName = 'original';
    protected $fileEntityClass = '\Organizations\Entity\OrganizationImage';
    protected $configKey = 'organization_logo_image';

    /**
     * abstract options defined in "Applications/Options"
     *
     * @var string
     */
    protected $options="Jobs/Options";

    /**
     * Configure the Form width Options
     *
     * @param \Core\Form\Form $form
     * @param AbstractOptions $options
     */
    protected function configureForm($form, AbstractOptions $options)
    {
        $size = $options->getCompanyLogoMaxSize();
        $type = $options->getCompanyLogoMimeType();
        
        $form->get($this->fileName)->setViewHelper('formImageUpload')
            ->setMaxSize($size)
            ->setAllowedTypes($type)
            ->setForm($form);

        $form->setIsDescriptionsEnabled(true);
        $form->setDescription(
            /*@translate*/ 'Choose a Logo. This logo will be shown in the job opening and the application form.'
        );

        //$form->setHydrator(new ImageHydrator());
    }
}
