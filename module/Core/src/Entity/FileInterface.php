<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 * @author Carsten Bleek <bleek@cross-solution.de>
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @author Miroslav Fedeleš <miroslav.fedeles@gmail.com>
 */

/** FileEntity.php */
namespace Core\Entity;

use Zend\Permissions\Acl\Resource\ResourceInterface;
use Auth\Entity\UserInterface;

/**
 *

 */
interface FileInterface extends
    IdentifiableEntityInterface,
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
    
    /**
     * Gets the URI of a file
     *
     * @return string|null
     * @since 0.27
     */
    public function getUri();
}
