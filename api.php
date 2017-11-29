<?php 
$route = $_GET['route'];
switch ($route) {
	case 'image':
		$rep['text'] = "bonjour";
		echo json_encode($rep);
		break;
	
	default:
		echo '404';
		break;
}
?>