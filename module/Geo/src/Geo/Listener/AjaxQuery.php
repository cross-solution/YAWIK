<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2017 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace Geo\Listener;

use Core\Listener\Events\AjaxEvent;
use Geo\Service\AbstractClient;

/**
 * ${CARET}
 * 
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @todo write test 
 */
class AjaxQuery 
{

    private $client;

    public function __construct(AbstractClient $client)
    {
        $this->client = $client;
    }

    public function __invoke(AjaxEvent $event)
    {
        $request = $event->getRequest();
        $query   = $request->getQuery();
        
        $result  = $this->client->query($query->get('q'), ['lang' => $query->get('lang')]);

        return ['items' => $result];
    }
}