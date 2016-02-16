<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2016 Cross Solution <http://cross-solution.de>
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
            ),
            'messageTemplates', $target
        );
    }

    public function testValidationFailsIfProvidedInvalidConnectionString()
    {
        $target = new MongoDbConnectionString();

        $this->assertFalse($target->isValid('noconnstr'), 'Passing invalid connection string did not return false');
        $this->assertTrue($target->isValid('mongodb://server:23432/dbName?option=value'), 'Passing valid connection string did not return true');

    }

    public function validConnectionStringsProvider()
    {
        return [
            [ 'mongodb://server' ],
            [ 'mongodb://server/database' ],
            [ 'mongodb://server:1234' ],
            [ 'mongodb://server:1234/database' ],
            [ 'mongodb://user@server' ],
            [ 'mongodb://user@server:1234' ],
            [ 'mongodb://user@server/database' ],
            [ 'mongodb://user@server:1234/database' ],
            [ 'mongodb://user:pass@server' ],
            [ 'mongodb://user:pass@server/database' ],
            [ 'mongodb://user:pass@server:1234' ],
            [ 'mongodb://user:pass@server:1234/database' ],
        ];
    }

    /**
     * @dataProvider validConnectionStringsProvider
     *
     * @param string $connStr Connection string to test
     */
    public function testValidConnectionStrings($connStr)
    {
        $target = new MongoDbConnectionString();

        $this->assertTrue($target->isValid($connStr), 'Failed with: "' . $connStr . '"');
    }


}