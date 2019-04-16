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

use PHPUnit\Framework\TestCase;

use Install\Validator\MongoDbConnectionString;

/**
 * Tests for \Install\Validator\MongoDbConnectionString
 *
 * @covers \Install\Validator\MongoDbConnectionString
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @group Install
 * @group Install.Validator
 */
class MongoDbConnectionStringTest extends TestCase
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
            'messageTemplates',
            $target
        );
    }

    public function invalidConnectionStringProvider()
    {
        return [
            [ 'noconnstr' ],
            [ 'mongodb://user name:pass@server' ],
            [ 'mongodb://server/db.with.dot' ],
        ];
    }

    /**
     * @dataProvider invalidConnectionStringProvider
     */
    public function testValidationFailsIfProvidedInvalidConnectionString($connStr)
    {
        $target = new MongoDbConnectionString();

        $this->assertFalse($target->isValid($connStr), 'Failed with: ' . $connStr);
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
            [ 'mongodb://server:23432/dbName?option=value' ],
            [ 'mongodb://name:pass@cluster0-shard-00-00-nvwmc.mongodb.net:27017,cluster0-shard-00-01-nvwmc.mongodb.net:27017,cluster0-shard-00-02-nvwmc.mongodb.net:27017/?ssl=true&replicaSet=Cluster0-shard-0&authSource=admin' ],
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
