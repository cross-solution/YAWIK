<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013-2015 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

/** Admin forms */
namespace Admin\Form;

use Core\Form\Container;

/**
 * Formular container for global settings formulars
 *
 * @author Carsten Bleek bleek@cross-solution.de>
 */

class Config extends Container {

    public function init()
    {
        $this->setName('global-settings');

        $this->setForms(array(
            'nameForm' => array(
                'type' => 'Admin\Form\ConfigForm',
                'property' => true,
                'options' => array(
                    'enable_descriptions' => true,
                    'description' => /*@translate*/ 'Please enter the name of the hiring organization.',
                ),
            )
        ));

    }
}