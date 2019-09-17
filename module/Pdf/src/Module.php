<?php
/**
 * YAWIK
 * Auth Module Bootstrap
 *
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

namespace Pdf;

use Zend\ServiceManager\ServiceManager;
use SplFileInfo;
use Zend\View\Resolver\ResolverInterface;
use Zend\View\Renderer\RendererInterface as Renderer;
use Zend\Mvc\MvcEvent;
use Zend\View\ViewEvent;
use Zend\EventManager\EventManagerInterface;
use Core\Html2Pdf\PdfInterface;
use Core\View\Helper\InsertFile\FileEvent;
use Core\Entity\FileEntity;
use Core\ModuleManager\Feature\VersionProviderInterface;
use Core\ModuleManager\Feature\VersionProviderTrait;
use Core\ModuleManager\ModuleConfigLoader;

/**
 * Make HTML to PDF
 *
 */
class Module implements PdfInterface, ResolverInterface, VersionProviderInterface
{
    use VersionProviderTrait;

    const VERSION = \Core\Module::VERSION;
    const RENDER_FULL = 0;
    const RENDER_WITHOUT_PDF = 1;
    const RENDER_WITHOUT_ATTACHMENTS = 2;

    protected $serviceManager;

    protected $viewResolverAttached = false;

    protected $appendPDF = array();
    protected $appendImage = array();


    /**
    * Loads module specific configuration.
    *
    * @return array
    */
    public function getConfig()
    {
        return ModuleConfigLoader::load(__DIR__ . '/../config');
    }

    public static function factory(ServiceManager $serviceManager)
    {
        $module = new static();
        $module->serviceManager = $serviceManager;
        return $module;
    }

    public function onBootstrap(MvcEvent $e)
    {
        $eventManager = $e->getApplication()->getEventManager();
        $eventManager->getSharedManager()->attach(
            'Applications',
            'application.detail.actionbuttons',
            function ($event) {
                return 'pdf/application/details/button';
            }
        );
    }

    /**
     * hook into the rendering for transformation of HTML to PDF
     * @param \Zend\EventManager\EventManagerInterface $events
     */
    public function attach(EventManagerInterface $events)
    {
        $events->attach(ViewEvent::EVENT_RENDERER_POST, array($this, 'cleanLayout'), 1);
        $events->attach(ViewEvent::EVENT_RESPONSE, array($this, 'attachPDFtransformer'), 10);
    }

    /**
     * hook into the MVC
     * in here you could still decide, if you want to hook into the Rendering
     * @param \Zend\EventManager\EventManagerInterface $events
     */
    public function attachMvc(EventManagerInterface $events)
    {
        $events->attach(MvcEvent::EVENT_RENDER, array($this, 'initializeViewHelper'), 100);
    }

    /**
     * hook into the Rendering of files
     * the manager to hook in is the viewhelper 'insertfiles'
     *
     * @param \Zend\Mvc\MvcEvent $e
     */
    public function initializeViewHelper(MvcEvent $e)
    {
        $viewhelperManager = $this->serviceManager->get('ViewHelperManager');
        if ($viewhelperManager->has('InsertFile')) {
            $insertFile = $viewhelperManager->get('InsertFile');
            $insertFile->attach(FileEvent::GETFILE, array($this, 'getFile'));
            $insertFile->attach(FileEvent::RENDERFILE, array($this, 'renderFile'));
            $insertFile->attach(FileEvent::INSERTFILE, array($this, 'collectFiles'));
        }
    }

    /**
     * proxy, in case that you just got a name and have to find the associated file-entity
     * maybe this is redundant and can be deprecated
     *
     * @param \Core\View\Helper\InsertFile\FileEvent $e
     * @return null
     */
    public function getFile(FileEvent $e)
    {
        $lastFileName = $e->getLastFileName();
        if (is_string($lastFileName)) {
            $repository = $this->serviceManager->get('repositories')->get('Applications/Attachment');
            $file       = $repository->find($lastFileName);
            if (isset($file)) {
                $e->setFileObject($lastFileName, $file);
                $e->stopPropagation();
                return $file;
            }
            return null;
        }
        // if it is not a string i do presume it is already a file-Object
        return $lastFileName;
    }

    /**
     * here the inserted File is rendered,
     * there is a lot which still can be done like outsorcing the HTML to a template,
     * or distinguish between different File Types,
     * at the moment we assume the $file is always an (sub-)instance of \Core\File\Entity
     *
     * @param \Core\View\Helper\InsertFile\FileEvent $e
     * @return string
     */
    public function renderFile(FileEvent $e)
    {
        $file = $e->getLastFileObject();
        // assume it is of the class Core\Entity\FileEntity
        $return = '<div class="col-md-3"><a href="#attachment_' . $file->getId() . '">' . $file->getName() . '</a></div>' . PHP_EOL
                . '<div class="col-md-3">' . $file->getType() . '</div>'
                . '<div class="col-md-3">' . $file->prettySize . '</div>';
        /*
         * this snippet was for direct inserting an image into the PDF
        if ($file && $file instanceOf FileEntity && 0 === strpos($file->getType(), 'image')) {
            //$content = $file->getContent();
            //$url = 'data:image/' . $file->getType() . ';base64,' . base64_encode ($content);
            //$html = '<img src="' . $url . '" >';
            $html = '<a href="#1">' . $file->getName() . '</a>';
            $e->stopPropagation();
            return $html;
        }
         */
        return $return;
    }

