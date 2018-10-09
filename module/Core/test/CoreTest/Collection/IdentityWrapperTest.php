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
    
    /**
     * @var array
     */
    protected $entries;
    
    public function setUp()
    {
        $getEntryMock = function ($id)
        {
            $entry = $this->getMockBuilder(\stdClass::class)
                ->setMethods(['getId'])
                ->getMock();
            $entry->expects($this->any())
                ->method('getId')
                ->willReturn($id);
            
            $this->entries[$id] = $entry;
            
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
        $expected = count($this->entries);
        $this->assertSame($expected, $this->identityWrapper->count());
        $this->assertSame($expected, $this->wrappedCollection->count());
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
        $this->assertTrue($this->identityWrapper->contains($this->entries['first']));
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
        $keys = array_keys($this->entries);
        $entry = $this->entries[$keys[0]];
        $count = count($this->wrappedCollection);
        $this->identityWrapper->remove($keys[0]);
        $this->assertSame($count - 1, count($this->identityWrapper));
        $this->assertSame($count - 1, count($this->wrappedCollection));
        $this->assertFalse($this->identityWrapper->contains($entry));
        $this->assertFalse($this->wrappedCollection->contains($entry));
    }

    /**
     * Tests IdentityWrapper->removeElement()
     */
    public function testRemoveElement()
    {
        $entry = reset($this->entries);
        $count = count($this->wrappedCollection);
        $this->identityWrapper->removeElement($entry);
        $this->assertSame($count - 1, count($this->identityWrapper));
        $this->assertSame($count - 1, count($this->wrappedCollection));
        $this->assertFalse($this->identityWrapper->contains($entry));
        $this->assertFalse($this->wrappedCollection->contains($entry));
    }

    /**
     * Tests IdentityWrapper->containsKey()
     */
    public function testContainsKey()
    {
        foreach (array_keys($this->entries) as $key) {
            $this->assertTrue($this->identityWrapper->containsKey($key));
            $this->assertFalse($this->wrappedCollection->containsKey($key));
        }
        $this->assertFalse($this->identityWrapper->contains('non-existent'));
    }

    /**
     * Tests IdentityWrapper->get()
     */
    public function testGet()
    {
        foreach ($this->entries as $key => $entry) {
            $this->assertSame($entry, $this->identityWrapper->get($key));
        }
        $this->assertNull($this->identityWrapper->get('non-existent'));
    }

    /**
     * Tests IdentityWrapper->getKeys()
     */
    public function testGetKeys()
    {
        $this->assertSame(array_keys($this->entries), $this->identityWrapper->getKeys());
    }

    /**
     * Tests IdentityWrapper->getValues()
     */
    public function testGetValues()
    {
        $expected = array_values($this->entries);
		$this->assertSame($expected, $this->identityWrapper->getValues());
        $this->assertSame($expected, $this->wrappedCollection->getValues());
    }

    /**
     * Tests IdentityWrapper->set()
     */
    public function testSetAppend()
    {
        $entry = 'non-existent-value';
        $count = count($this->wrappedCollection);
        $this->identityWrapper->set('non-existent', $entry);
        $this->assertSame($count + 1, count($this->identityWrapper));
        $this->assertSame($count + 1, count($this->wrappedCollection));
        $this->assertTrue($this->identityWrapper->contains($entry));
        $this->assertTrue($this->wrappedCollection->contains($entry));
    }

    /**
     * Tests IdentityWrapper->set()
     */
    public function testSetOverwrite()
    {
        $keys = array_keys($this->entries);
        $existentEntry = $this->entries[$keys[0]];
        $key = $keys[0];
        $newEntry = 'new-entry';
        $count = count($this->wrappedCollection);
        $this->identityWrapper->set($key, $newEntry);
        $this->assertSame($count, count($this->identityWrapper));
        $this->assertSame($count, count($this->wrappedCollection));
        $this->assertTrue($this->identityWrapper->contains($newEntry));
        $this->assertTrue($this->wrappedCollection->contains($newEntry));
        $this->assertFalse($this->identityWrapper->contains($existentEntry));
        $this->assertFalse($this->wrappedCollection->contains($existentEntry));
    }

    /**
     * Tests IdentityWrapper->toArray()
     */
    public function testToArray()
    {
        $this->assertSame($this->entries, $this->identityWrapper->toArray());
        $this->assertNotSame($this->entries, $this->wrappedCollection->toArray());
        $this->assertSame(array_values($this->entries), array_values($this->wrappedCollection->toArray()));
    }

    /**
     * Tests IdentityWrapper->first()
     */
    public function testFirst()
    {
        $expected = reset($this->entries);
		$this->assertSame($expected, $this->identityWrapper->first());
		$this->assertSame($expected, $this->wrappedCollection->first());
    }

    /**
     * Tests IdentityWrapper->last()
     */
    public function testLast()
    {
        $expected = end($this->entries);
		$this->assertSame($expected, $this->identityWrapper->last());
		$this->assertSame($expected, $this->wrappedCollection->last());
    }

    /**
     * Tests IdentityWrapper->key()
     */
    public function testKey()
    {
        $this->assertSame(key($this->entries), $this->identityWrapper->key());
        
        $this->identityWrapper->clear();
        $this->assertNull($this->identityWrapper->key());
        $this->assertNull($this->wrappedCollection->key());
    }

    /**
     * Tests IdentityWrapper->current()
     */
    public function testCurrent()
    {
        $this->assertSame(current($this->entries), $this->identityWrapper->current());
        
        $this->identityWrapper->clear();
        $this->assertFalse($this->identityWrapper->current());
        $this->assertFalse($this->wrappedCollection->current());
    }

    /**
     * Tests IdentityWrapper->next()
     */
    public function testNext()
    {
        $this->assertSame(next($this->entries), $this->identityWrapper->next());
        
        $this->identityWrapper->last();
        $this->assertFalse($this->identityWrapper->next());
        $this->assertFalse($this->wrappedCollection->next());
    }

    /**
     * Tests IdentityWrapper->exists()
     */
    public function testExistsWithKey()
    {
        $searchKey = key($this->entries);
        $this->assertTrue($this->identityWrapper->exists(function ($key) use ($searchKey) {
            return $key === $searchKey;
        }));
        $searchKey = 'non-existent';
        $this->assertFalse($this->identityWrapper->exists(function ($key) use ($searchKey) {
            return $key === $searchKey;
        }));
    }
    
    /**
     * Tests IdentityWrapper->exists()
     */
    public function testExistsWithValue()
    {
        $searchValue = current($this->entries);
        $this->assertTrue($this->identityWrapper->exists(function ($key, $value) use ($searchValue) {
            return $value === $searchValue;
        }));
        $searchValue = 'non-existent';
        $this->assertFalse($this->identityWrapper->exists(function ($key, $value) use ($searchValue) {
            return $value === $searchValue;
        }));
    }

    /**
     * Tests IdentityWrapper->filter()
     */
    public function testFilter()
    {
        $searchValue = current($this->entries);
        $filter = function ($value) use ($searchValue) {
            return $value === $searchValue;
        };
        $filtered = $this->identityWrapper->filter($filter);
		$this->assertInstanceOf(IdentityWrapper::class, $filtered);
		$this->assertNotSame($this->identityWrapper, $filtered);
		$this->assertSame(array_filter($this->entries, $filter), $filtered->toArray());
    }

    /**
     * Tests IdentityWrapper->forAll()
     */
    public function testForAllWithKey()
    {
        $keys = array_keys($this->entries);
        $this->assertTrue($this->identityWrapper->forAll(function ($key) use ($keys) {
            return in_array($key, $keys);
        }));
        $keys = ['non-existent'];
        $this->assertFalse($this->identityWrapper->forAll(function ($key) use ($keys) {
            return in_array($key, $keys);
        }));
    }

    /**
     * Tests IdentityWrapper->forAll()
     */
    public function testForAllWithValue()
    {
        $values = array_values($this->entries);
        $this->assertTrue($this->identityWrapper->forAll(function ($key, $value) use ($values) {
            return in_array($value, $values, true);
        }));
        $values = ['non-existent'];
        $this->assertFalse($this->identityWrapper->forAll(function ($key, $value) use ($values) {
            return in_array($value, $values, true);
        }));
    }

    /**
     * Tests IdentityWrapper->map()
     */
    public function testMapValidOperation()
    {
        $map = function ($value) {
            $value->random = rand();
            return $value;
        };
        $mapped = $this->identityWrapper->map($map);
        $this->assertInstanceOf(IdentityWrapper::class, $mapped);
        $this->assertNotSame($this->identityWrapper, $mapped);
        $this->assertSame(array_map($map, $this->entries), $mapped->toArray());
    }
    
    /**
     * @expectedException \LogicException
     * @expectedExceptionMessage $element must have getId() method
     */
    public function testMapInvalidOperation()
    {
        $map = function ($value) {
            return sprintf('id: %s', $value->getId());
        };
        $this->identityWrapper->map($map);
    }

    /**
     * Tests IdentityWrapper->exists()
     */
    public function testPartitionWithKey()
    {
        $searchKey = key($this->entries);
        $partitions = $this->identityWrapper->partition(function ($key) use ($searchKey) {
            return $key === $searchKey;
        });
        $expectedFirstPartiton = array_slice($this->entries, 0, 1, true);
        $expectedSecondPartiton = array_slice($this->entries, 1, null, true);
        $this->assertArrayHasKey(0, $partitions);
        $this->assertArrayHasKey(1, $partitions);
        $this->assertInstanceOf(IdentityWrapper::class, $partitions[0]);
        $this->assertInstanceOf(IdentityWrapper::class, $partitions[1]);
        $this->assertSame($expectedFirstPartiton, $partitions[0]->toArray());
        $this->assertSame($expectedSecondPartiton, $partitions[1]->toArray());
    }
    
    /**
     * Tests IdentityWrapper->exists()
     */
    public function testPartitionWithValue()
    {
        $searchValue = reset($this->entries);
        $partitions = $this->identityWrapper->partition(function ($key, $value) use ($searchValue) {
            return $value === $searchValue;
        });
        $expectedFirstPartiton = array_slice($this->entries, 0, 1, true);
        $expectedSecondPartiton = array_slice($this->entries, 1, null, true);
        $this->assertArrayHasKey(0, $partitions);
        $this->assertArrayHasKey(1, $partitions);
        $this->assertInstanceOf(IdentityWrapper::class, $partitions[0]);
        $this->assertInstanceOf(IdentityWrapper::class, $partitions[1]);
        $this->assertSame($expectedFirstPartiton, $partitions[0]->toArray());
        $this->assertSame($expectedSecondPartiton, $partitions[1]->toArray());
    }

    /**
     * Tests IdentityWrapper->indexOf()
     */
    public function testIndexOf()
    {
        foreach ($this->entries as $key => $entry) {
            $this->assertSame($key, $this->identityWrapper->indexOf($entry));
            $this->assertNotSame($key, $this->wrappedCollection->indexOf($entry));
        }
        $this->assertFalse($this->identityWrapper->indexOf('non-existent'));
    }

    /**
     * Tests IdentityWrapper->slice()
     */
    public function testSlice()
    {
        $offset = 1;
        $length = 1;
        $this->assertSame(array_slice($this->entries, $offset, $length), $this->identityWrapper->slice($offset, $length));
    }

    /**
     * Tests IdentityWrapper->getIterator()
     */
    public function testGetIterator()
    {
        $iterator = $this->identityWrapper->getIterator();
        $this->assertInstanceOf(\ArrayIterator::class, $iterator);
        $this->assertSame($this->entries, $iterator->getArrayCopy());
    }

    /**
     * Tests IdentityWrapper->offsetExists()
     */
    public function testOffsetExists()
    {
        foreach (array_keys($this->entries) as $key) {
            $this->assertTrue($this->identityWrapper->offsetExists($key));
            $this->assertFalse($this->wrappedCollection->offsetExists($key));
        }
        $this->assertFalse($this->identityWrapper->offsetExists('non-existent'));
    }

    /**
     * Tests IdentityWrapper->offsetGet()
     */
    public function testOffsetGet()
    {
        foreach ($this->entries as $key => $entry) {
            $this->assertSame($entry, $this->identityWrapper->offsetGet($key));
            $this->assertNull($this->wrappedCollection->offsetGet($key));
        }
        $this->assertNull($this->identityWrapper->offsetGet('non-existent'));
    }

    /**
     * Tests IdentityWrapper->set()
     */
    public function testOffsetSetAppend()
    {
        $entry = 'non-existent-value';
        $count = count($this->wrappedCollection);
        $this->identityWrapper->offsetSet('non-existent', $entry);
        $this->assertSame($count + 1, count($this->identityWrapper));
        $this->assertSame($count + 1, count($this->wrappedCollection));
        $this->assertTrue($this->identityWrapper->contains($entry));
        $this->assertTrue($this->wrappedCollection->contains($entry));
    }
    
    /**
     * Tests IdentityWrapper->set()
     */
    public function testOffsetSetWithNullMustAppend()
    {
        $entry1 = 'non-existent-value-1';
        $entry2 = 'non-existent-value-2';
        $count = count($this->wrappedCollection);
        $this->identityWrapper->offsetSet(null, $entry1);
        $this->identityWrapper->offsetSet(null, $entry2);
        $this->assertSame($count + 2, count($this->identityWrapper));
        $this->assertSame($count + 2, count($this->wrappedCollection));
        $this->assertTrue($this->identityWrapper->contains($entry1));
        $this->assertTrue($this->wrappedCollection->contains($entry2));
        $this->assertTrue($this->identityWrapper->contains($entry1));
        $this->assertTrue($this->wrappedCollection->contains($entry2));
    }
    
    /**
     * Tests IdentityWrapper->set()
     */
    public function testOffsetSetOverwrite()
    {
        $keys = array_keys($this->entries);
        $key = $keys[0];
        $existentEntry = $this->entries[$key];
        $newEntry = 'new-entry';
        $count = count($this->wrappedCollection);
        $this->identityWrapper->offsetSet($key, $newEntry);
        $this->assertSame($count, count($this->identityWrapper));
        $this->assertSame($count, count($this->wrappedCollection));
        $this->assertTrue($this->identityWrapper->contains($newEntry));
        $this->assertTrue($this->wrappedCollection->contains($newEntry));
        $this->assertFalse($this->identityWrapper->contains($existentEntry));
        $this->assertFalse($this->wrappedCollection->contains($existentEntry));
    }

    /**
     * Tests IdentityWrapper->offsetUnset()
     */
    public function testOffsetUnset()
    {
        $keys = array_keys($this->entries);
        $key = $keys[0];
        $entry = $this->entries[$key];
        $count = count($this->wrappedCollection);
        $this->identityWrapper->offsetUnset($key);
        $this->assertSame($count - 1, count($this->identityWrapper));
        $this->assertSame($count - 1, count($this->wrappedCollection));
        $this->assertFalse($this->identityWrapper->contains($entry));
        $this->assertFalse($this->wrappedCollection->contains($entry));
    }

    /**
     * Tests IdentityWrapper->setIdentityExtractor()
     */
    public function testSetIdentityExtractor()
    {
        $suffix = '.suffix';
        $identityExtractor = function ($entry) use ($suffix) {
            return $entry->getId() . $suffix;
        };
        
        $this->assertSame($this->identityWrapper, $this->identityWrapper->setIdentityExtractor($identityExtractor));
        
        foreach ($this->entries as $key => $entry) {
            $this->assertSame($key.$suffix, $this->identityWrapper->indexOf($entry));
        }
    }
}
