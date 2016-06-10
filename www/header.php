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
		<link href="css/bootstrap.css" rel="stylesheet">
		<link href="css/bootstrap-theme.css" rel="stylesheet">
		<link href="css/sticky-footer.css" rel="stylesheet">
		<link href="/css/font-awesome.css" rel="stylesheet" type="text/css">
		<link href="/css/custom-font-icons.css" rel="stylesheet" type="text/css">
		<link href="css/bootstrap-slider.css" rel="stylesheet">
		<link href="css/blog.css" rel="stylesheet">
		<link href="css/dashboard.css" rel="stylesheet">
		<link href="css/clashtracker.css" rel="stylesheet">
		<script src="js/jquery-1.11.3.min.js"></script>
		<script src="js/bootstrap.js"></script>
		<script src="js/Chart.js"></script>
		<script src="js/Chart.Scatter.js"></script>
		<script src="js/bootstrap-slider.js"></script>
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
					<a class="navbar-brand" href="/home.php">Clash&nbsp;Tracker</a>
				</div>
				<div id="navbar" class="navbar-collapse collapse">
					<ul class="nav navbar-nav navbar-right">
						<?if(!isset($loggedInUser)){?>
							<li><a href="/players.php">Players</a></li>
							<li><a href="/clans.php">Clans</a></li>
							<li><a href="/login.php">Log In</a></li>
						<?}else{
							if($loggedInUser->get('email') == 'alexinmann@gmail.com'){?>
								<li><a href="/dev.php">Dev</a></li>
							<?}if(isset($loggedInUserPlayer)){?>
								<li><a href="/player.php?playerId=<?=$loggedInUserPlayer->get('id');?>">My Player</a></li>
							<?}else{?>
								<li><a href="/players.php">Players</a></li>
							<?}if(isset($loggedInUserClan)){?>
								<li><a href="/clan.php?clanId=<?=$loggedInUserClan->get('id');?>">My Clan</a></li>
							<?}else{
								if(isset($loggedInUserPlayer)){
									$loggedInUserPlayerClan = $loggedInUserPlayer->getClan();
									if(isset($loggedInUserPlayerClan)){?>
										<li><a href="/clan.php?clanId=<?=$loggedInUserPlayerClan->get('id');?>">My Clan</a></li>
									<?}else{?>
										<li><a href="/clans.php">Clans</a></li>
									<?}
								}else{?>
									<li><a href="/clans.php">Clans</a></li>
								<?}
							}?>
							<li><a href="/accountSettings.php">Settings</a></li>
							<li><a href="/processLogout.php">Log Out</a></li>
						<?}?>
					</ul>
					<form class="navbar-form navbar-right" action="searchResults.php" method="GET">
						<input type="text" name="query" class="form-control" placeholder="Search...">
					</form>
				</div>
			</div>
		</nav>
		<br>
		<div class="container-fluid">