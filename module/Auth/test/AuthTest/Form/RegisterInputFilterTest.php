<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license       MIT
 */

namespace AuthTest\Form;

use PHPUnit\Framework\TestCase;

use Auth\Entity\User;
use Auth\Form\RegisterInputFilter;
use Zend\Filter\FilterChain;
use Zend\Validator\ValidatorChain;

class RegisterInputFilterTest extends TestCase
{
    /**
     * @var RegisterInputFilter
     */
    private $testedObject;

    protected function setUp(): void
    {
        $this->testedObject = new RegisterInputFilter();
    }

    public function testConstructor()
    {
        $this->assertInstanceOf('Zend\InputFilter\InputFilter', $this->testedObject);
    }

    public function testParametersOfRegisterFieldsetNameElement()
    {
        $registerFieldset = $this->testedObject->get('register');

        $input = $registerFieldset->get('name');
        $this->assertTrue($input->isRequired());

        /** @var FilterChain $filterChain */
        $filterChain = $input->getFilterChain();
        $filters = $filterChain->getFilters()->toArray();
        $this->assertInstanceOf('Zend\Filter\StripTags', $filters[0]);
        $this->assertInstanceOf('Zend\Filter\StringTrim', $filters[1]);

        /** @var ValidatorChain $validatorChain */
        $validatorChain = $input->getValidatorChain();
        $validators = $validatorChain->getValidators();

        /** @var \Zend\Validator\StringLength $firstValidator */
        $firstValidator = $validators[0]['instance'];
        $this->assertInstanceOf('Zend\Validator\StringLength', $firstValidator);
        $this->assertEquals($firstValidator->getMin(), 3);
        $this->assertEquals($firstValidator->getMax(), 255);
        $this->assertEquals($firstValidator->getOption('encoding'), 'UTF-8');
    }

    /**
     * @dataProvider dataProviderForTestIdentityElementByValue
     *
     * @param array $value
     * @param bool $expectedResult
     */
    public function testRegisterFieldsetNameElementByValue($value, $expectedResult)
    {
        $registerFieldset = $this->testedObject->get('register');

        $input = $registerFieldset->get('name');
        $input->setValue($value);
        $result = $input->isValid();
        $this->assertSame($expectedResult, $result);
    }

    public function dataProviderForTestIdentityElementByValue()
    {
        return array(
            'Empty string should be invalid' => array('', false),
            'String with 2 chars should be invalid' => array('ab', false),
            'String with 3 chars should be valid' => array('abc', true),
            'String with 255 chars should be valid' => array(str_repeat('a', 255), true),
            'String with 256 chars should be invalid' => array(str_repeat('a', 256), false),
        );
    }

    public function testParametersOfRegisterFieldsetEmailElement()
    {
        $registerFieldset = $this->testedObject->get('register');

        $input = $registerFieldset->get('email');
        $this->assertTrue($input->isRequired());

        /** @var FilterChain $filterChain */
        $filterChain = $input->getFilterChain();
        $filters = $filterChain->getFilters()->toArray();
        $this->assertInstanceOf('Zend\Filter\StripTags', $filters[0]);
        $this->assertInstanceOf('Zend\Filter\StringTrim', $filters[1]);

        /** @var ValidatorChain $validatorChain */
        $validatorChain = $input->getValidatorChain();
        $validators = $validatorChain->getValidators();

        /** @var \Zend\Validator\EmailAddress $firstValidator */
        $firstValidator = $validators[0]['instance'];
        $this->assertInstanceOf('Zend\Validator\EmailAddress', $firstValidator);
    }

    public function testParametersOfRegisterFieldsetRoleElement()
    {
        $registerFieldset = $this->testedObject->get('register');

        $input = $registerFieldset->get('role');
        $this->assertTrue($input->isRequired());

        /** @var FilterChain $filterChain */
        $filterChain = $input->getFilterChain();
        $filters = $filterChain->getFilters()->toArray();
        $this->assertInstanceOf('Zend\Filter\StripTags', $filters[0]);
        $this->assertInstanceOf('Zend\Filter\StringTrim', $filters[1]);

        /** @var ValidatorChain $validatorChain */
        $validatorChain = $input->getValidatorChain();
        $validators = $validatorChain->getValidators();

        /** @var \Zend\Validator\InArray $firstValidator */
        $firstValidator = $validators[0]['instance'];
        $this->assertInstanceOf('Zend\Validator\InArray', $firstValidator);
        $this->assertSame(array(User::ROLE_RECRUITER, User::ROLE_USER), $firstValidator->getHaystack());
    }
}
