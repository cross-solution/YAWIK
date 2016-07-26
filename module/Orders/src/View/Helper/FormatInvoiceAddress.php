<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2016 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace Orders\View\Helper;

use Orders\Entity\InvoiceAddressInterface;
use Zend\View\Helper\AbstractHelper;

/**
 * ${CARET}
 * 
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @todo write test 
 */
class FormatInvoiceAddress extends AbstractHelper
{
    public function __invoke(InvoiceAddressInterface $address)
    {
        return $this->render($address);
    }

    public function render(InvoiceAddressInterface $address)
    {
        $translate = $this->getView()->plugin('translate');
        $title = $address->getGender();
        $name = $address->getName();
        $fullname = ($title ? "$title " : '') . "<strong>$name</strong>";
        $company = $address->getCompany();
        $street = $address->getStreet();
        $city = $address->getZipCode() . ' ' . $address->getCity();
        $region = $address->getRegion();
        $country = $address->getCountry();
        $location = ($region ? "$region / " : '') . $country;
        $vatId = '<em>' . $translate('VAT ID') . '</em>: ' . $address->getVatIdNumber();
        $email = $address->getEmail();
        $email = $email ? '<i class="fa fa-envelope"></i> ' . $this->getView()->plugin('link')->__invoke($email) : '';


        $markup = "<address>$fullname<br>$company<br><br>$street<br>$city<br>$location<br><br>$email<br><br>$vatId</address>";

        return $markup;

    }
    
}