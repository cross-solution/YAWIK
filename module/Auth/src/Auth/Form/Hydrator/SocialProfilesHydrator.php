<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

/** Auth form hydrators */
namespace Auth\Form\Hydrator;

use Laminas\Hydrator\AbstractHydrator;

/**
 * Hydrator for social profiles collections.
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 */
class SocialProfilesHydrator extends AbstractHydrator
{
    /**
     * Map for form element names to social profile classes.
     * Format:
     *      [element name] => [Full Qualified class name]
     *
     * @var array
     */
    protected $profileClassMap = array(
        'facebook' => '\Auth\Entity\SocialProfiles\Facebook',
        'xing'     => '\Auth\Entity\SocialProfiles\Xing',
        'linkedin' => '\Auth\Entity\SocialProfiles\LinkedIn',
    );
    
    /**
     * Creates a social profiles collection hydrator.
     *
     * @param array $profileClassMap
     */
    public function __construct(array $profileClassMap = array())
    {
        $this->profileClassMap = array_merge($this->profileClassMap, $profileClassMap);
    }
    
    /**
     * Adds or removes a social profile from the collection.
     *
     * @param array $data
     * @param Collection $object
     * @see \Laminas\Hydrator\HydratorInterface::hydrate()
     * @return \Auth\Entity\SocialProfiles\ProfileInterface
     */
    public function hydrate(array $data, $object)
    {
        foreach ($data as $name => $value) {
            if (!isset($this->profileClassMap[$name])) {
                continue;
            }
            
            if (empty($value)) {
                // We need to check, if collection has a profile and
                // remove it.
                foreach ($object as $p) {
                    if ($p instanceof $this->profileClassMap[$name]) {
                        $object->removeElement($p);
                        continue 2;
                    }
                }
                // No profile found, so do nothing.
                continue;
            }
            
            if (is_string($value)) {
                $value = \Laminas\Json\Json::decode($value, \Laminas\Json\Json::TYPE_ARRAY);
            }

            /* If there is already a profile of this type, we do not need to
             * add it, but update the data only.
             */
            foreach ($object as $p) {
                if ($p instanceof $this->profileClassMap[$name]) {
                    // Already a profile in the collection, just update and continue main loop.
                    $p->setData($value);
                    continue 2;
                }
            }
                
            // We need to add a new profile to the collection.
            $class = $this->profileClassMap[$name];
            $profile = new $class();
            $profile->setData($value);
            $object->add($profile);
        }
        return $object;
    }
    
    /**
     * Extracts profile data from the collection.
     *
     * @param Collection $object
     * @return array profile data in the format [profile name] => [profile data].
     * @see \Laminas\Hydrator\HydratorInterface::extract()
     */
    public function extract($object): array
    {
        $return = array();
        foreach ($object as $profile) {
            $return[strtolower($profile->getName())] = $profile->getData();
        }
        return $return;
    }
}
