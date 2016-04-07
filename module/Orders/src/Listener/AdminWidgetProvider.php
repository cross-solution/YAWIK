<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2016 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace Orders\Listener;

use Core\Controller\AdminControllerEvent;

/**
 * ${CARET}
 * 
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @todo write test 
 */
class AdminWidgetProvider 
{
    /**
     *
     *
     * @var \Orders\Repository\Orders
     */
    protected $orders;

    /**
     *
     *
     * @var \Orders\Repository\InvoiceAddressDrafts
     */
    protected $drafts;

    public function __construct($orderRepository, $draftsRepository)
    {
        $this->orders = $orderRepository;
        $this->drafts = $draftsRepository;
    }

    public function __invoke(AdminControllerEvent $event)
    {
        $ordersCount = $this->orders->count();
        $draftsCount = $this->drafts->count();

        $data = [
            /*@translate*/ 'Orders'
                => $ordersCount,
            /*@translate*/ 'Invoice address drafts'
                => $draftsCount
        ];

        $event->addViewVariables('orders', ['data' => $data], -1);

    }
}