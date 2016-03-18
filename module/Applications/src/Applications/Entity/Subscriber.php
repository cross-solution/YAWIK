<?php
/**
 * YAWIK
 *
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

namespace Applications\Entity;

use Core\Entity\AbstractIdentifiableEntity;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/**
 * Personal information of a subscriber. This class can translate a subscriber ID into an subscriber name
 * by calling an API of another YAWIK
 *
 * @ODM\Document (collection="applications.subscribers", repositoryClass="Applications\Repository\Subscriber")
 */
class Subscriber extends AbstractIdentifiableEntity implements SubscriberInterface
{
  
    /**
     * name of the instance (other YAWIK, or jobboard etc.) who has
     * published the job posting. Technically it's a name of a referrer
     * of an application
     *
     * @ODM\Field(type="string")
     */
    protected $name;
    
    /**
     * Referer of a job posting. This referrer must be submitted within the
     * application form
     *
     * @ODM\Field(type="string")
     **/
    protected $uri;
   
    /**
     * Gets the name of the instance, who has published the job ad.
     *
     * @return String
     */
    public function getName()
    {
        if (empty($this->name)) {
            $this->fetchData();
        }

        return $this->name;
    }
    
    /**
     * Sets a name of the Instance, who has published the job
     *
     * @param String $name
     * @return Subscriber
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }
   
    /**
     * Gets the job publishers URI
     *
     * @return String
     */
    public function getUri()
    {
        return $this->uri;
    }
    
    /**
     * Sets the job publishers URI
     *
     * @param String $uri
     * @return Subscriber
     */
    public function setUri($uri)
    {
        $this->uri = $uri;
        return $this;
    }

    /**
     * Fetches and sets data from the remote system via {@link $this->uri}.
     */
    protected function fetchData()
    {
        $uri = $this->getUri();
        if (!$uri) {
            return;
        }

        $client = new \Zend\Http\Client($this->getUri());
        $client->setMethod('GET');

        try {
            $response = $client->send();
        } catch (\Exception $e) {
            return;
        }

        $status = $response->getStatusCode();
        if ($status == 200) {
            $result = $response->getBody();
            $result = (array) json_decode($result);
            if (0 < count($result)) {
                $name = array_pop($result);
                $this->setName($name);
            }
        }
    }
}
