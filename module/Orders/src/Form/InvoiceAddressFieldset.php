<?php
/**
 * YAWIK
 *
 * @filesource
 * @license    MIT
 * @copyright  2013 - 2016 Cross Solution <http://cross-solution.de>
 */

/** */
namespace Orders\Form;

use Core\Entity\Hydrator\EntityHydrator;
use Zend\Form\Fieldset;

/**
 * ${CARET}
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @todo   write test
 */
class InvoiceAddressFieldset extends Fieldset
{

    public function init()
    {
        $this->setName('invoiceAddress');

        $this->add([
                       'type'       => 'text',
                       'name'       => 'company',
                       'options'    => [
                           'label'       => /*@translate*/ 'Company',
                           'description' => /*@translate*/ 'Please enter the name of the company, which should appear on the invoice address',
                       ],
                       'attributes' => [
                           'required' => true, // marks the label as required.
                       ]
                   ]
        );

        $this->add([
                       'type'    => 'text',
                       'name'    => 'street',
                       'options' => [
                           'label' => /*@translate*/ 'Street',
                       ],
                   ]
        );

        $this->add([
                       'type'    => 'text',
                       'name'    => 'zipCode',
                       'options' => [
                           'label' => /* @translate */ 'Postalcode'
                       ],
                   ]
        );

        $this->add([
                       'type'    => 'text',
                       'name'    => 'city',
                       'options' => [
                           'label' => /*@translate*/ 'City',
                       ],
                   ]
        );

        $this->add([
                       'type'    => 'text',
                       'name'    => 'region',
                       'options' => [
                           'label' => /*@translate*/ 'Region',
                       ],
                   ]
        );

        $this->add([
                       'type'    => 'text',
                       'name'    => 'country',
                       'options' => [
                           'label' => /*@translate*/ 'Country',
                       ],
                   ]
        );

        $this->add([
                       'type'    => 'text',
                       'name'    => 'vatIdNumber',
                       'options' => [
                           'label' => /*@translate*/ 'Value added tax ID',
                       ],
                   ]
        );
        $this->add(
            array(
                'name'       => 'gender',
                'type'       => 'Zend\Form\Element\Select',
                'options'    => [
                    'label'         => /*@translate */ 'Salutation',
                    'value_options' => [
                        ''       => '',
                        'male'   => /*@translate */ 'Mr.',
                        'female' => /*@translate */ 'Mrs.',
                    ]
                ],
                'attributes' => [
                    'data-placeholder' => /*@translate*/ 'please select',
                    'data-allowclear' => 'false',
                    'data-searchbox' => -1,  // hide the search box
                    'required' => false, // mark label as required
                ],
            )
        );

        $this->add([
                       'type'    => 'text',
                       'name'    => 'name',
                       'options' => [
                           'label'       => /*@translate*/ 'Contact Person',
                           'description' => /*@translate*/ 'Please enter the name of a contact person',
                       ],
                   ]
        );

        $this->add([
                       'type'       => 'text',
                       'name'       => 'email',
                       'options'    => [
                           'label' => /*@translate*/ 'Email Address',
                       ],
                       'attributes' => [
                           'required' => true, // marks the label as required.
                       ]
                   ]
        );
    }

    public function getHydrator()
    {
        if (!$this->hydrator) {
            $this->setHydrator(new EntityHydrator());
        }

        return parent::getHydrator();
    }
}