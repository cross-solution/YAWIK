<?php
/**
 * YAWIK
 * 
 * @filesource
 * @copyright (c) 2013-2015 Cross Solution (http://cross-solution.de)
 * @license   MIT
 * @author    weitz@cross-solution.de
 */

namespace Jobs\Form;

use Core\Entity\DraftableEntityInterface;
use Zend\Form\Fieldset;
use Core\Entity\Hydrator\EntityHydrator;

/**
 * Defines the formular files of the 3rd formular for entering a job opening
 *
 * @package Jobs\Form
 */
class PreviewFieldset extends Fieldset
{
    public function getHydrator()
    {
        if (!$this->hydrator) {
            $hydrator = new EntityHydrator();
            $this->setHydrator($hydrator);
        }
        return $this->hydrator;
    }

    public function init()
    {
        $this->setAttribute('id', 'jobpreview-fieldset');

        $this->add(
             array(
            'type' => 'infocheckbox',
            'name' => 'termsAccepted',
            'options' => array(
                'headline' => /*@translate*/ 'Terms and conditions',
                'long_label' => /*@translate*/ 'I have read the %s and accept it',
                'linktext' => /*@translate*/ 'terms an conditions',
                'route' => 'lang/content',
                'params' => array(
                    'view' => 'jobs-terms-and-conditions'
                )
            ),
            'attributes' => array(
                'data-trigger' => 'submit',
            ),
        ));

    }

    public function setObject($object)
    {
        if ($object instanceOf DraftableEntityInterface && !$object->isDraft())
        foreach ($this as $element) {
            $element->setAttribute('disabled', 'disabled');
        }
        return parent::setObject($object);
    }
}