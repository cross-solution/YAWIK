<?php
/**
 * Cross Applicant Management
 *
 * @filesource
 * @copyright (c) 2013 Cross Solution (http://cross-solution.de)
 * @license   GPLv3
 */

/** Core mappers */
namespace Core\Mapper;

use \Core\Model\ModelInterface;

/**
 * Partial implementation of \Core\Mapper\MapperInterface.
 * 
 * Implements the methods
 *
 * - setModelPrototype()
 *   Stores the model prototype as protected class member.
 *   
 * - create()
 * 
 */
abstract class AbstractMapper implements MapperInterface
{

    /**
     * The model prototype.
     * 
     * @var \Core\Model\ModelInterface
     */
    protected $_modelPrototype;
    
    /**
     * {@inheritdoc}
     * 
     * @see \Core\Mapper\MapperInterface::setModelPrototype()
     * @return \Core\Mapper\AbstractMapper
     */
    public function setModelPrototype(ModelInterface $model)
    {
        $this->_modelPrototype = $model;
        return $this;
    }
    
    /**
     * {@inheritdoc}
     */
    public function create(array $data=array())
    {
        $model = clone $this->_modelPrototype;
        $model->setData($data);
        return $model;
    }
}