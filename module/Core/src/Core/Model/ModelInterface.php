<?php
/**
 * Cross Applicant Management
 *
 * @copyright (c) 2013 Cross Solution (http://cross-solution.de)
 * @license   GPLv3
 */

namespace Core\Model;


/**
 *
 */
interface ModelInterface 
{

    public function getId();
    public function setData(array $data);
}