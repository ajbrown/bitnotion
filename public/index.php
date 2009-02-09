<?php
/**
 * My new Zend Framework Project
 *
 * @author
 * @version
 */
require_once '../application/bootstrap.php';

// Prepare the front controller.
$frontController = Zend_Controller_Front::getInstance();

// Change to 'production' parameter under production environemtn
$frontController->registerPlugin( new Initializer('dev') );

//register_shutdown_function( 'debugQueries' );

// Dispatch the request using the front controller.
$frontController->dispatch();


function debugQueries() {
$db = Zend_Registry::get( 'DbConn' );
$profiler = $db->getProfiler();
$profile = '';
$queries = $profiler->getQueryProfiles();
echo '<table>';
if ( is_array( $queries ) ) foreach( $queries as $query) {
	$profile .= '<tr><td>' . $query->getElapsedSecs() * 1000 . '</td>'
		. '<td>' . $query->getQuery() . "</td></tr>\n";
}
echo $profile;
echo '</table>';

}
