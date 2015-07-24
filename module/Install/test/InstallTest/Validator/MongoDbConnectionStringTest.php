<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2015 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace InstallTest\Validator;

use Install\Validator\MongoDbConnectionString;

/**
 * Tests for \Install\Validator\MongoDbConnectionString
 * 
 * @covers \Install\Validator\MongoDbConnectionString
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @group Install
 * @group Install.Validator
 */
class MongoDbConnectionStringTest extends \PHPUnit_Framework_TestCase
{

    public function testExtendsAbstractValidator()
    {
        $this->assertInstanceOf('\Zend\Validator\AbstractValidator', new MongoDbConnectionString());
    }

    public function testProvidesDefaultProperties()
    {
        $target = new MongoDbConnectionString();

        $this->assertAttributeEquals(array('translatorTextDomain' => 'Install'), 'options', $target);
        $this->assertAttributeEquals(
            array(
                $target::INVALID => 'Invalid connection string',
                $target::NO_CONNECTION => 'Connecting the database failed: %databaseError%',
            ),
            'messageTemplates', $target
        );

        $this->assertAttributeEquals(array('databaseError' => 'databaseError'), 'messageVariables', $target);
    }

    public function testValidationFailsIfProvidedInvalidConnectionString()
    {
        $target = new MongoDbConnectionString();

        $this->assertFalse($target->isValid('noconnstr'));

    }


}