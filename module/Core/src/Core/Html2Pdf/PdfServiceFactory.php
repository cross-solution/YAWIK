<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013-2015 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

namespace Core\Html2Pdf;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class PdfServiceFactory implements FactoryInterface
{
     
    /* (non-PHPdoc)
     * @see \Zend\ServiceManager\FactoryInterface::createService()
     */
    public function createService(ServiceLocatorInterface $serviceLocator) {
        
        $Html2PdfConverter = $serviceLocator->get('Html2PdfConverter');
        if (!$Html2PdfConverter instanceof PdfInterface) {
            throw new \DomainException(sprintf(
                'PdfConverter %s does not implements PdfInterface',
                get_class($Html2PdfConverter)
            ));
        }
        //$configArray = $serviceLocator->get('Config');
        
        $viewManager = $serviceLocator->get('ViewManager');
        $view = $viewManager->getView();
        $viewEvents = $view->getEventManager();
        $Html2PdfConverter->attach($viewEvents);
        
        $application = $serviceLocator->get('Application');
        $MvcEvents = $application->getEventManager();
        $Html2PdfConverter->attachMvc($MvcEvents);
        //$events->attach(ViewEvent::EVENT_RENDERER_POST, array($this, 'removeLayout'), 1);
        //$viewEvents->attach(ViewEvent::EVENT_RESPONSE, array($this, 'attachPDFtransformer'), 10);

        
        return $Html2PdfConverter;
        
    }

}

