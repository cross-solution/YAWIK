<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright https://yawik.org/COPYRIGHT.php
 */

/** */
namespace CoreTest\Form;

use PHPUnit\Framework\TestCase;

use Core\Entity\Hydrator\EntityHydrator;
use Core\Form\Hydrator\MetaDataHydrator;
use Core\Form\MetaDataFieldset;
use CoreTestUtils\TestCase\TestInheritanceTrait;
use CoreTestUtils\TestCase\TestSetterGetterTrait;
use Laminas\Form\Fieldset;

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
