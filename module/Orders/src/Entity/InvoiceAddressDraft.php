<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2016 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace Orders\Entity;

use Core\Entity\EntityInterface;
use Core\Entity\EntityTrait;
use Core\Entity\IdentifiableEntityInterface;
use Core\Entity\IdentifiableEntityTrait;
use Core\Entity\ModificationDateAwareEntityInterface;
use Core\Entity\ModificationDateAwareEntityTrait;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/**
 * ${CARET}
 *
 * @ODM\Document(collection="orders.invoiceaddressdrafts", repositoryClass="\Orders\Repository\InvoiceAddressDrafts")
 * @ODM\HasLifecycleCallbacks
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @todo write test 
 */
class InvoiceAddressDraft implements EntityInterface, IdentifiableEntityInterface, ModificationDateAwareEntityInterface
{
    use EntityTrait, IdentifiableEntityTrait, ModificationDateAwareEntityTrait;


    /**
     *
     * @ODM\String
     * @ODM\Index
     * @var string
     */
    protected $jobId;

    /**
     *
     * @ODM\EmbedOne(targetDocument="\Orders\Entity\InvoiceAddress")
     * @var InvoiceAddressInterface
     */
    protected $invoiceAddress;

    /**
     * @param \Orders\Entity\InvoiceAddressInterface $invoiceAddress
     *
     * @return self
     */
    public function setInvoiceAddress($invoiceAddress)
    {
        $this->invoiceAddress = $invoiceAddress;

        return $this;
    }

    /**
     * @return \Orders\Entity\InvoiceAddressInterface
     */
    public function getInvoiceAddress()
    {
        return $this->invoiceAddress;
    }

    /**
     * @param string $jobId
     *
     * @return self
     */
    public function setJobId($jobId)
    {
        $this->jobId = $jobId;

        return $this;
    }

    /**
     * @return string
     */
    public function getJobId()
    {
        return $this->jobId;
    }
}