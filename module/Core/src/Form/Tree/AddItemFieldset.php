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

use Core\Form\ViewPartialProviderInterface;
use Core\Form\ViewPartialProviderTrait;
use Zend\Form\Fieldset;
use Zend\InputFilter\InputFilterProviderInterface;

/**
 * Fieldset for adding a tree item.
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @since 0.29
 */
class AddItemFieldset extends Fieldset implements ViewPartialProviderInterface, InputFilterProviderInterface
{
    use ViewPartialProviderTrait;

    /**
     * Default view partial.
     *
     * @var string
     */
    private $defaultPartial = 'core/form/tree-add-item';

    public function init()
    {
        $this->setObject(new \ArrayObject);
        $this->add([
                'name' => 'id',
                'type' => 'Hidden',
            ]);

        $this->add([
                'name' => 'current',
                'type' => 'Hidden',
            ]);
        $this->add([
                'name' => 'do',
                'type' => 'Hidden',
            ]);
        $this->add([
                'name' => 'name',
                'type' => 'Text',
                'options' => [
                    'label' => /*@translate*/ 'Name',
                ],

                'attributes' => ['required' => 'required'],
            ]);

        $this->add([
                'name' => 'value',
                'type' => 'Text',
                'options' => [
                    'label' => /*@translate*/ 'Value',
                ],
            ]);

        $this->add([
                'name' => 'priority',
                'type' => 'Text',
            ]);
    }

    public function getInputFilterSpecification()
    {
        return [
            'name' => [
                'required' => true,
                'filters' => [
                    [ 'name' => 'StringTrim' ],
                ],
            ],
            'value' => [
                'required' => false,
                'filters' => [
                    [ 'name' => 'StringTrim' ],
                ],
            ],
        ];
    }
}
