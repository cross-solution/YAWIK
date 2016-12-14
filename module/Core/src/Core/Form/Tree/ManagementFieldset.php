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
 * ${CARET}
 * 
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @todo write test 
 */
class ManagementFieldset extends Fieldset implements ViewPartialProviderInterface
{

    use ViewPartialProviderTrait;

    private $defaultPartial = 'core/form/tree-manage';

    /**
     * The view helper name
     *
     * @var string
     */
    private $viewHelper = "formTreeManagement";

    public function setViewHelper($helper)
    {
        $this->viewHelper = $helper;

        return $this;
    }

    public function getViewHelper()
    {
        return $this->viewHelper;
    }


    public function getHydrator()
    {
        if (!$this->hydrator) {
            $this->setHydrator(new TreeHydrator());
        }

        return $this->hydrator;
    }

    public function init()
    {
//        $this->add([
//                'name' => 'data',
//                'type' => 'Core/Tree/AddItemFieldset',
//            ]);
//
//
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