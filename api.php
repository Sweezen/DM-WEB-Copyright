<?php 
$route = $_GET['route'];

require 'api/images/routes.php';

handle_route($route);

function handle_route($route) {
	$resp = run_handler($route);

	echo json_encode($resp);
}

function run_handler($route) {
	if(! isset($GLOBALS['routes'][$route])) {
		return 404;
	}
	$rep = $GLOBALS['routes'][$route]();

	if (is_null($rep)) $rep['status'] = 500; //server internal error
	return $rep;
}
?>