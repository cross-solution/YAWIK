<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 * @author Miroslav Fedeleš <miroslav.fedeles@gmail.com>
 * @since 0.28
 */
namespace CoreTest\Entity;

use PHPUnit\Framework\TestCase;

use Core\Entity\ModificationDateAwareEntityTrait;
use DateTime;
use Doctrine\ODM\MongoDB\Event\LifecycleEventArgs;

/**
 * @coversDefaultClass \Core\Entity\ModificationDateAwareEntityTrait
 */
class ModificationDateAwareEntityTraitTest extends TestCase
{
    
    /**
     * @var \Core\Entity\ModificationDateAwareEntityInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $modificationDateAwareEntityTrait;
    
    /**
     * @see \PHPUnit\Framework\TestCase::setUp()
     */
    protected function setUp(): void
    {
        $this->modificationDateAwareEntityTrait = $this->getObjectForTrait(ModificationDateAwareEntityTrait::class);
    }
    
    /**
     * @covers ::getDateCreated()
     */
    public function testGetDateCreatedReturnNull()
    {
        $this->assertNull($this->modificationDateAwareEntityTrait->getDateCreated());
    }
    
    /**
     * @param mixed $parameter
     * @covers ::setDateCreated()
     * @covers ::getDateCreated()
     * @dataProvider dataProviderUseNow
     */
    public function testSetDateCreatedUseNow($parameter)
    {
        $now = new DateTime();
        $this->assertSame($this->modificationDateAwareEntityTrait, $this->modificationDateAwareEntityTrait->setDateCreated($parameter));
        $date = $this->modificationDateAwareEntityTrait->getDateCreated();
        $this->assertInstanceOf(DateTime::class, $date);
        $this->assertTrue($date->getTimestamp() >= $now->getTimestamp());
    }
    
    /**
     * @covers ::setDateCreated()
     * @covers ::getDateCreated()
     */
    public function testSetDateCreatedWithDateTime()
    {
        $date = new DateTime();
        $this->assertSame($this->modificationDateAwareEntityTrait, $this->modificationDateAwareEntityTrait->setDateCreated($date));
        $this->assertSame($date, $this->modificationDateAwareEntityTrait->getDateCreated());
    }
    
    /**
     * @covers ::setDateCreated()
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage $dateCreated has to be
     */
    public function testSetDateCreatedInvalidParameter()
    {
        $this->modificationDateAwareEntityTrait->setDateCreated('invalid parameter');
    }
    
    /**
     * @covers ::getDateModified()
     */
    public function testGetDateModifiedReturnNull()
    {
        $this->assertNull($this->modificationDateAwareEntityTrait->getDateModified());
    }
    
    /**
     * @param mixed $parameter
     * @covers ::setDateModified()
     * @covers ::getDateModified()
     * @dataProvider dataProviderUseNow
     */
    public function testSetDateModifiedUseNow($parameter)
    {
        $now = new DateTime();
        $this->assertSame($this->modificationDateAwareEntityTrait, $this->modificationDateAwareEntityTrait->setDateModified($parameter));
        $date = $this->modificationDateAwareEntityTrait->getDateModified();
        $this->assertInstanceOf(DateTime::class, $date);
        $this->assertTrue($date->getTimestamp() >= $now->getTimestamp());
    }
    
    /**
     * @covers ::setDateModified()
     * @covers ::getDateModified()
     */
    public function testSetDateModifiedWithDateTime()
    {
        $date = new DateTime();
        $this->assertSame($this->modificationDateAwareEntityTrait, $this->modificationDateAwareEntityTrait->setDateModified($date));
        $this->assertSame($date, $this->modificationDateAwareEntityTrait->getDateModified());
    }
    
    /**
     * @covers ::setDateModified()
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage $dateModified has to be
     */
    public function testSetDateModifiedInvalidParameter()
    {
        $this->modificationDateAwareEntityTrait->setDateModified(123);
    }
    
    /**
     * @covers ::setDateModified()
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Invalid date string
     */
    public function testSetDateModifiedInvalidStringParameter()
    {
        $this->modificationDateAwareEntityTrait->setDateModified('invalid string parameter');
    }
    
    /**
     * @covers ::setDateModified()
     * @covers ::getDateModified()
     */
    public function testSetDateModifiedValidStringParameter()
    {
        $dateString = '2016-11-14 17:37:35';
        $this->assertSame($this->modificationDateAwareEntityTrait, $this->modificationDateAwareEntityTrait->setDateModified($dateString));
        $this->assertEquals($dateString, $this->modificationDateAwareEntityTrait->getDateModified()->format('Y-m-d H:i:s'));
    }
    
    /**
     * @return array
     */
    public function dataProviderUseNow()
    {
        $event = $this->getMockBuilder(LifecycleEventArgs::class)
            ->disableOriginalConstructor()
            ->getMock();
        
        return [
            'null' => [null],
            'event' => [$event]
        ];
    }
}
