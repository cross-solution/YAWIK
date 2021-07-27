<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

namespace Core\View\Helper\Service;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Laminas\I18n\View\Helper\DateFormat;
use Locale;

/**
 * Hybridauth authentication adapter factory
 */
class DateFormatHelperFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $helper = new DateFormat();
        $helper->setLocale(Locale::DEFAULT_LOCALE);
        return $helper;
    }
}
