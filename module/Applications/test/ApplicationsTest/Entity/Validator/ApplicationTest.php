<?php
/**
 * YAWIK
 *
 * @filesource
 * @license    MIT
 * @copyright  2013 - 2016 Cross Solution <http://cross-solution.de>
 */

/** */
namespace ApplicationsTest\Entity\Validator;

use PHPUnit\Framework\TestCase;

use Applications\Entity\Application;
use Applications\Entity\ApplicationInterface;
use Applications\Entity\Contact;

/**
 * Tests for Application validator.
 *
 * @covers \Applications\Entity\Validator\Application
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @group  Applications
 * @group  Applications.Entity
 * @group  Applications.Entity.Validator
 */
class ApplicationTest extends TestCase
{
    /**
     * @var \Applications\Entity\Validator\Application
     */
    private $target;


    protected function setUp(): void
    {
        $this->target = new \Applications\Entity\Validator\Application();
    }

    /**
     * @testdox Extends \Zend\Validator\AbstractValidator
     * @coversNothing
     */
    public function testExtendsAbstractValidator()
    {
        $this->assertInstanceOf('\Zend\Validator\AbstractValidator', $this->target);
    }

    /**
     * @testdox isValid() returns false and sets 'NO_APPLICATION' error if it gets passed something other than an ApplicationInterface
     */
    public function testIsValidReturnsFalseAndSetsNoApplicationErrorIfNotPassedAnApplication()
    {
        $value          = 'notAnApplicationInterface';
        $expectedErrors = array(
            'NO_APPLICATION' => null,
        );

        $result = $this->target->isValid($value);
        $errors = $this->target->getMessages();

        $this->assertFalse($result);
        $this->assertEquals($expectedErrors, $errors);
    }

    public function provideIsValidTestData()
    {
        $app1    = new Application();
        $app2    = new Application();
        $app3    = new Application();
        $app4    = new Application();
        $contact = new Contact();
        $contact->setEmail('some.email@host.tld');
        $app1->setContact(new Contact());
        $app2->setContact($contact);
        $app3->setContact(new Contact());
        $app3->getAttributes()->setAcceptedPrivacyPolicy(true);
        $app4->setContact($contact);
        $app4->getAttributes()->setAcceptedPrivacyPolicy(true);

        return array(
            array($app1, false, 'NO_EMAIL,NO_ACCEPT_PP'),
            array($app2, false, 'NO_ACCEPT_PP'),
            array($app3, false, 'NO_EMAIL'),
            array($app4, true),
        );
    }

    /**
     * @testdox      isValid() returns true only if an email is set and the privacy policy is accepted. In any other case, it sets the appropriate error messages.
     * @dataProvider provideIsValidTestData
     *
     * @param ApplicationInterface $value
     * @param bool                 $expectedResult
     * @param null|string          $expectedErrorKey
     */
    public function testIsValidReturnsOnlyTrueIfEmailAndPrivacyPolicyIsSet(
        $value,
        $expectedResult,
        $expectedErrorKey = null
    ) {
        $expectedErrors = array();
        if (null !== $expectedErrorKey) {
            $expectedErrorKey = explode(',', $expectedErrorKey);

            foreach ($expectedErrorKey as $key) {
                $expectedErrors[$key] = null;
            }
        }

        $assertMethod = 'assert' . ($expectedResult ? 'True' : 'False');


        $this->$assertMethod($this->target->isValid($value));
        $this->assertEquals($expectedErrors, $this->target->getOption('messages'));
    }
}
