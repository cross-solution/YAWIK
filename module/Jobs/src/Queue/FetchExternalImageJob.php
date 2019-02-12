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

use Core\Queue\Exception\FatalJobException;
use Core\Queue\Exception\RecoverableJobException;
use Core\Queue\LoggerAwareJobTrait;
use Jobs\Repository\Job;
use SlmQueue\Job\AbstractJob;
use Jobs\Entity\JobInterface as JobEntityInterface;
use Zend\Http\Client;
use Zend\Log\LoggerAwareInterface;

/**
 * ${CARET}
 * 
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @todo write test 
 */
class FetchExternalImageJob extends AbstractJob implements LoggerAwareInterface
{
    use LoggerAwareJobTrait;

    private $repository;

    public static function create(JobEntityInterface $jobEntity)
    {
        $job = new self();
        $job->setContent(['jobId' => $jobEntity->getId()]);

        return $job;
    }

    public function __construct(Job $repository = null)
    {
        $this->repository = $repository;
    }

    public function execute()
    {
        $logger = $this->getLogger();

        if (!$this->repository) {
            $logger->err('Cannot execute without repository.');

            throw new FatalJobException('Cannot execute without repository.');
        }
        $payload = $this->getContent();

        if (!isset($payload['jobId'])) {
            $logger->err('Missing jobId in playload.');
            throw new FatalJobException('Missing jobId in playload.');
        }
        /* @var \Jobs\Entity\Job $jobEntity */
        $jobEntity = $this->repository->find($payload['jobId']);

        if (!$jobEntity) {
            $logger->err('No job entity with the id ' . $payload['jobId'] . ' was found.');

            throw new FatalJobException('No job entity with the id ' . $payload['jobId'] . ' was found.');
        }

        $uri = $jobEntity->getLogoRef();

        if (0 !== strpos($uri, 'http')) {
            $logger->notice('logoRef seems not to be external: ' . $uri);
            $logger->info('Skip fetching for this job.');
            return;
        }

        $logger->debug('Trying to fetch image from ' . $uri);

        $client = new Client($uri);
        $response = $client->send();

        if (200 != $response->getStatusCode()) {
            $logger->err('Received status code ' . $response->getStatusCode() . ' when trying to fetch ' . $uri);

            throw new RecoverableJobException('Status code ' . $response->getStatusCode() . ' received.', ['delay' => '+5 minutes']);
        }

        $content = $response->getBody();
        $type = $response->getHeaders()->get('Content-Type')->getFieldValue();
        list(,$ext) = explode('/', $type, 2);
        $imageName = '/static/Jobs/logos/' . $jobEntity->getId() . '.' . $ext;

        if (false === file_put_contents("public$imageName", $content, FILE_BINARY)) {
            $logger->err('Writing image failed.');

            throw new FatalJobException('Writing image failed.');
        }

        $logger->info('Saved job logo as ' . basename($imageName));
        $jobEntity->setLogoRef($imageName);
        $this->repository->store($jobEntity);
        $logger->info('Saved job logo as ' . basename($imageName));

    }
}
