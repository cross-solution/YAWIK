<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2016 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace Jobs\Form\Element;

use Jobs\Entity\Status;
use Core\Form\Element\Select;

/**
 * ${CARET}
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @todo write test
 */
class StatusSelect extends Select
{
    public function init()
    {
        $valueOptions = [
            Status::ACTIVE => /*@translate*/ 'Active',
            Status::INACTIVE => /*@translate*/ 'Inactive',
            Status::WAITING_FOR_APPROVAL => /*@translate*/ 'Waiting for approval',
            Status::CREATED => /*@translate*/ 'Created',
            Status::PUBLISH => /*@translate*/ 'Published',
            Status::REJECTED => /*@translate*/ 'Rejected',
            Status::EXPIRED => /*@translate*/ 'Expired',
        ];

        if (true === $this->getOption('include_all_option')) {
            $valueOptions = array_merge([
                                            'all' => /*@translate*/ 'All',
                                         ], $valueOptions);
        }

        $this->setValueOptions($valueOptions);

        foreach (['data-searchbox' => -1, 'data-allowclear' => 'false', 'class' => 'form-control'] as $attr => $value) {
            if (!$this->hasAttribute($attr)) {
                $this->setAttribute($attr, $value);
            }
        }

        if (null === $this->getName()) {
            $this->setName('status');
        }

        if (null === $this->getLabel()) {
            $this->setLabel(/*@translate*/ 'Status');
        }

        if (null === $this->getOption('description')) {
            $this->setOption('description', /*@translate*/ 'Select a job status.');
        }
    }

    public function setOptions($options)
    {
        parent::setOptions($options);

        if (null == $this->getLabel()) {
            $this->setLabel('Status');
        }

        if (!isset($this->options['description'])) {
            $this->options['description'] = /*@translate*/ 'Select a job status.';
        }

        if (null == $this->getName()) {
            $this->setName('status');
        }
    }

    public function getValueOptions()
    {
        $options = parent::getValueOptions();

        return true === $this->getOption('include_all_option')
               ? array_merge([ 'all' => /*@translate*/ 'All' ], $options)
               : $options;
    }
}
