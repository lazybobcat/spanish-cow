<?php
namespace Deployer;

// All Deployer recipes are based on `recipe/common.php`.
require 'recipe/symfony4.php';
set('ssh_type', 'native');
set('deploy_assets', true);
set('fpm_command', null);
set('writable_use_sudo', false);
set('keep_releases', 2);
set('supervisor_command', null);

inventory(dirname(__FILE__) . '/servers.yml');

// Specify the repository from which to download your project's code.
// The server needs to have git installed for this to work.
// If you're not using a forward agent, then the server has to be able to clone
// your project from this repository.
set('repository', 'git@gitlab.nvision.lu:nvision/spanish-cow.git');

// Add web/uploads to shared_dirs
set('shared_files', array_merge(get('shared_files'), [
    'config/parameters.yaml',
]));

// Add web/uploads to writable_dirs
set('writable_dirs', array_merge(get('writable_dirs'), [
    'web/uploads',
]));

// Copy built assets to release
task('deploy:assets', function () {
    if (get('deploy_assets')) {
        upload('public/build', '{{release_path}}/public');
    }
});
after('deploy:update_code', 'deploy:assets');

// Copy vendors from previous release if any
set('copy_dirs', ['vendor']);
before('deploy:vendors', 'deploy:copy_dirs');

// automatic database migration
after('deploy:vendors', 'database:migrate');


task('reload:php-fpm', function() {
    if (null !== ($fpm = get('fpm_command', null))) {
        run($fpm);
    }
});
after('deploy:symlink', 'reload:php-fpm');

task('reload:supervisor', function() {
    if (null !== ($supervisor = get('supervisor_command', null))) {
        run($supervisor);
    }
});
after('reload:php-fpm', 'reload:supervisor');
