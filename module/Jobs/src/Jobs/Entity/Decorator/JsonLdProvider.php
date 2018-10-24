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

use Doctrine\Common\Collections\Collection;
use Jobs\Entity\JobInterface;
use Jobs\Entity\JsonLdProviderInterface;
use Jobs\Entity\TemplateValuesInterface;
use Zend\Json\Json;

/**
 * Decorates a job with implementing a toJsonLd method.
 *
 * This decorator *does not* delegate other methods.
 * 
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @author Carsten Bleek <bleek@cross-solution.de>
 * @todo write test 
 */
class JsonLdProvider implements JsonLdProviderInterface
{

    /**
     * the decorated job entity.
     *
     * @var \Jobs\Entity\JobInterface
     */
    private $job;

    /**
     * @param JobInterface $job
     */
    public function __construct(JobInterface $job)
    {
        $this->job = $job;
    }


    public function toJsonLd()
    {
        $organization = $this->job->getOrganization();
        $organizationName = $organization ? $organization->getOrganizationName()->getName() : $this->job->getCompany();

        $dateStart = $this->job->getDatePublishStart();
        $dateStart = $dateStart ? $dateStart->format('Y-m-d H:i:s') : null;
	    $dateEnd = $this->job->getDatePublishEnd();
        $dateEnd = $dateEnd ? $dateEnd->format('Y-m-d H:i:s') : null;
        if (!$dateEnd){
            $dateEnd = new \DateTime($dateStart);
            $dateEnd->add(new \DateInterval("P180D"));
            $dateEnd = $dateEnd->format('Y-m-d H:i:s');
        }

        $array=[
            '@context'=>'http://schema.org/',
            '@type' => 'JobPosting',
            'title' => $this->job->getTitle(),
            'description' => $this->getDescription($this->job->getTemplateValues()),
            'datePosted' => $dateStart,
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
            'employmentType' => $this->job->getClassifications()->getEmploymentTypes()->getValues(),
            'validThrough' => $dateEnd
        ];

        return Json::encode($array);
    }

    /**
     * Generates a location array
     *
     * @param Collection $locations,
     *
     * @return array
     */
    private function getLocations($locations){
        $array=[];
        foreach($locations as $location){ /* @var \Core\Entity\LocationInterface $location */
            array_push(
                $array,
                [
                    '@type' => 'Place',
                    'address' => [
                        '@type' => 'PostalAddress',
                        'streetAddress' => $location->getStreetname() .' '.$location->getStreetnumber(),
                        'postalCode' => $location->getPostalCode(),
                        'addressLocality' => $location->getCity(),
                        'addressCountry' => $location->getCountry(),
                        'addressRegion' => $location->getRegion(),
                    ]
                ]);
        }
        return $array;
    }

    /**
     * Generates a description from template values
     *
     * @param TemplateValuesInterface $values
     *
     * @return string
     */
    private function getDescription(TemplateValuesInterface $values) {

        $description=sprintf(
            "<p>%s</p>".
            "<h1>%s</h1>".
            "<h3>Requirements</h3><p>%s</p>".
            "<h3>Qualifications</h3><p>%s</p>".
            "<h3>Benefits</h3><p>%s</p>",
            $values->getDescription(),
            $values->getTitle(),
            $values->getRequirements(),
            $values->getQualifications(),
            $values->getBenefits()
        );
        return $description;
    }
}
