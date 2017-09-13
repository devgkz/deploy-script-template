<?php
/**
 * ***************
 * Copyright (c) 2017 Eugene Dementyev.
 * BSD 3-clause License (https://opensource.org/licenses/BSD-3-Clause).
 * ***************
 */

namespace Deployer;

require 'recipe/common.php';


// Project name
set('application', 'Your-App-Name');

// Project repository
set('branch', 'master');

set('default_stage', 'production');

// Number of releases to be stored
set('keep_releases', 4);

set('writable_mode', 'chmod');
set('writable_chmod_mode', '0755');


// [Optional] Allocate tty for git clone. Default value is false.
set('git_tty', false);           // false on windows
set('ssh_multiplexing', false);  // false on windows

//set('ssh_type', 'native');

// Shared files/dirs between deploys
set('shared_files', [
    'config/autoload/database.local.php'
]);

set('shared_dirs', [
    'public/files',
    'public/resized',
]);

// Writable dirs by web server
set('writable_dirs', [
    'public/files',
    'public/resized',
]);

// Restart PHP-FPM to clean file descriptors cache. Check your command
task('restart-fpm', 'service php7.1-fpm reload');
after('cleanup', 'restart-fpm');

// -----
// # Uncomment these lines if you want use Slack Webhook notification messages
// require 'slack.php';
// set('slack_webhook', 'https://hooks.slack.com/services/Your-Webhook-Link');
// ## Custom Slack messages
// set('slack_text', 'Начато развертывание ветки `{{branch}}` на сервер *{{target}}* ()');
// set('slack_success_text', 'Развертывание прошло *успешно*!');
// before('deploy', 'slack:notify');
// after('success', 'slack:notify:success');
// -----
// # DB-migration scripts. Now is robmorgan/phinx
// task('migration', function () {
    // cd('{{release_path}}');
    // run('vendor/bin/phinx migrate');
// });
// after('deploy:vendors', 'migration');
// -----

// Hosts
localhost()
    ->stage('production')
    ->set('repository', 'Link-To-Git-Repository')
    ->set('branch', 'release')
    ->set('deploy_path', __DIR__);
    
localhost()
    ->stage('staging')
    ->set('repository', 'Link-To-Git-Repository')
    ->set('deploy_path', __DIR__);

host('bit55.ru')
    ->stage('remote')
    ->user('root')
    ->port(22)
    ->identityFile('.ssh/id_rsa')
    ->set('repository', 'Link-To-Git-Repository')
    ->set('deploy_path', '/path/to/project/dir');
    
// [Optional] If deploy fails automatically unlock.
after('deploy:failed', 'deploy:unlock');
    
// Tasks pipeline
desc('Project deployment started.');

task('deploy', [
    'deploy:info',
    'deploy:prepare',
    'deploy:lock',
    'deploy:release',
    'deploy:update_code',
    'deploy:shared',
    'deploy:writable',
    'deploy:vendors',
    'deploy:clear_paths',
    'deploy:symlink',
    'deploy:unlock',
    'cleanup',
    'success'
]);
