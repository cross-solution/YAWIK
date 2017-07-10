<?php

return array(
    'service_manager' => array(
        'factories' => array(
            'Html2PdfConverter' => [Pdf\Module::class,'factory'], //'Pdf\Module::factory',
        )
    ),
    'view_manager' => array(
        'template_map' => array(
            'pdf/application/details/button' => __DIR__ . '/../view/applicationDetailsButton.phtml',
        )
    ),
);
