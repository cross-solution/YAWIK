<?php
/**
 * Cross Applicant Management
 * Auth Module Bootstrap
 *
 * @copyright (c) 2013 Cross Solution (http://cross-solution.de)
 * @license   GPLv3
 */

namespace Pdf;

use Zend\Mvc\MvcEvent;
use Zend\View\ViewEvent;
use Zend\EventManager\EventManagerInterface;
use Core\Html2Pdf\PdfInterface;

/**
 * Make HTML to PDF
 * 
 */
class Module implements PdfInterface
{
    public function getConfig() {
        return array(
            'service_manager' => array(
                'invokables' => array(
                    'Html2PdfConverter' => __NAMESPACE__ . '\Module',
                )
            )
        );
    }   
    
    public function attach(EventManagerInterface $events) {
        $events->attach(ViewEvent::EVENT_RENDERER_POST, array($this, 'cleanLayout'), 1);
        $events->attach(ViewEvent::EVENT_RESPONSE, array($this, 'attachPDFtransformer'), 10);
    }
    
    /**
     * remove Layout related Data
     * @param \Zend\View\ViewEvent $e
     */
    public function cleanLayout(ViewEvent $e) {
        $result   = $e->getResult();
        $response = $e->getResponse();
        $model = $e->getModel();
        if ($model->hasChildren()) {
            $children = $model->getChildren();
            $content = Null;
            foreach ($children as $child) {
                if ($child->captureTo() == 'content') {
                    $content = $child;
                }
            }
            if (!empty($content)) {
                $e->setModel($content);
            }
        }
    }
    
    public function attachPDFtransformer(ViewEvent $e) {
        //$renderer = $e->getRenderer();

        $result   = $e->getResult();
        $response = $e->getResponse();
        
        //error_reporting(0);
        try {
            $pdf = new \mPDF();
            $pdf->WriteHTML($result);
            $result = $pdf->Output();
        } catch (Exception $e) {
        }
        //error_reporting(E_ALL);
        
        $e->setResult($result);
    }
    
}