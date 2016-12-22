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

use Core\Form\Hydrator\HydratorStrategyProviderInterface;
use Core\Form\Hydrator\HydratorStrategyProviderTrait;
use Traversable;
use Zend\Form\Element\Select as ZfSelect;
use Zend\Form\Element;
use Zend\Form\ElementInterface;
use Zend\Form\Exception;

/**
 * ${CARET}
 * 
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @todo write test 
 */
class Select extends ZfSelect implements HydratorStrategyProviderInterface
{
    use HydratorStrategyProviderTrait;
}