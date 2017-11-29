<?php 

include 'handlers.php';

$GLOBALS['routes'] = [
	"/images" => $handle_bonjour,
	"/images/download" => function() {
		return "Je download une image";
	}
];
?>