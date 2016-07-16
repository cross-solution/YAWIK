<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

/** HttploadStrategy.php */
namespace Organizations\Entity\Hydrator\Strategy;

use Zend\Hydrator\Strategy\StrategyInterface;
use Zend\Http\Client as HttpClient;
use Doctrine\MongoDB\GridFSFile;
use Organizations\Entity\OrganizationImage as OrganizationImageEntity;

class HttploadStrategy implements StrategyInterface
{
    /**
     * @var $repository \Organizations\Repository\OrganizationImage
     */
    protected $repository;

    /**
     * @param $repository
     */
    public function __construct($repository = null)
    {
        $this->repository = $repository;
        return $this;
    }

    /**
     * @param mixed $value$organizationImageEntity
     *
     * @return mixed
     */
    public function extract($value)
    {
        return $value;
    }

    /**
     * @param mixed $value
     * @param null  $data
     * @param null  $object
     *
     * @return mixed
     */
    public function hydrate($value, $data = null, $object = null)
    {
        $organizationImageEntity = $value;
        // @TODO: has the Object already an Image, than take this

        if (is_string($value)) {
            $client = new HttpClient($value, array('sslverifypeer' => false));
            $response = $client->send();
            $file = new GridFSFile();
            $file->setBytes($response->getBody());

            /* @var OrganizationImageEntity $organizationImageEntity */
            $organizationImageEntity = $this->repository->create();
            $organizationImageEntity->setType($response->getHeaders()->get('Content-Type')->getFieldValue());
            
            $organizationImageEntity->setFile($file);
        }
        
        return $organizationImageEntity;
    }
}
