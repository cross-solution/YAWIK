<?php
/**
 * Cross Applicant Management
 * Auth Module Bootstrap
 *
 * @copyright (c) 2013 Cross Solution (http://cross-solution.de)
 * @license   GPLv3
 */

namespace Pdf;

use Zend\ServiceManager\ServiceManagerAwareInterface;
use Zend\ServiceManager\ServiceManager;
use SplFileInfo;
use Zend\View\Resolver\ResolverInterface;
use Zend\View\Renderer\RendererInterface as Renderer;
use Zend\Mvc\MvcEvent;
use Zend\View\ViewEvent;
use Zend\EventManager\EventManagerInterface;
use Core\Html2Pdf\PdfInterface;

/**
 * Make HTML to PDF
 * 
 */
class Module implements PdfInterface, ResolverInterface, ServiceManagerAwareInterface
{
    protected $serviceManager;
    
    protected $viewResolverAttached = False;
    
    public function getConfig() {
        return array(
            'service_manager' => array(
                'invokables' => array(
                    'Html2PdfConverter' => __NAMESPACE__ . '\Module',
                )
            )
        );
    }   
    
    public function setServiceManager(ServiceManager $serviceManager) {
        $this->serviceManager = $serviceManager;
        return $this;
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
                    $this->attachViewResolver();
                }
            }
            if (!empty($content)) {
                $e->setModel($content);
            }
        }
        else {
            // TODO: attach the own resolver here too ?
        }
    }
    
    public function attachViewResolver() {
        if (!$this->viewResolverAttached) {
            $this->viewResolverAttached = True;
            $resolver = $this->serviceManager->get('ViewResolver');
            $resolver->attach($this,100);
        }
    }
    
    public function attachPDFtransformer(ViewEvent $e) {
        //$renderer = $e->getRenderer();

        $result   = $e->getResult();
        $response = $e->getResponse();
        
        //error_reporting(0);
        try {
            $pdf = new \mPDF();
            
            // create bookmark list in Acrobat Reader
            $pdf->h2bookmarks = array('H1'=>0, 'H2'=>1, 'H3'=>2);
            
            $pdf->WriteHTML($result);
            $result = $pdf->Output();
        } catch (Exception $e) {
        }
        //error_reporting(E_ALL);
        
        $e->setResult($result);
    }
    
    public function resolve($name, Renderer $renderer = null) {
        if ($this->serviceManager->has('ViewTemplatePathStack')) {
            // get all the Pases made up for the zend-provided resolver
            // we won't get any closer to ALL than that
            $viewTemplatePathStack = $this->serviceManager->get('ViewTemplatePathStack');
            $paths = $viewTemplatePathStack->getPaths();
            $defaultSuffix = $viewTemplatePathStack->getDefaultSuffix();
            if (pathinfo($name, PATHINFO_EXTENSION) != $defaultSuffix) {;
                $name .= '.pdf.' . $defaultSuffix;
            }
            else {
                // TODO: replace Filename by Filename for PDF
            }

            foreach ($paths as $path) {
                $file = new SplFileInfo($path . $name);
                if ($file->isReadable()) {
                    // Found! Return it.
                    if (($filePath = $file->getRealPath()) === false && substr($path, 0, 7) === 'phar://') {
                        // Do not try to expand phar paths (realpath + phars == fail)
                        $filePath = $path . $name;
                        if (!file_exists($filePath)) {
                            break;
                        }
                    }
                    //if ($this->useStreamWrapper()) {
                    //    // If using a stream wrapper, prepend the spec to the path
                    //    $filePath = 'zend.view://' . $filePath;
                    //}
                    return $filePath;
                }
            }
        }
        // TODO: Resolving to an PDF has failed, this could have implications for the transformer
        return false;
    }
    
}