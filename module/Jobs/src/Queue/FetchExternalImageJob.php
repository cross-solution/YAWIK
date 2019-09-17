<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2019 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace Jobs\Queue;

use Core\Queue\Job\MongoJob;
use Core\Queue\LoggerAwareJobTrait;
use Jobs\Repository\Job;
use Jobs\Entity\JobInterface as JobEntityInterface;
use Zend\Http\Client;
use Zend\Log\LoggerAwareInterface;

/**
 * ${CARET}
 * 
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @todo write test 
 */
class FetchExternalImageJob extends MongoJob implements LoggerAwareInterface
{
    use LoggerAwareJobTrait;

    private $repository;

    protected static function filterPayload($payload)
    {
        if ($payload instanceOf JobEntityInterface) {
            return ['jobId' => $payload->getId()];
        }

        return parent::filterPayload($payload);
    }

    public function __construct(Job $repository = null)
    {
        $this->repository = $repository;
    }

    public function execute()
    {
        $logger = $this->getLogger();

        if (!$this->repository) {
            return $this->failure('Cannot execute without repository.');
        }
        $payload = $this->getContent();

        if (!isset($payload['jobId'])) {
            return $this->failure('Missing jobId in playload.');
        }
        /* @var \Jobs\Entity\Job $jobEntity */
        $jobEntity = $this->repository->find($payload['jobId']);

        if (!$jobEntity) {
            return $this->failure('No job entity with the id ' . $payload['jobId'] . ' was found.');
        }

        $uri = $jobEntity->getLogoRef();

        if (0 !== strpos($uri, 'http')) {
            $logger->notice('logoRef seems not to be external: ' . $uri);
            return $this->success('Skip fetching for this job.');
        }

        $logger->debug('Trying to fetch image from ' . $uri);

        $client = new Client($uri);
        $response = $client->send();

        if (200 != $response->getStatusCode()) {
            $logger->err('Received status code ' . $response->getStatusCode() . ' when trying to fetch ' . $uri);

            return $this->recoverable('Status code ' . $response->getStatusCode() . ' received.', ['delay' => '+5 minutes']);
        }

        $content = $response->getBody();
        $type = $response->getHeaders()->get('Content-Type')->getFieldValue();
        list(,$ext) = explode('/', $type, 2);
        $imageName = '/static/Jobs/logos/' . $jobEntity->getId() . '.' . $ext;

        if (false === file_put_contents("public$imageName", $content, FILE_BINARY)) {
            return $this->failure('Writing image to "public' . $imageName . '" failed.');
        }

        $logger->info('Saved job logo as ' . basename($imageName));
        $jobEntity->setLogoRef($imageName);
        $this->repository->store($jobEntity);
        $logger->info('Saved job logo as ' . basename($imageName));

        return $this->success();
    }
}
