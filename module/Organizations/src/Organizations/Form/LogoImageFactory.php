<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013-2014 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

namespace Organizations\Form;

use Auth\Form\UserImageFactory;

class LogoImageFactory extends UserImageFactory
{
    protected $fileEntityClass = '\Organizations\Entity\OrganizationImage';
    protected $configKey = 'organization_logo_image';
} 
