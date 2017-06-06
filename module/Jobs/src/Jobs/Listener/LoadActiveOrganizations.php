<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2017 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace Jobs\Listener;

use Core\Listener\Events\AjaxEvent;

/**
 * Listener paginates through the list of active organizations.
 * 
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @since 0.30
 */
class LoadActiveOrganizations 
{
    /**
     * The paginator.
     *
     * @var \Zend\Paginator\Paginator
     */
    private $paginator;

    public function __construct($paginator)
    {
        $this->paginator = $paginator;
    }

    public function __invoke(AjaxEvent $event)
    {
        $request = $event->getRequest();
        $query   = $request->getQuery();
        $page    = $query->get('page', 1);

        $this->paginator->setCurrentPageNumber($page)
                        ->setItemCountPerPage(30);

        $options = [];
        foreach ($this->paginator as $org) {
            /* @var $org \Organizations\Entity\Organization */

            $name     = $org->getOrganizationName()->getName();
            $contact  = $org->getContact();
            $image    = $org->getImage();
            $imageUrl = $image ? $image->getUri() : '';

            $options[] = [ 'id' => $org->getId(),
                           'text' => $name . '|'
                                      . $contact->getCity() . '|'
                                      . $contact->getStreet() . '|'
                                      . $contact->getHouseNumber() . '|'
                                      . $imageUrl
            ];
        }

        return [
            'items' => $options,
            'count' => $this->paginator->getTotalItemCount(),
        ];
    }
}