<?php

return array(
    'service_manager' => array(
        'invokables' => array(
            'Html2PdfConverter' => 'Pdf\Module',
        )
    ),
    'view_manager' => array(
        'template_map' => array(
            'pdf/application/details/button' => __DIR__ . '/../view/applicationDetailsButton.phtml',
        )
    ),
);
