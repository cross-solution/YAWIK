<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2016 Cross Solution <http://cross-solution.de>
 */
namespace CoreTest\Form;

use Core\Form\EmptySummaryAwareTrait;

/**
 *
 * @author fedys
 * @since 0.26
 */
class EmptySummaryAwareTraitTest extends \PHPUnit_Framework_TestCase
{

    public function testSetEmptySummaryNotice()
    {
        $expected = 'notice';
        $summaryAware = $this->getMockBuilder(EmptySummaryAwareTrait::class)
            ->setMethods(['getDefaultEmptySummaryNotice'])
            ->getMockForTrait();
        $summaryAware->expects($this->never())
            ->method('getDefaultEmptySummaryNotice');
        
        $this->assertSame($summaryAware, $summaryAware->setEmptySummaryNotice($expected));
        $this->assertSame($expected, $summaryAware->getEmptySummaryNotice());
    }

    public function testGetEmptySummaryNotice()
    {
        $expected = '';
        $summaryAware = $this->getMockBuilder(EmptySummaryAwareTrait::class)
            ->setMethods(['getDefaultEmptySummaryNotice'])
            ->getMockForTrait();
        $summaryAware->expects($this->once())
            ->method('getDefaultEmptySummaryNotice')
            ->willReturn($expected);
        
        $this->assertSame($expected, $summaryAware->getEmptySummaryNotice());
        $this->assertSame($expected, $summaryAware->getEmptySummaryNotice());
    }

    public function testIsSummaryEmpty()
    {
        $summaryAware = new EmptySummaryAwareTraitMock();
        $summaryAware->add([
            'name' => 'text',
            'type' => 'text'
        ]);
        
        $this->assertTrue($summaryAware->isSummaryEmpty());
        $summaryAware->populateValues(['text' => 'value']);
        $this->assertFalse($summaryAware->isSummaryEmpty());
    }
}

class EmptySummaryAwareTraitMock extends \Zend\Form\Fieldset
{
    
    use EmptySummaryAwareTrait;
}
