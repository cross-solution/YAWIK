<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license       MIT
 */
namespace CoreTest\Collection;

use Core\Collection\IdentityWrapper;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

class IdentityWrapperTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var IdentityWrapper
     */
    protected $identityWrapper;
    
    /**
     * @var ArrayCollection
     */
    protected $wrappedCollection;
    
    public function setUp()
    {
        $getEntryMock = function ($id)
        {
            $entry = $this->getMock(\stdClass::class, ['getId']);
            $entry->expects($this->any())
                ->method('getId')
                ->willReturn($id);
            
            return $entry;
        };
        
        $this->wrappedCollection = new ArrayCollection([
            $getEntryMock('first'),
            $getEntryMock('second'),
            $getEntryMock('third')
        ]);
        $this->identityWrapper = new IdentityWrapper($this->wrappedCollection);
    }
    
    public function testConstructor()
    {
        $this->assertInstanceOf(Collection::class, $this->identityWrapper);
    }
    
    /**
     * Tests IdentityWrapper->count()
     */
    public function testCount()
    {
        $this->assertSame($this->wrappedCollection->count(), $this->wrappedCollection->count());
    }

    /**
     * Tests IdentityWrapper->add()
     */
    public function testAdd()
    {
        $entry = 'value';
        $count = count($this->wrappedCollection);
		$this->identityWrapper->add($entry);
        $this->assertSame($count + 1, count($this->identityWrapper));
        $this->assertSame($count + 1, count($this->wrappedCollection));
        $this->assertTrue($this->identityWrapper->contains($entry));
        $this->assertTrue($this->wrappedCollection->contains($entry));
    }

    /**
     * Tests IdentityWrapper->clear()
     */
    public function testClear()
    {
        $this->identityWrapper->clear();
        $this->assertSame(0, count($this->identityWrapper));
        $this->assertSame(0, count($this->wrappedCollection));
    }

    /**
     * Tests IdentityWrapper->contains()
     */
    public function testContains()
    {
        $this->assertTrue($this->identityWrapper->contains($this->wrappedCollection->first()));
        $this->assertFalse($this->identityWrapper->contains('non-existent'));
    }

    /**
     * Tests IdentityWrapper->isEmpty()
     */
    public function testIsEmpty()
    {
        $this->assertFalse($this->identityWrapper->isEmpty());
        $this->assertFalse($this->wrappedCollection->isEmpty());
        
        $this->identityWrapper->clear();
        $this->assertTrue($this->identityWrapper->isEmpty());
        $this->assertTrue($this->wrappedCollection->isEmpty());
    }

    /**
     * Tests IdentityWrapper->remove()
     */
    public function testRemove()
    {
        // TODO Auto-generated IdentityWrapperTest->testRemove()
        $this->markTestIncomplete("remove test not implemented");
        
        $this->identityWrapper->remove(/* parameters */);
    }

    /**
     * Tests IdentityWrapper->removeElement()
     */
    public function testRemoveElement()
    {
        // TODO Auto-generated IdentityWrapperTest->testRemoveElement()
        $this->markTestIncomplete("removeElement test not implemented");
        
        $this->identityWrapper->removeElement(/* parameters */);
    }

    /**
     * Tests IdentityWrapper->containsKey()
     */
    public function testContainsKey()
    {
        // TODO Auto-generated IdentityWrapperTest->testContainsKey()
        $this->markTestIncomplete("containsKey test not implemented");
        
        $this->identityWrapper->containsKey(/* parameters */);
    }

    /**
     * Tests IdentityWrapper->get()
     */
    public function testGet()
    {
        // TODO Auto-generated IdentityWrapperTest->testGet()
        $this->markTestIncomplete("get test not implemented");
        
        $this->identityWrapper->get(/* parameters */);
    }

    /**
     * Tests IdentityWrapper->getKeys()
     */
    public function testGetKeys()
    {
        // TODO Auto-generated IdentityWrapperTest->testGetKeys()
        $this->markTestIncomplete("getKeys test not implemented");
        
        $this->identityWrapper->getKeys(/* parameters */);
    }

    /**
     * Tests IdentityWrapper->getValues()
     */
    public function testGetValues()
    {
        // TODO Auto-generated IdentityWrapperTest->testGetValues()
        $this->markTestIncomplete("getValues test not implemented");
        
        $this->identityWrapper->getValues(/* parameters */);
    }

    /**
     * Tests IdentityWrapper->set()
     */
    public function testSet()
    {
        // TODO Auto-generated IdentityWrapperTest->testSet()
        $this->markTestIncomplete("set test not implemented");
        
        $this->identityWrapper->set(/* parameters */);
    }

    /**
     * Tests IdentityWrapper->toArray()
     */
    public function testToArray()
    {
        // TODO Auto-generated IdentityWrapperTest->testToArray()
        $this->markTestIncomplete("toArray test not implemented");
        
        $this->identityWrapper->toArray(/* parameters */);
    }

    /**
     * Tests IdentityWrapper->first()
     */
    public function testFirst()
    {
        // TODO Auto-generated IdentityWrapperTest->testFirst()
        $this->markTestIncomplete("first test not implemented");
        
        $this->identityWrapper->first(/* parameters */);
    }

    /**
     * Tests IdentityWrapper->last()
     */
    public function testLast()
    {
        // TODO Auto-generated IdentityWrapperTest->testLast()
        $this->markTestIncomplete("last test not implemented");
        
        $this->identityWrapper->last(/* parameters */);
    }

    /**
     * Tests IdentityWrapper->key()
     */
    public function testKey()
    {
        // TODO Auto-generated IdentityWrapperTest->testKey()
        $this->markTestIncomplete("key test not implemented");
        
        $this->identityWrapper->key(/* parameters */);
    }

    /**
     * Tests IdentityWrapper->current()
     */
    public function testCurrent()
    {
        // TODO Auto-generated IdentityWrapperTest->testCurrent()
        $this->markTestIncomplete("current test not implemented");
        
        $this->identityWrapper->current(/* parameters */);
    }

    /**
     * Tests IdentityWrapper->next()
     */
    public function testNext()
    {
        // TODO Auto-generated IdentityWrapperTest->testNext()
        $this->markTestIncomplete("next test not implemented");
        
        $this->identityWrapper->next(/* parameters */);
    }

    /**
     * Tests IdentityWrapper->exists()
     */
    public function testExists()
    {
        // TODO Auto-generated IdentityWrapperTest->testExists()
        $this->markTestIncomplete("exists test not implemented");
        
        $this->identityWrapper->exists(/* parameters */);
    }

    /**
     * Tests IdentityWrapper->filter()
     */
    public function testFilter()
    {
        // TODO Auto-generated IdentityWrapperTest->testFilter()
        $this->markTestIncomplete("filter test not implemented");
        
        $this->identityWrapper->filter(/* parameters */);
    }

    /**
     * Tests IdentityWrapper->forAll()
     */
    public function testForAll()
    {
        // TODO Auto-generated IdentityWrapperTest->testForAll()
        $this->markTestIncomplete("forAll test not implemented");
        
        $this->identityWrapper->forAll(/* parameters */);
    }

    /**
     * Tests IdentityWrapper->map()
     */
    public function testMap()
    {
        // TODO Auto-generated IdentityWrapperTest->testMap()
        $this->markTestIncomplete("map test not implemented");
        
        $this->identityWrapper->map(/* parameters */);
    }

    /**
     * Tests IdentityWrapper->partition()
     */
    public function testPartition()
    {
        // TODO Auto-generated IdentityWrapperTest->testPartition()
        $this->markTestIncomplete("partition test not implemented");
        
        $this->identityWrapper->partition(/* parameters */);
    }

    /**
     * Tests IdentityWrapper->indexOf()
     */
    public function testIndexOf()
    {
        // TODO Auto-generated IdentityWrapperTest->testIndexOf()
        $this->markTestIncomplete("indexOf test not implemented");
        
        $this->identityWrapper->indexOf(/* parameters */);
    }

    /**
     * Tests IdentityWrapper->slice()
     */
    public function testSlice()
    {
        // TODO Auto-generated IdentityWrapperTest->testSlice()
        $this->markTestIncomplete("slice test not implemented");
        
        $this->identityWrapper->slice(/* parameters */);
    }

    /**
     * Tests IdentityWrapper->getIterator()
     */
    public function testGetIterator()
    {
        // TODO Auto-generated IdentityWrapperTest->testGetIterator()
        $this->markTestIncomplete("getIterator test not implemented");
        
        $this->identityWrapper->getIterator(/* parameters */);
    }

    /**
     * Tests IdentityWrapper->offsetExists()
     */
    public function testOffsetExists()
    {
        // TODO Auto-generated IdentityWrapperTest->testOffsetExists()
        $this->markTestIncomplete("offsetExists test not implemented");
        
        $this->identityWrapper->offsetExists(/* parameters */);
    }

    /**
     * Tests IdentityWrapper->offsetGet()
     */
    public function testOffsetGet()
    {
        // TODO Auto-generated IdentityWrapperTest->testOffsetGet()
        $this->markTestIncomplete("offsetGet test not implemented");
        
        $this->identityWrapper->offsetGet(/* parameters */);
    }

    /**
     * Tests IdentityWrapper->offsetSet()
     */
    public function testOffsetSet()
    {
        // TODO Auto-generated IdentityWrapperTest->testOffsetSet()
        $this->markTestIncomplete("offsetSet test not implemented");
        
        $this->identityWrapper->offsetSet(/* parameters */);
    }

    /**
     * Tests IdentityWrapper->offsetUnset()
     */
    public function testOffsetUnset()
    {
        // TODO Auto-generated IdentityWrapperTest->testOffsetUnset()
        $this->markTestIncomplete("offsetUnset test not implemented");
        
        $this->identityWrapper->offsetUnset(/* parameters */);
    }

    /**
     * Tests IdentityWrapper->setIdentityExtractor()
     */
    public function testSetIdentityExtractor()
    {
        // TODO Auto-generated IdentityWrapperTest->testSetIdentityExtractor()
        $this->markTestIncomplete("setIdentityExtractor test not implemented");
        
        $this->identityWrapper->setIdentityExtractor(/* parameters */);
    }
}

