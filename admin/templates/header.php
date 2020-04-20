<?php 

session_start();

// check if the current is admin, else redirect back to home
if (!isset($_SESSION['admin'])) {
	header('Location: /pharmacie');
	exit;
}

require_once('../config/db.php');

?>
<!doctype html>
<html lang="en">

<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<meta name="description" content="">

	<!-- Bootstrap core CSS -->
	<link href="css/bootstrap.min.css" rel="stylesheet"
		integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">

	<!-- Custom styles for this template -->
	<link rel="stylesheet" href="css/dataTables.bootstrap4.min.css">
	<link href="css/custom.css" rel="stylesheet">
</head>
