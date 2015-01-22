<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013-2014 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

/** HttploadStrategy.php */ 
namespace Organizations\Entity\Hydrator\Strategy;

use Zend\Stdlib\Hydrator\Strategy\StrategyInterface;
use Zend\Http\Client as HttpClient;
use Doctrine\MongoDB\GridFSFile;
use Organizations\Entity\OrganizationImage;

class HttploadStrategy implements StrategyInterface
{
    protected $repository;
    
    public function __construct($repository = Null)
    {
        $this->repository = $repository;
        return $this;
    }
    
    public function extract ($value)
    {
        return $value;
    }

    public function hydrate ($value, $data = Null, $object = Null)
    {
        $organizationImageEntity = $value;
        // @TODO: has the Object already an Image, than take this
        
        // 
        if (is_string($value)) {
            $client = new HttpClient($value, array('sslverifypeer' => false));
            $response = $client->send();
            $file = new GridFSFile();
            $file->setBytes($response->getBody());

            $organizationImageEntity = $this->repository->create();
            $organizationImageEntity->setType($response->getHeaders()->get('Content-Type')->getFieldValue());
            
            $organizationImageEntity->setFile($file);
        }
        
        return $organizationImageEntity;
    }
}

