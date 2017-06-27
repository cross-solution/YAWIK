<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2017 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace Jobs\Entity\Decorator;

use Jobs\Entity\JobInterface;
use Jobs\Entity\JsonLdProviderInterface;

/**
 * ${CARET}
 * 
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @todo write test 
 */
class JsonLdProvider implements JsonLdProviderInterface
{

    private $job;

    public function __construct(JobInterface $job)
    {
        $this->job = $job;
    }

    /**
     * creates a JSON-LD specified in https://developers.google.com/search/docs/data-types/job-postings
     * 
     * Results can be tested in https://search.google.com/structured-data/testing-tool
     *
     * @return string
     */
    public function toJsonLd()
    {
        $organizationName = $this->job->getOrganization()->getOrganizationName()->getName();

        $array=[
            '@context'=>'http://schema.org/',
            '@type' => 'JobPosting',
            'title' => $this->job->getTitle(),
            'description' => $this->getDescription($this->job),
            'datePosted' => $this->job->getDatePublishStart()->format('Y-m-d'),
            'identifier' => [
                '@type' => 'PropertyValue',
                'value' => $this->job->getApplyId(),
                'name' => $organizationName,
            ],
            'hiringOrganization' => [
                '@type' => 'Organization',
                'name' => $organizationName,
                // @TODO add the link to the Logo of the company
                // https://developers.google.com/search/docs/data-types/logo
                // 'logo' => $this->job->getOrganization()->getImage()->getUri(),
            ],
            'jobLocation' => $this->getLocations($this->job->getLocations()),
            'employmentType' => $this->job->getClassifications()->getEmploymentTypes()->getValues()
        ];
        return json_encode($array);
    }

    private function getLocations($locations){
        $array=[];
        foreach($locations as $location){ /* @var \Core\Entity\LocationInterface $location */
            array_push(
                $array,
                [
                    '@type' => 'Place',
                    'address' => [
                        '@type' => 'PostalAddress',
                        'postalCode' => $location->getPostalCode(),
                        'addressLocality' => $location->getCity(),
                        'addressCountry' => $location->getCountry(),
                        'addressRegion' => $location->getRegion(),
                    ]
                ]);
        }
        return $array;
    }

    private function getDescription(JobInterface $job) {

        $description=sprintf(
            "<p>%s</p>".
            "<h1>%s</h1>".
            "<h3>Requirements</h3><p>%s</p>".
            "<h3>Qualifications</h3><p>%s</p>".
            "<h3>Benefits</h3><p>%s</p>",
            $job->getTemplateValues()->getDescription(),
            $job->getTemplateValues()->getTitle(),
            $job->getTemplateValues()->getRequirements(),
            $job->getTemplateValues()->getQualifications(),
            $job->getTemplateValues()->getBenefits()
        );
        return $description;
    }
}