<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2017 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace CoreTest\Form;

use PHPUnit\Framework\TestCase;

use Core\Entity\Hydrator\EntityHydrator;
use Core\Form\Hydrator\MetaDataHydrator;
use Core\Form\MetaDataFieldset;
use CoreTestUtils\TestCase\TestInheritanceTrait;
use CoreTestUtils\TestCase\TestSetterGetterTrait;
use Zend\Form\Fieldset;

/**
 * Tests for \Core\Form\MetaDataFieldset
 *
 * @covers \Core\Form\MetaDataFieldset
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @group Core
 * @group Core.Form
 */
class MetaDataFieldsetTest extends TestCase
{
    use TestInheritanceTrait, TestSetterGetterTrait;

    private $target = MetaDataFieldset::class;

    private $inheritance = [ Fieldset::class ];

    private $properties = [
        [ 'hydrator', ['default@' => MetaDataHydrator::class, '@value' => EntityHydrator::class]]
    ];
}
