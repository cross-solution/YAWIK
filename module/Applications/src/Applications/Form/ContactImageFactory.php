<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013-2014 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

/**  */ 
namespace Applications\Form;


use Auth\Form\UserImageFactory;

/**
 * Service factory for the contact image formular element in the application form.
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 */
class ContactImageFactory extends UserImageFactory
{
    protected $fileEntityClass = '\Applications\Entity\Attachment';
} 
