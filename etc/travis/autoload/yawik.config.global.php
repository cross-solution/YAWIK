<?php

return array(
    'doctrine' =>
        array(
            'connection' =>
                array(
                    'odm_default' =>
                        array(
                            'connectionString' => 'mongodb://172.22.1.151:27017/YAWIK',
                        ),
                ),
            'configuration' =>
                array(
                    'odm_default' =>
                        array(
                            'default_db' => 'YAWIK',
                        ),
                ),
        ),
    'core_options' =>
        array(
            'system_message_email' => 'test.yawik@gmail.com',
        ),
);
