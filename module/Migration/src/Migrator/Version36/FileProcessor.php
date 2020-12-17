<?php

declare(strict_types=1);

namespace Yawik\Migration\Migrator\Version36;


use Auth\Entity\User;
use Doctrine\ODM\MongoDB\DocumentManager;
use MongoDB\BSON\ObjectId;
use MongoDB\Collection;
use MongoDB\Database;
use MongoDB\GridFS\Bucket;
use Mpdf\Tag\P;
use Psr\Container\ContainerInterface;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Output\OutputInterface;
use Yawik\Migration\Contracts\ProcessorInterface;
use Yawik\Migration\Exception\MigrationException;
use Yawik\Migration\Util\MongoUtilTrait;

class FileProcessor implements ProcessorInterface
{
    use MongoUtilTrait;

    /**
     * @var DocumentManager
     */
    private DocumentManager $dm;

    private string $document;

    /**
     * @var OutputInterface
     */
    private OutputInterface $output;

    protected Database $database;

    protected Collection $collection;

    private Bucket $bucket;

    private string $bucketName;

    private string $fieldName;

    public function __construct(
        DocumentManager $dm,
        OutputInterface $output,
        string $bucketName
    )
    {
        $this->dm = $dm;
        $this->output = $output;
        $this->bucketName = $bucketName;

        $this->database = $dm->getDocumentDatabase(User::class);
        $this->bucket = $this->database->selectGridFSBucket(['bucketName' => $bucketName]);
        $this->collection = $this->database->selectCollection($bucketName.'.files');
    }

    public function process(): bool
    {
        $bucket = $this->bucket;
        $count = $this->collection->countDocuments();
        $progressBar = new ProgressBar($this->output, $count);
        $progressBar->setFormat(
            "<info>processing <comment>{$this->bucketName}</comment> bucket</info> [%current%/%max%]"
        );

        $cursor = $bucket->find();
        foreach($cursor as $current){
            $progressBar->advance();
            try{
                $this->processFile($current['_id']);
            }catch (\Exception $exception){
                $progressBar->finish();
                throw new MigrationException($exception->getMessage());
            }

        }
        $progressBar->finish();
        $this->output->writeln("");
        $this->output->writeln("");
        return true;
    }

    private function processFile(ObjectId $fileId)
    {
        $database = $this->database;
        $bucketName = $this->bucketName;

        $fcol = $database->selectCollection($bucketName.'.files');
        $oldMeta = $fcol->findOne(['_id'=>$fileId]);
        if(is_null($oldMeta)){
            return;
        }

        $metaMap = [
            'user' => 'metadata.user',
            'permissions' => 'metadata.permissions',
            'mimetype' => 'metadata.contentType',
            'belongsTo' => 'metadata.belongsTo',
            'key' => 'metadata.key',
            'md5' => 'metadata.md5',
            'filename' => 'metadata.name',
        ];

        $options = [];
        $set = [];
        $unset = [];

        foreach($metaMap as $from => $to){
            $exp = explode('.', $from);
            $fromKey = $exp[0];
            $value = $this->getNamespacedValue($from, $oldMeta);
            if(isset($oldMeta[$from])){
                $set[$to] = $value;
                $unset[$fromKey] = true;
            }
        }

        if(!is_null($oldMeta['name'])){
            $set['metadata.name'] = $oldMeta['name'];
        }

        //'uploadedDate' => 'uploadDate',
        //'dateuploaded.date' => 'uploadDate',
        //'dateUploaded.date' => 'uploadDate'
        $dateMap = [
            'uploadedDate',
            'dateuploaded',
            'dateUploaded'
        ];
        foreach($dateMap as $key){
            if(!is_null($date = $oldMeta[$key])){
                $set['uploadDate'] = $date;
                $unset[$key] = true;
            }
        }

        if(!empty($set)){
            $options['$set'] = $set;
        }
        if(!empty($unset)){
            $options['$unset'] = $unset;
        }
        if(!empty($options)){
            $fcol->updateOne(
                ['_id' => $fileId],
                $options
            );
        }
    }
}