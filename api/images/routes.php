<?php 

include 'handlers.php'

$GLOBALS['routes'] = [
	"/images" => function() {
		return "Je créé une image";
	},
	"/images/download" => function() {
		return "Je download une image";
	}
];
?>