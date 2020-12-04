<?php

declare(strict_types=1);

namespace Yawik\Migration\Migrator\Version36;


use Doctrine\ODM\MongoDB\DocumentManager;
use MongoDB\BSON\ObjectId;
use MongoDB\Collection;
use Organizations\Entity\Organization;
use Organizations\Entity\OrganizationImage;
use Symfony\Component\Console\Output\OutputInterface;
use Yawik\Migration\Contracts\ProcessorInterface;
use Yawik\Migration\Util\MongoUtilTrait;

class OrganizationProcessor implements ProcessorInterface
{
    use MongoUtilTrait;

    private OutputInterface $output;
    private Collection $collection;

    public function __construct(
        DocumentManager $dm,
        OutputInterface $output
    )
    {

        $database = $dm->getDocumentDatabase(Organization::class);
        $this->collection = $database->selectCollection('organizations');
        $this->output = $output;
    }

    public function process(): bool
    {
        $col = $this->collection;

        $status = true;
        foreach($col->find() as $current){
            $val = $this->getNamespacedValue('images.images', $current);
            if(!is_null($val)){
                $col->updateOne(
                    ['_id' => $current['_id']],
                    [
                        '$set' => [
                            'images.images' => $this->processImages($val)
                        ]
                    ]
                );
            }
        }

        return $status;
    }

    private function processImages(array $images)
    {
        $newImages = [];
        foreach($images as $image){
            if(!isset($image['$ref'])){
                $oid = null;
                if(is_string($image)){
                    $oid = new ObjectId($image);
                }
                if(!is_null($oid)){
                    $newImages[] = [
                        '$ref' => 'organizations.images.files',
                        '$id' => $oid,
                        '_entity' => OrganizationImage::class
                    ];
                }
            }else{
                $image['$ref'] = 'organizations.images.files';
                $image['_entity'] = OrganizationImage::class;
                $newImages[] = $image;
            }
        }
        return $newImages;
    }
}