    /**
     * give a summary of all inserted Files,
     * this is for having access to those files in the post-process
     * @param \Core\View\Helper\InsertFile\FileEvent|\Zend\View\ViewEvent $e
     * @return NULL
     */
    public function collectFiles(FileEvent $e)
    {
        $this->appendPDF = array();
        $files = $e->getAllFiles();
        foreach ($files as $name => $file) {
            if (!empty($file) && $file instanceof FileEntity) {
                if (0 === strpos($file->getType(), 'image')) {
                    $this->appendImage[] = $file;
                }
                if (strtolower($file->getType()) == 'application/pdf') {
                    $this->appendPDF[] = $file;
                }
            }
        }
        return null;
    }

    /**
     * remove unwanted or layout related data
     *
     * basically you rake through the viewmodel for the data you want to use for your template,
     * this may not be optimal because you have to rely on the correct naming of the viewmodels
     *
     * if you get the data you want, you switch to the specific template by adding the conforming resolver
     *
     * @param \Zend\View\ViewEvent $e
     */
    public function cleanLayout(ViewEvent $e)
    {
        $result   = $e->getResult();
        $response = $e->getResponse();
        $model = $e->getModel();
        if ($model->hasChildren()) {
            $children = $model->getChildren();
            $content = null;
            foreach ($children as $child) {
                if ($child->captureTo() == 'content') {
                    $content = $child;
                    $this->attachViewResolver();
                }
            }
            if (!empty($content)) {
                $e->setModel($content);
            }
        } else {
            // attach the own resolver here too ?
            // ...
        }
    }

    /**
     * Attach an own ViewResolver
     */
    public function attachViewResolver()
    {
        if (!$this->viewResolverAttached) {
            $this->viewResolverAttached = true;
            $resolver = $this->serviceManager->get('ViewResolver');
            $resolver->attach($this, 100);
        }
    }

    /**
     * Transform the HTML to PDF,
     * this is a post-rendering-process
     *
     * put in here everything related to the transforming-process like options
     *
     * @param \Zend\View\ViewEvent $e
     */
    public function attachPDFtransformer(ViewEvent $e)
    {

        //$renderer = $e->getRenderer();
        $result   = $e->getResult();
        $response = $e->getResponse();

        // the handles are for temporary files
        error_reporting(0);
        foreach (array(self::RENDER_FULL, self::RENDER_WITHOUT_PDF, self::RENDER_WITHOUT_ATTACHMENTS ) as $render) {
            $handles = array();
            try {
                $pdf = new extern\mPDFderive();
                $pdf->SetImportUse();
                // create bookmark list in Acrobat Reader
                $pdf->h2bookmarks = array('H1' => 0, 'H2' => 1, 'H3' => 2);
                $pdf->WriteHTML($result);

                // Output of the Images
                if (self::RENDER_FULL == $render || self::RENDER_WITHOUT_PDF == $render) {
                    if (is_array($this->appendImage) && !empty($this->appendImage)) {
                        foreach ($this->appendImage as $imageAttachment) {
                            $content = $imageAttachment->getContent();
                            $url = 'data:image/' . $imageAttachment->getType() . ';base64,' . base64_encode($content);
                            $html = '<a name="attachment_' . $imageAttachment->getId() . '"><img src="' . $url . '" /><br /></a>';
                            $pdf->WriteHTML($html);
                        }
                    }
                }

                // Temp Files PDF
                if (self::RENDER_FULL == $render) {
                    if (is_array($this->appendPDF) && !empty($this->appendPDF)) {
                        foreach ($this->appendPDF as $pdfAttachment) {
                            $content = $pdfAttachment->getContent();
                            $tmpHandle = tmpfile();
                            $handles[] = $tmpHandle;
                            fwrite($tmpHandle, $content);
                            fseek($tmpHandle, 0);
                        }
                    }
                }

                // Output of the PDF
                foreach ($handles as $handle) {
                    $meta_data = stream_get_meta_data($handle);
                    $filename = $meta_data["uri"];
                    $pdf->WriteHTML($filename);
                    $pagecount = $pdf->SetSourceFile($filename);
                    for ($pages = 0; $pages < $pagecount; $pages++) {
                        $pdf->AddPage();
                        $pdf->WriteHTML(' pages: ' . $pagecount);
                        $tx = $pdf->ImportPage($pages + 1);
                        $pdf->UseTemplate($tx);
                    }
                }

                $pdf_result = $pdf->Output();
                $e->setResult($pdf_result);

                // delete all temporary Files again
                foreach ($handles as $handle) {
                    fclose($handle);
                }
                break;
            } catch (\Exception $e) {
            }
        }
        error_reporting(E_ALL);
    }

    /**
     * Look for a template with the Suffix ".pdf.phtml"
     *
     * @param string $name
     * @param \Zend\View\Renderer\RendererInterface $renderer
     * @return string|boolean
     */
    public function resolve($name, Renderer $renderer = null)
    {
        if ($this->serviceManager->has('ViewTemplatePathStack')) {
            // get all the Pases made up for the zend-provided resolver
            // we won't get any closer to ALL than that
            $viewTemplatePathStack = $this->serviceManager->get('ViewTemplatePathStack');
            $paths = $viewTemplatePathStack->getPaths();
            $defaultSuffix = $viewTemplatePathStack->getDefaultSuffix();
            if (pathinfo($name, PATHINFO_EXTENSION) != $defaultSuffix) {
                ;
                $name .= '.pdf.' . $defaultSuffix;
            } else {
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
