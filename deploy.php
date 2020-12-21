<?php
namespace Deployer;

require 'recipe/zend_framework.php';
require 'recipe/cachetool.php';


// Project name
set('application', 'YawikDemo');

// Project repository
set('repository', 'git@gitlab.com:yawik/yawik.git');

// Shared files/dirs between deploys
add('shared_files', [
    'test/sandbox/public/.htaccess',
    'test/sandbox/public/pro-sitemaps-4112318.php'
]);
add('shared_dirs', [
    'test/sandbox/var/log',
    'test/sandbox/var/cache',
    'test/sandbox/config/autoload',
    'test/sandbox/public/static',
    'test/sandbox/public/lg'
]);

// Writable dirs by web server
add('writable_dirs', [
    'test/sandbox/var/cache',
    'test/sandbox/var/log',
    'test/sandbox/public/static',
    'test/sandbox/public/lg'
]);

set('default_stage', 'prod');

host('staging.yawik.org')
    ->user('yawik')
    ->stage('staging')
    ->multiplexing(false)
    ->set('deploy_path', '/var/www/staging');

after('deploy:symlink', 'cachetool:clear:opcache');

// [Optional] if deploy fails automatically unlock.
after('deploy:failed', 'deploy:unlock');

