<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013-2014 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

/** FileEntity.php */ 
namespace Core\Entity;

use Zend\Permissions\Acl\Resource\ResourceInterface;
use Auth\Entity\UserInterface;

/**
 * 
 
 */
interface FileInterface extends IdentifiableEntityInterface, 
                                ResourceInterface,
                                PermissionsAwareInterface
{
    
    public function getResourceId();
    
    public function setUser(UserInterface $user);
    
    public function getUser();
    
    public function setName($name);
    
    public function getName();
    
    public function getPrettySize();
    
    public function setType($mime);
    
    public function getType();
    
    public function setDateUploaded(\DateTime $date = null);
    
    public function getDateUploaded();
    
    public function getFile();
    
    public function setFile($file);
    
    public function getLength();
    
    public function getResource();
    
    public function getContent();
}

