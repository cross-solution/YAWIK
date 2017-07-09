<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2016 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace Core\Form\Tree;

use Core\Form\Element\ViewHelperProviderInterface;
use Core\Form\Hydrator\TreeHydrator;
use Core\Form\ViewPartialProviderInterface;
use Core\Form\ViewPartialProviderTrait;
use Zend\Form\Exception;
use Zend\Form\Fieldset;
use Zend\Form\FieldsetInterface;
use Zend\Hydrator;
use Zend\Hydrator\HydratorAwareInterface;
use Zend\Hydrator\HydratorInterface;

/**
 * Fieldset for managing tree items.
 * 
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @since 0.29
 */
class ManagementFieldset extends Fieldset implements ViewPartialProviderInterface
{

    use ViewPartialProviderTrait;

    /**
     * Default view partial name.
     *
     * @var string
     */
    private $defaultPartial = 'core/form/tree-manage';

    public function getHydrator()
    {
        if (!$this->hydrator) {
            $this->setHydrator(new TreeHydrator());
        }

        return $this->hydrator;
    }

    public function init()
    {
    	$this->setName('items');
        $this->add([
                'type' => 'Collection',
                'name' => 'items',
                'options' => [
                    'count' => 0,
                    'should_create_template' => true,
                    'allow_add' => true,
                    'target_element' => [
                        'type' => 'Core/Tree/AddItemFieldset',
                    ],
                ],
            ]);
    }
}