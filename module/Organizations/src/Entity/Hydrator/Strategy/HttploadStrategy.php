<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright https://yawik.org/COPYRIGHT.php
 * @license   MIT
 */

/** HttploadStrategy.php */
namespace Organizations\Entity\Hydrator\Strategy;

use Laminas\Hydrator\Strategy\StrategyInterface;
use Laminas\Http\Client as HttpClient;
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
     * @param mixed $value $organizationImageEntity
     *
     * @param object|null $object
     * @return mixed
     */
    public function extract($value, ?object $object = null): array
    {
        return $value;
    }

    /**
     * @param mixed $value
     * @param array|null $data
     * @return mixed|OrganizationImageEntity
     */
    public function hydrate($value, ?array $data)
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
