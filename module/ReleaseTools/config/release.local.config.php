<?php

return [
    'release' => [
        /*
         * Change this value to match with your remote
         * configuration that pointing to: git@github.com:cross-solution/YAWIK.git
         * Check this value by using: git remote -v
         */
        'main_remote_name' => 'origin',

        /*
         * If you don't want to keep git clone for yawik/* repository
         * in your local directories just leave this value commented.
         */
        //'subsplit_clone_dir' => sys_get_temp_dir().'/yawik/build',
    ],
];
