<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

namespace Cv\Factory\Form;

/**
 * @author fedys
 */
class CvContactImageFactory extends \Auth\Form\UserImageFactory
{
    
    /**
     * @var string
     */
    protected $fileEntityClass = 'Cv\Entity\ContactImage';
}
