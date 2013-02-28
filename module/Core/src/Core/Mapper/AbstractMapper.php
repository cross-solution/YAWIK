<?php
/**
 * Cross Applicant Management
 *
 * @copyright (c) 2013 Cross Solution (http://cross-solution.de)
 * @license   GPLv3
 */

namespace Core\Mapper;

use \Core\Model\ModelInterface;

/**
 *
 */
class AbstractMapper implements MapperInterface
{

    protected $_modelPrototype;
    
    public function setModelPrototype(ModelInterface $model)
    {
        $this->_modelPrototype = $model;
    }
    
    public function create(array $data=array())
    {
        $model = clone $this->_modelPrototype;
        $model->setData($data);
        return $model;
    }
}