<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013-2014 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

namespace Organizations\Form;

use Core\Form\FileUploadFactory;
use Core\Form\Form;

class LogoImageFactory extends FileUploadFactory
{
    protected $fileName = 'image';
    protected $fileEntityClass = '\Organizations\Entity\OrganizationImage';
    protected $configKey = 'organization_logo_image';

    protected function configureForm(Form $form)
    {
        $size = isset($this->config['max_size']) ? $this->config['max_size'] : 100000;
        $type = isset($this->config['mimetype']) ? $this->config['mimetype'] : 'image';

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
