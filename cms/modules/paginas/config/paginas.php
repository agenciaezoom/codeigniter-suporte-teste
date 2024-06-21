<?php  (defined('BASEPATH')) or exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| Load Config to Module
|--------------------------------------------------------------------------
|
*/
$config['version'] = '0.1.1';

/*
|--------------------------------------------------------------------------
| Enable/Disable Migrations
|--------------------------------------------------------------------------
|
| Migrations are disabled by default but should be enabled
| whenever you intend to do a schema migration.
|
*/
$config['migration_enabled'] = true;

/*
|--------------------------------------------------------------------------
| Migrations version
|--------------------------------------------------------------------------
|
| This is used to set migration version that the file system should be on.
| If you run $this->migration->latest() this is the version that schema will
| be upgraded / downgraded to.
|
*/
$config['migration_version'] = 1;
