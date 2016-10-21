<?php
/**
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license MIT
 * @author Miroslav Fedeleš <miroslav.fedeles@gmail.com>
 * @since 0.28
 */

namespace OrganizationsTest\Options;

use Organizations\Options\ImageFileCacheOptions;

/**
 * @coversDefaultClass \Organizations\Options\ImageFileCacheOptions
 */
class ODMListenerTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var ImageFileCacheOptions
     */
    protected $options;
    
    /**
     * @see \PHPUnit_Framework_TestCase::setUp()
     */
    protected function setUp()
    {
        $this->options = new ImageFileCacheOptions();
    }
    
    /**
     * @covers ::__construct
     * @covers ::getEnabled
     * @covers ::setEnabled
     */
    public function testEnabled()
    {
        $this->assertTrue($this->options->getEnabled());
        
        $value = false;
        $this->assertSame($this->options, $this->options->setEnabled($value));
        $this->assertEquals($value, $this->options->getEnabled());
    }
    
    /**
     * @covers ::getFilePath
     * @covers ::setFilePath
     */
    public function testFilePath()
    {
        $defaultFilePath = $this->options->getFilePath();
        $this->assertNotEmpty($defaultFilePath);
        $this->assertInternalType('string', $defaultFilePath);
        
        $value = '/somePath';
        $this->assertSame($this->options, $this->options->setFilePath($value));
        $this->assertEquals($value, $this->options->getFilePath());
    }
    
    /**
     * @covers ::getUriPath
     * @covers ::setUriPath
     */
    public function testUriPath()
    {
        $defaultUriPath = $this->options->getUriPath();
        $this->assertNotEmpty($defaultUriPath);
        $this->assertInternalType('string', $defaultUriPath);
        
        $value = '/someUri';
        $this->assertSame($this->options, $this->options->setUriPath($value));
        $this->assertEquals($value, $this->options->getUriPath());
    }
}
