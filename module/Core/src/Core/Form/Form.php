<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 Cross Solution (http://cross-solution.de)
 * @license   AGPLv3
 */

namespace Core\Form;

use Zend\Form\Form as ZendForm;
use Zend\Form\FieldsetInterface;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;
use Zend\InputFilter\InputFilterProviderInterface;
use Zend\InputFilter\InputProviderInterface;

class Form extends ZendForm
{
/**
     * Attach defaults provided by the elements to the input filter
     *
     * @param  InputFilterInterface $inputFilter
     * @param  FieldsetInterface $fieldset Fieldset to traverse when looking for default inputs
     * @return void
     */
    public function attachInputFilterDefaults(InputFilterInterface $inputFilter, FieldsetInterface $fieldset)
    {
        $formFactory  = $this->getFormFactory();
        $inputFactory = $formFactory->getInputFilterFactory();

        if ($this === $fieldset && $fieldset instanceof InputFilterProviderInterface) {
            foreach ($fieldset->getInputFilterSpecification() as $name => $spec) {
                $input = $inputFactory->createInput($spec);
                $inputFilter->add($input, $name);
            }
        }

        foreach ($fieldset->getElements() as $element) {
            $name = $element->getName();

            if ($this->preferFormInputFilter && $inputFilter->has($name)) {
                continue;
            }

            if (!$element instanceof InputProviderInterface) {
                if ($inputFilter->has($name)) {
                    continue;
                }
                // Create a new empty default input for this element
                $spec = array('name' => $name, 'required' => false);
            } else {
                // Create an input based on the specification returned from the element
                $spec  = $element->getInputSpecification();
            }

            $input = $inputFactory->createInput($spec);
            $inputFilter->add($input, $name);
            
        }

        foreach ($fieldset->getFieldsets() as $fieldset) {
            $name = $fieldset->getName();

            if (!$fieldset instanceof InputFilterProviderInterface) {
                if (!$inputFilter->has($name)) {
                    // Add a new empty input filter if it does not exist (or the fieldset's object input filter),
                    // so that elements of nested fieldsets can be recursively added
                    if ($fieldset->getObject() instanceof InputFilterAwareInterface) {
                        $inputFilter->add($fieldset->getObject()->getInputFilter(), $name);
                    } else {
                        $inputFilter->add(new InputFilter(), $name);
                    }
                }

                $fieldsetFilter = $inputFilter->get($name);

                if (!$fieldsetFilter instanceof InputFilterInterface) {
                    // Input attached for fieldset, not input filter; nothing more to do.
                    continue;
                }

                // Traverse the elements of the fieldset, and attach any
                // defaults to the fieldset's input filter
                $this->attachInputFilterDefaults($fieldsetFilter, $fieldset);
                continue;
            }

            if ($inputFilter->has($name)) {
                // if we already have an input/filter by this name, use it
                continue;
            }

            // Create an input filter based on the specification returned from the fieldset
            $spec   = $fieldset->getInputFilterSpecification();
            $filter = $inputFactory->createInputFilter($spec);
            $inputFilter->add($filter, $name);

            // Recursively attach sub filters
            $this->attachInputFilterDefaults($filter, $fieldset);
        }
    }
    
}