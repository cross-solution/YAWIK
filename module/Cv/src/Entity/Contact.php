<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

namespace Cv\Entity;

use Auth\Entity\Info;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Auth\Entity\InfoInterface;
use Core\Entity\Hydrator\EntityHydrator;
use Core\Entity\Hydrator\Strategy\FileCopyStrategy;

/**
 * Holds the contact information including the optional photo of the applicant.
 *
 * @ODM\EmbeddedDocument
 */
class Contact extends Info
{

    /**
     * Contact image
     *
     * @var ContactImage
     * @ODM\ReferenceOne(targetDocument="\Cv\Entity\ContactImage", storeAs="id", nullable=true, cascade={"persist"})
     * @ODM\Index
     */
    protected $image;
    
    /**
     * Creates a Contact
     *
     * @param InfoInterface|null $userInfo
     * @uses inherit()
     */
    public function __construct(InfoInterface $userInfo = null)
    {
        if ($userInfo) {
            $this->inherit($userInfo);
        }
    }

    /**
     * Inherit data from an {@link UserInfoInterface}.
     *
     * Copies the user image to an application attachment.
     * @param InfoInterface $info
     * @return $this
     */
    public function inherit(InfoInterface $info)
    {
        $hydrator      = new EntityHydrator();
        $imageStrategy = new FileCopyStrategy(new ContactImage());
        
        $hydrator->addStrategy('image', $imageStrategy);
        
        $data = $hydrator->extract($info);
        $hydrator->hydrate($data, $this);
        
        return $this;
    }
}
