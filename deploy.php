<?php
namespace Deployer;

require 'recipe/zend_framework.php';

// Project name
set('application', 'Yawik');

// Project repository
set('repository', 'git@gitlab.com:yawik/yawik.git');

// Shared files/dirs between deploys
add('shared_files', [
    'public/.htaccess',
]);
add('shared_dirs', [
    'var/log',
    'var/cache',
    'config/autoload',
    'public/static'
]);

// Writable dirs by web server
add('writable_dirs', [
    'var/cache',
    'var/log',
    'public/static'
]);

set('default_stage', 'staging');

host('staging.yawik.org')
    ->user('yawik')
    ->stage('staging')
    ->multiplexing(false)
    ->set('deploy_path', '/var/www/staging');

#after('deploy:symlink', 'cachetool:clear:opcache');

// [Optional] if deploy fails automatically unlock.
after('deploy:failed', 'deploy:unlock');

