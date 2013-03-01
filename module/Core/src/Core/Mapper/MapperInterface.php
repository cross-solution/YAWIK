<?php
/**
 * Cross Applicant Management
 *
 * @copyright (c) 2013 Cross Solution (http://cross-solution.de)
 * @license   GPLv3
 */

namespace Core\Mapper;


use Core\Model\ModelInterface;
/**
 *
 */
interface MapperInterface
{

    public function setModelPrototype(ModelInterface $model);
    public function find($id);
    public function create(array $data=array());
    public function save(ModelInterface $model);
    
}