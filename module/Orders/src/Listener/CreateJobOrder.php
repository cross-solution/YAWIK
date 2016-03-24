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

use Core\Entity\Collection\ArrayCollection;
use Core\Entity\Hydrator\EntityHydrator;
use Jobs\Listener\Events\JobEvent;
use Orders\Entity\Order;
use Orders\Entity\OrderInterface;
use Orders\Entity\Product;
use Orders\Entity\Snapshot\Job\Builder;
use Orders\Entity\InvoiceAddress;
use Zend\Session\Container;

/**
 * ${CARET}
 * 
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @todo write test 
 */
class CreateJobOrder 
{
    /**
     *
     *
     * @var \Orders\Options\ModuleOptions
     */
    protected $options;

    /**
     *
     *
     * @var \Jobs\Options\ProviderOptions
     */
    protected $providerOptions;
    /**
     *
     *
     * @var \Zend\Filter\FilterInterface
     */
    protected $priceFilter;

    /**
     *
     *
     * @var \Orders\Repository\Orders
     */
    protected $repository;

    public function __construct($options, $providerOptions, $priceFilter, $repository)
    {
        $this->priceFilter     = $priceFilter;
        $this->options         = $options;
        $this->providerOptions = $providerOptions;
        $this->repository      = $repository;
    }

    public function __invoke(JobEvent $event)
    {
        $job = $event->getJobEntity();
        $sessionKey = 'Orders_JobInvoiceAddress_' . $job->getId();
        $session = new Container($sessionKey);

        if (!$session->values) { return; }

        $hydrator = new EntityHydrator();
        $invoiceAddress = $hydrator->hydrate($session->values, new InvoiceAddress());
        $snapshotBuilder = new Builder();
        $snapshot = $snapshotBuilder->build($job);
        $products = new ArrayCollection();

        foreach ($job->getPortals() as $key) {
            $product = new Product();
            $channel = $this->providerOptions->getChannel($key);

            $product->setName($channel->getLabel())
                    ->setProductNumber($channel->getExternalKey())
                    ->setQuantity(1);

            $products->add($product);
        }

        $data = [
            'type' => OrderInterface::TYPE_JOB,
            'taxRate' => $this->options->getTaxRate(),
            'price' => $this->priceFilter->filter($job->getPortals()), // must come after tax rate!
            'invoiceAddress' => $invoiceAddress,
            'currency' => $this->options->getCurrency(),
            'currencySymbol' => $this->options->getCurrencySymbol(),
            'entity' => $snapshot,
            'products' => $products,
        ];

        $order = $this->repository->create($data);

        $this->repository->store($order);
    }
}