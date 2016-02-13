<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

namespace Core\Form;

use Zend\Form\Fieldset;

/**
 * Class LocalizationSettingsFieldset
 *
 * @package Core\Form
 */
class LocalizationSettingsFieldset extends Fieldset
{
    /**
     * Initialize the Sele
     */
    public function init()
    {
        $this->setLabel('general settings');
        
        $this->add(
            array(
                'type' => 'Zend\Form\Element\Select',
                'name' => 'language',
                'options' => array(
                        'label' => /* @translate */ 'choose your language',
                        'value_options' => array(
                                'en' => /* @translate */ 'English',
                                'fr' => /* @translate */ 'French',
                                'de' => /* @translate */ 'German',
                                'it' => /* @translate */ 'Italian',
                                'po' => /* @translate */ 'Polish',
                                'ru' => /* @translate */ 'Russian',
                                'tr' => /* @translate */ 'Turkish',
                                'es' => /* @translate */ 'Spanish',
                        ),
                        'description' => /* @translate */ 'defines the languages of this frontend.'
                ),
            )
        );
    }
}
