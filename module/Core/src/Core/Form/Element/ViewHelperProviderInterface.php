<?php

namespace Core\Form\Element;

interface ViewHelperProviderInterface
{
    /**
     * Gets the view helper instance or service name.
     *
     * @return \Zend\View\Helper\HelperInterface|string
     */
    public function getViewHelper();

    /**
     * Sets the view helper instance or service name.
     *
     * @param \Zend\View\Helper\HelperInterface|string $helper
     *
     * @return self
     * @throws \InvalidArgumentException if $helper is neiter a string nor a HelperInterface.
     */
    public function setViewHelper($helper);
}
