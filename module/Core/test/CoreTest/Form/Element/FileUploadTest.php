<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license       MIT
 */

namespace CoreTest\Form\Element;

use PHPUnit\Framework\TestCase;

use Core\Form\Element\FileUpload;
use Core\Form\Form;

/**
* @covers \Core\Form\Element\FileUpload
*/
class FileUploadTest extends TestCase
{
    /**
     * @var FileUpload
     */
    protected $target;

    protected function setUp(): void
    {
        $this->target = new FileUpload();
    }

    public function testConstructor()
    {
        $this->assertInstanceOf('Core\Form\Element\FileUpload', $this->target);
        $this->assertInstanceOf('Zend\Form\Element', $this->target);
        $this->assertAttributeSame('formFileUpload', 'helper', $this->target);
        $this->assertAttributeSame(false, 'isMultiple', $this->target);
    }

    public function testSetMaxSize()
    {
        $input = 100000;
        $this->target->setMaxSize($input);
        $this->assertAttributeSame(
            [
                'type' => 'file',
                'data-maxsize' => $input
            ],
            'attributes',
            $this->target
        );
        $this->assertEquals($this->target->getMaxSize(), $input);
    }

    public function testSetGetAllowedMimetypesWithArray()
    {
        $input = ["image/png", "image/jpg"];
        $expected = 'image/png,image/jpg';
        $this->target->setAllowedTypes($input);
        $this->assertAttributeSame(
            [
                'type' => 'file',
                'data-allowedtypes' => $expected
            ],
            'attributes',
            $this->target
        );
        $this->assertEquals($this->target->getAllowedTypes(), $expected);
    }

    public function testSetGetAllowedMimetypesWithString()
    {
        $input = "image/png";
        $this->target->setAllowedTypes($input);
        $this->assertAttributeSame(
            [
                'type' => 'file',
                'data-allowedtypes' => 'image/png'
            ],
            'attributes',
            $this->target
        );
        $this->assertEquals($this->target->getAllowedTypes(), $input);
    }

    /**
     * @covers \Core\Form\Element\FileUpload::setMaxFileCount
     * @covers \Core\Form\Element\FileUpload::getMaxFileCount
     */
    public function testSetGetMaxFileCount()
    {
        $input = "10";
        $this->target->setMaxFileCount($input);
        $this->assertAttributeSame(
            [
                'type' => 'file',
                'data-maxfilecount' => (int) $input
            ],
            'attributes',
            $this->target
        );
        $this->assertEquals($this->target->getMaxFileCount(), (int) $input);
    }

    /**
     * @covers \Core\Form\Element\FileUpload::setIsMultiple
     * @covers \Core\Form\Element\FileUpload::isMultiple
     */
    public function testSetGetIsMultiple()
    {
        $input = 1;
        $this->target->setIsMultiple($input);
        $this->assertAttributeSame(
            [
                'type' => 'file',
                'multiple' => true
            ],
            'attributes',
            $this->target
        );
        $input = true;
        $this->target->setIsMultiple($input);
        $this->assertAttributeSame(
            [
                'type' => 'file',
                'multiple' => $input
            ],
            'attributes',
            $this->target
        );
        $this->assertEquals($this->target->isMultiple(), true);
        $input = false;
        $this->target->setIsMultiple($input);
        $this->assertAttributeSame(
            [
                'type' => 'file',
            ],
            'attributes',
            $this->target
        );
        $this->assertEquals($this->target->isMultiple(), false);
    }

    public function testPrepareElement()
    {
        $form = new Form();
        $this->target->setIsMultiple(true);
        $this->target->prepareElement($form);

        $this->assertAttributeSame(
            [
                'method' => 'POST',
                'class' => 'multi-file-upload',
                'data-is-empty' => true,
                'enctype' => 'multipart/form-data',
            ],
            'attributes',
            $form
        );
    }

    public function testSetGetViewHelper()
    {
        $viewHelper = "test";
        $this->target->setViewHelper($viewHelper);
        $this->assertEquals($this->target->getViewHelper(), $viewHelper);
    }

    public function xtestInputSpecification()
    {
        /* @todo */
    }
}
