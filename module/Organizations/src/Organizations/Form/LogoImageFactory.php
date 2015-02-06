<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013-2015 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

namespace Organizations\Form;

use Core\Form\FileUploadFactory;
use Applications\Options\ModuleOptions; // als log we have no organization options, we use the options from
                                        // the applications module

class LogoImageFactory extends FileUploadFactory
{
    protected $fileName = 'image';
    protected $fileEntityClass = '\Organizations\Entity\OrganizationImage';
    protected $configKey = 'organization_logo_image';

    protected function configureForm($form, ModuleOptions $options)
    {
        $size = $options->getContactImageMaxSize();
        $type = $options->getContactImageMimeType();

        $form->get($this->fileName)->setViewHelper('FormImageUpload')
            ->setMaxSize($size)
            ->setAllowedTypes($type)
            ->setForm($form);

        $form->setIsDescriptionsEnabled(true);
        $form->setDescription(
            /*@translate*/ 'Choose a Logo. This logo will be shown in the job opening and the application form.'
        );
    }
} 
