<?php

namespace Jobs\Form;

use Traversable;
use Zend\Stdlib\ArrayUtils;
use Zend\Form\Form;
use Core\Entity\Hydrator\EntityHydrator;
use Core\Entity\Hydrator\Strategy\ArrayToCollectionStrategy;
use Zend\Uri\Http;

/**
 * This form is used to import jobs via the API
 *
 * @package Jobs\Form
 */
class Import extends Form
{
    /**
     * @var
     */
    protected $host;

    /**
     * @param $host
     * @return $this
     */
    public function setHost($host)
    {
        $this->host = $host;
        return $this;
    }

    public function getHydrator()
    {
        if (!$this->hydrator) {
            $hydrator = new EntityHydrator();
            $this->setHydrator($hydrator);
        }
        return $this->hydrator;
    }

    public function setData($data)
    {
        if ($data instanceof Traversable) {
            $data = ArrayUtils::iteratorToArray($data);
        }

        $isAts = isset($data['atsEnabled']) && $data['atsEnabled'];
        $isUri = isset($data['uriApply']) && !empty($data['uriApply']);
        $email = isset($data['contactEmail']) ? $data['contactEmail'] : '';
        $data['atsMode']['oneClickApply'] = 0;
        if ($isAts && $isUri) {
            $data['atsMode']['mode'] = 'uri';
            $data['atsMode']['uri'] = $data['uriApply'];
            $uri = new Http($data['uriApply']);
            if ($uri->getHost() == $this->host) {
                $data['atsMode']['mode'] = 'intern';
            }
        } elseif ($isAts && !$isUri) {
            $data['atsMode']['mode'] = 'intern';
        } elseif (!$isAts && !empty($email)) {
            $data['atsMode']['mode'] = 'email';
            $data['atsMode']['email'] = $email;
        } else {
            $data['atsMode']['mode'] = 'none';
        }

        if (!array_key_exists('job', $data)) {
            $data = array('job' => $data);
        }

        return parent::setData($data);
    }



    public function init()
    {
        $this->setName('job-create');
        $this->setAttribute('id', 'job-create');


        $this->add(
            array(
                'type' => 'Jobs/ImportFieldset',
                'name' => 'job',
                'options' => array(
                    'use_as_base_fieldset' => true
                ),
            )
        );

        $this->add(
            array(
                'type' => 'DefaultButtonsFieldset'
            )
        );
    }
}
