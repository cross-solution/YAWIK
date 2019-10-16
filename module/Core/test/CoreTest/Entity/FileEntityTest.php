<?php
/**
 * YAWIK
 *
 * @filesource
 * @license    MIT
 * @copyright  2013 - 2016 Cross Solution <http://cross-solution.de>
 */

/** */
namespace CoreTest\Entity;

use PHPUnit\Framework\TestCase;

use Auth\Entity\User;
use Core\Entity\FileEntity;
use Core\Entity\Permissions;

/**
 * Test the File Entity
 *
 * @author Carsten Bleek <gelhausen@cross-solution.de>
 * @author Miroslav Fedeleš <miroslav.fedeles@gmail.com>
 * @group  Core
 * @group  Core.Entity
 * @covers \Core\Entity\FileEntity
 */
class FileEntityTest extends TestCase
{

    /**
     * @var $target FileEntity
     */
    protected $target;

    protected function setUp(): void
    {
        $this->target = new FileEntity();
    }

    public function testEntityImplementsInterface()
    {
        $this->assertInstanceOf('\Core\Entity\FileEntity', $this->target);
        $this->assertInstanceOf('\Core\Entity\FileInterface', $this->target);
        $this->assertInstanceOf('\Core\Entity\AbstractIdentifiableEntity', $this->target);
    }

    public function testGetResourceId()
    {
        $this->assertSame($this->target->getResourceId(), 'Entity/File');
    }

    public function testSetGetName()
    {
        $name="test.jpg";
        $this->target->setName($name);
        $this->assertSame($this->target->getName(), $name);
    }

    public function testSetGetUser()
    {
        $user = new User();
        $this->target->setUser($user);
        $this->assertSame($this->target->getUser(), $user);
    }

    /**
     * @covers \Core\Entity\FileEntity::getPrettySize
     * @dataProvider provideSize
     */
    public function testGetPrettySize($size, $unit)
    {
        $target = $this->getMockBuilder(FileEntity::class)
            ->setMethods(['getLength'])
            ->getMock();
        $target->expects($this->once())
            ->method('getLength')
            ->willReturn($size);
        
        $this->assertStringEndsWith($unit, $target->getPrettySize($size));
    }

    public function provideSize()
    {
        return [
            [10, '10'],
            [10000, 'kB'],
            [10000000, 'MB'],
            [10000000000, 'GB']
        ];
    }

    public function testSetGetMimeType()
    {
        $mime = "image/gif";
        $this->target->setType($mime);
        $this->assertSame($this->target->getType(), $mime);
    }

    public function testGetDateUploadedWithoutSetting()
    {
        $this->assertEquals(
            $this->target->getDateUploaded()->format('Y-m-d H:i:s'),
            (new \DateTime())->format('Y-m-d H:i:s')
        );
    }

    public function testGetDateUploaded()
    {
        $input= new \DateTime("2016-01-02");
        $this->target->setDateUploaded($input);
        $this->assertEquals($this->target->getDateUploaded(), $input);
    }

    public function testSetGetFile()
    {
        $file = "test";
        $this->target->setFile($file);
        $this->assertSame($this->target->getFile(), $file);
    }

    /**
     * @dataProvider providePermissions
     * @param $permissions
     */
    public function testGetPermissions($input)
    {
        $permissions = new Permissions($input);
        $this->target->setPermissions($permissions);
        $this->assertEquals($this->target->getPermissions(), $permissions);
    }

    public function providePermissions()
    {
        return [
            [Permissions::PERMISSION_ALL],
            [Permissions::PERMISSION_CHANGE],
            [Permissions::PERMISSION_NONE],
            [Permissions::PERMISSION_VIEW]
        ];
    }

    /**
     * @covers \Core\Entity\FileEntity::getUri
     */
    public function testGetUri()
    {
        $this->assertNull($this->target->getUri());
    }
}
