<?php
/**
 * YAWIK
 *
 * Configuration file of the MailService
 *
 * Copy this file into your autoload directory (without .dist) and adjust it for your needs
 *
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

$config = [
    'options' => [
        'Core/MailServiceOptions' => [
            'options' => [
                'transportClass' => 'file',
                'path' => realpath(__DIR__ . '/../../build/mails')
            ],
        ],
    ]
];
return $config;
