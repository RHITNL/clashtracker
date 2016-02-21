<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<meta name="description" content="">
		<meta name="author" content="">
		<link rel="icon" href="images/clash.png">
		<title>Clash Tracker</title>
		<link href="css/bootstrap.min.css" rel="stylesheet">
		<link href="css/bootstrap-theme.min.css" rel="stylesheet">
		<link href="css/sticky-footer.css" rel="stylesheet">
		<link rel="stylesheet" type="text/css" href="/css/font-awesome.css">
		<link rel="stylesheet" type="text/css" href="/css/custom-font-icons.css"></link>
		<script src="js/jquery-1.11.3.min.js"></script>
		<script src="js/bootstrap.min.js"></script>
		<script src="js/Chart.js"></script>
		<script src="js/Chart.Scatter.js"></script>
	</head>
	<body role="document">
		<nav class="navbar navbar-inverse navbar-fixed-top">
			<div class="container-fluid">
				<div class="navbar-header">
					<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
						<span class="sr-only">Toggle navigation</span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
					</button>
					<a class="navbar-brand" href="/home.php">
						<img alt="Brand" src="images/clash.png" height="20" width="20">
					</a>
					<a class="navbar-brand" href="/home.php">Clash Tracker</a>
				</div>
				<div id="navbar" class="navbar-collapse collapse">
					<ul class="nav navbar-nav navbar-right">
						<!-- <li><a href="/home.php">Home</a></li> -->
						<li><a href="/players.php">Players</a></li>
						<li><a href="/clans.php">Clans</a></li>
						<?if(!isset($loggedInUser)){?>
							<li><a href="/login.php">Log In</a></li>
						<?}else{?>
							<li><a href="/accountSettings.php">Settings</a></li>
							<li><a href="/processLogout.php">Log Out</a></li>
						<?}?>
						<!-- <li><a href="/wars.php">Wars</a></li> -->
					</ul>
					<form class="navbar-form navbar-right" action="searchResults.php" method="GET">
						<input type="text" name="query" class="form-control" placeholder="Search...">
					</form>
				</div>
			</div>
		</nav>
		<br><br><br><br>
		<div class="container-fluid">