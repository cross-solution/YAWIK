<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

namespace Core\Html2Pdf;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

class PdfServiceFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $Html2PdfConverter = $container->get('Html2PdfConverter');
        if (!$Html2PdfConverter instanceof PdfInterface) {
            throw new \DomainException(
                sprintf(
                    'PdfConverter %s does not implements PdfInterface',
                    get_class($Html2PdfConverter)
                )
            );
        }
        //$configArray = $serviceLocator->get('Config');
        
        $viewManager = $container->get('ViewManager');
        $view = $viewManager->getView();
        $viewEvents = $view->getEventManager();
        $Html2PdfConverter->attach($viewEvents);
        
        $application = $container->get('Application');
        $MvcEvents = $application->getEventManager();
        $Html2PdfConverter->attachMvc($MvcEvents);
        //$events->attach(ViewEvent::EVENT_RENDERER_POST, array($this, 'removeLayout'), 1);
        //$viewEvents->attach(ViewEvent::EVENT_RESPONSE, array($this, 'attachPDFtransformer'), 10);
        
        
        return $Html2PdfConverter;
    }
}
