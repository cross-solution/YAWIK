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

use Auth\Form\ForgotPasswordInputFilter;
use Zend\Filter\FilterChain;
use Zend\Validator\ValidatorChain;

class ForgotPasswordInputFilterTest extends TestCase
{
    /**
     * @var ForgotPasswordInputFilter
     */
    private $testedObject;

    protected function setUp(): void
    {
        $this->testedObject = new ForgotPasswordInputFilter();
    }

    public function testConstructor()
    {
        $this->assertInstanceOf('Zend\InputFilter\InputFilter', $this->testedObject);
    }

    public function testParametersOfIdentityElement()
    {
        $input = $this->testedObject->get('identity');
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
    public function testIdentityElementByValue($value, $expectedResult)
    {
        $input = $this->testedObject->get('identity');
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
}
