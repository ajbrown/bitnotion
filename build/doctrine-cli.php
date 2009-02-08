#!/usr/bin/env php
<?php

require_once '../application/bootstrap.php';

set_include_path( '../models/generated' . PATH_SEPARATOR . get_include_path() );

$init = new Initializer( 'build' );
$init->initDb();

$basePath = dirname( dirname( __FILE__ ) );
$cli = new Doctrine_Cli( array (
	'data_fixtures_path' => $basePath . '/build/data/fixtures',
	'models_path' => $basePath . '/models',
	'migrations_path' => $basePath . '/build/data/migrations',
	'sql_path' => $basePath . '/build/data/sql',
	'yaml_schema_path' => $basePath . '/build/data/schema.yml'
) );
$cli->run( $_SERVER[ 'argv' ] );