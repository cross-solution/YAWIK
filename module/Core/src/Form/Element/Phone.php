<?php

namespace Core\Form\Element;

use Zend\Form\Element;
use Zend\InputFilter\InputProviderInterface;
use Zend\Validator\Regex as RegexValidator;

class Phone extends Element implements InputProviderInterface
{
    /**
     * @var \Zend\Validator\ValidatorInterface
     */
    protected $validator;

    /**
    * Get a validator if none has been set.
    * https://github.com/posabsolute/jQuery-Validation-Engine/issues/265
    * @return RegexValidator
    */
    public function getValidator()
    {
        if (null === $this->validator) {
            $validator = new RegexValidator('/^([\+][0-9]{1,3}[ \.\-])?([\(]{1}[0-9]{1,6}[\)])?([0-9 \.\-\/]{3,20})((x|ext|extension)[ ]?[0-9]{1,4})?$/');
            $validator->setMessage(
                /*@translate */ 'Please enter a phone Number. You can use the intenational format. Only digits and \'+\'.',
                RegexValidator::NOT_MATCH
            );

            $this->validator = $validator;
        }

        return $this->validator;
    }

    /**
     * Sets the validator to use for this element
     *
     * @param  RegexValidator $validator
     * @return Phone
     */
    public function setValidator(RegexValidator $validator)
    {
        $this->validator = $validator;
        return $this;
    }

    /**
     * Provide default input rules for this element
     *
     * Attaches a phone number validator.
     *
     * @return array
     */
    public function getInputSpecification()
    {
        return array(
            'name' => $this->getName(),
            'required' => true,
            'filters' => array(
                array('name' => 'Zend\Filter\StringTrim'),
            ),
            'validators' => array(
                $this->getValidator(),
            ),
        );
    }
}
