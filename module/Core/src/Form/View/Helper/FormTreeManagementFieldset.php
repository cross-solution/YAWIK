<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2016 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace Core\Form\View\Helper;

use Core\Form\Tree\ManagementFieldset;
use Zend\Form\View\Helper\AbstractHelper;

/**
 * ${CARET}
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @todo write test
 */
class FormTreeManagementFieldset extends AbstractHelper
{
    public function __invoke(ManagementFieldset $fieldset)
    {
        $children = $fieldset->get('children');
    }
}
