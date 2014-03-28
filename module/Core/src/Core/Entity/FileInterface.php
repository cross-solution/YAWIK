<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 Cross Solution (http://cross-solution.de)
 * @license   AGPLv3
 */

/** FileEntity.php */ 
namespace Core\Entity;



/**
 * 
 
 */
interface FileInterface extends IdentifiableEntityInterface
{
    
    public function setName($name);
    
    public function getName();
    
    public function setDateUploaded(\DateTime $date = null);
    
    public function getDateUploaded();
    
    public function getFile();
    
    public function setFile($file);
    
    public function getResource();
    
    public function getContent();
}

