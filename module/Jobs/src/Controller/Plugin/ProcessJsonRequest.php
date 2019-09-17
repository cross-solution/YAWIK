<?php declare(strict_types=1);
/**
 * YAWIK
 *
 * @filesource
 * @copyright 2019 CROSS Solution <https://www.cross-solution.de>
 * @license MIT
 */

namespace Jobs\Controller\Plugin;

use Jobs\Entity\JobInterface;
use Zend\Mvc\Controller\Plugin\AbstractPlugin;

/**
 * TODO: description
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * TODO: write tests
 */
class ProcessJsonRequest extends AbstractPlugin
{
    private $serverUrl;
    private $basePath;
    private $dateFormat;
    private $jobUrl;
    private $applyUrl;
    private $organizationImageCache;

    public function __construct(
        $serverUrl,
        $basePath,
        $dateFormat,
        $jobUrl,
        $applyUrl,
        $organizationImageCache
    ) {
        $this->serverUrl = $serverUrl;
        $this->basePath = $basePath;
        $this->dateFormat = $dateFormat;
        $this->jobUrl = $jobUrl;
        $this->applyUrl = $applyUrl;
        $this->organizationImageCache = $organizationImageCache;
    }

    public function __invoke(array $data): array
    {
        /** @var \Zend\Paginator\Paginator $paginator */
        $paginator = $data['jobs'];

        $result = [
            'total' => $paginator->getTotalItemCount(),
            'count' => $paginator->getCurrentItemCount(),
            'currentPage' => $paginator->getCurrentPageNumber(),
            'totalPages' => $paginator->count(),
            'jobsPerPage' => $paginator->getItemCountPerPage(),
            'jobs' => $this->getJobs($paginator),
        ];

        return $result;
    }

    private function getJobs($paginator)
    {
        $jobs = [];
        foreach ($paginator as $job) {
            /** @var \Jobs\Entity\Job $job */
            $jobArr = [
                'id' => $job->getId(),
                'title' => $job->getTitle(),
                'link' => ($this->serverUrl)(($this->jobUrl)($job, ['linkOnly' => true])),
                'dateStart' => $this->extractStartDate($job),
                'organization' => $this->extractOrganization($job),
                'organizationLogo' => $this->extractOrganizationLogo($job),
                'location' => $job->getLocation(),
                'locations' => $this->extractLocations($job),
                'apply' => ($this->applyUrl)($job, ['linkOnly' => true, 'absolute' => true]),
            ];


            $jobs[] = $jobArr;
        }

        return $jobs;
    }

    private function extractStartDate($job)
    {
        if ($date = $job->getDatePublishStart()) {
            return $date->format('Y-m-d');
        }

        if ($date = $job->getDateCreated()) {
            return $date->format('Y-m-d');
        }

        return null;
    }

    private function extractOrganization(JobInterface $job)
    {
        if ($org = $job->getOrganization()) {
            return $org->getOrganizationName()->getName();
        }

        return $job->getCompany() ?? null;
    }

    private function extractOrganizationLogo(JobInterface $job)
    {
        if (($org = $job->getOrganization()) && $org->getImage()) {
            return ($this->serverUrl)(
                ($this->basePath)(
                    $this->organizationImageCache->getUri($org->getImage(true))
                )
            );
        }

        return null;
    }

    private function extractLocations($job)
    {
        $locations = [];
        foreach ($job->getLocations() as $loc) {
            $locations[] = $loc->toArray();
        }

        return $locations;
    }
}
