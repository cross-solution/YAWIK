<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2017 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace Jobs\Form;

use Core\Form\MetaDataFieldset;
use Core\Form\ViewPartialProviderInterface;
use Core\Form\ViewPartialProviderTrait;

/**
 * ${CARET}
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @todo write test
 */
class CustomerNoteFieldset extends MetaDataFieldset implements ViewPartialProviderInterface
{
    use ViewPartialProviderTrait;

    protected $defaultPartial = 'jobs/form/customer-note';

    public function init()
    {
        $this->setAttribute('id', 'customerNoteFieldset');
        $this->setName('customerNote');
        $this->add([
                'type' => 'Textarea',
                'name' => 'note',
            ]);
    }
}
