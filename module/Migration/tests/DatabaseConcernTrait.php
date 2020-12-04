<?php

declare(strict_types=1);

namespace Yawik\Migration\Tests;

use Auth\Entity\User;
use Doctrine\ODM\MongoDB\DocumentManager;
use MongoDB\Client;
use MongoDB\Collection;
use MongoDB\Database;
use MongoDB\GridFS\Bucket;
use Psr\Container\ContainerInterface;
use Yawik\Migration\Util\MongoUtilTrait;

/**
 * Trait DatabaseConcernTrait
 * @package Yawik\Migration\Tests
 */
trait DatabaseConcernTrait
{
    use MongoUtilTrait;

    /**
     * @var ContainerInterface
     */
    protected $dbConcernContainer;

    /**
     * @var DocumentManager
     */
    private DocumentManager $dbConcernDM;

    protected function initialize(ContainerInterface $dbConcernContainer)
    {
        $this->dbConcernContainer = $dbConcernContainer;
        $this->dbConcernDM = $dbConcernContainer->get(DocumentManager::class);

        return $this;
    }

    protected function getMongodbClient(): Client
    {
        return $this->getDoctrine()->getClient();
    }

    protected function getDatabase($className = User::class): Database
    {
        return $this->dbConcernDM->getDocumentDatabase($className);
    }

    protected function getBucket(string $bucketName): Bucket
    {
        return $this->getDatabase()->selectGridFSBucket(['bucketName' => $bucketName]);
    }

    protected function getCollection(string $name, array $options = array()): Collection
    {
        return $this->getDatabase()->selectCollection($name, $options);
    }

    protected function createFile(string $bucketName, string $source = __FILE__, $fileName = "test.php")
    {
        $stream = fopen($source, 'rb');
        $bucket = $this->getBucket($bucketName);
        $fileId = $bucket->uploadFromStream($fileName, $stream);
        $meta = $bucket->findOne(['_id' => $fileId]);
        $meta['name'] = "name.jpeg";
        $meta['mimetype'] = 'test/content';

        $collection = $this->getCollection($bucketName.'.files');
        $collection->updateOne(
            ['_id' => $fileId],
            ['$set' => [
                'name' => $fileName,
                'mimetype' => 'test/content',
                'dateuploaded' => [
                    "date" => "2016-06-13T13:48:05.000+00:00",
                    "tz" => "Europe/Berlin"
                ],
                'permissions' => [
                    "type" => "Core\\Entity\\Permissions"
                ],
                "filename" => "/tmp/yk-copy",
                "uploadedDate" => "2016-06-13T13:48:05.473+00:00"
            ]]
        );
        return $fileId;
    }

    protected function loadJson(string $file)
    {
        $content = file_get_contents($file);
        return json_decode($content, true);
    }

    protected function insert(string $collectionName, array $document)
    {
        $this->getCollection($collectionName)->insertOne($document);
    }

    protected function drop(string $collectionName)
    {
        $this->getCollection($collectionName)->drop();
    }
}