<?
require('init.php');
require('session.php');
if(!isset($loggedInUser)){
	$_SESSION['curError'] = NO_ACCESS;
	header('Location: /home.php');
	exit;
}
if(isset($_GET['tab'])){
	$tab = $_GET['tab'];
}else{
	$tab = 'general';
}
require('header.php');
?>
<div class="col-md-12">
	<div class="col-sm-3 col-md-2 sidebar">
		<ul class="nav nav-sidebar">
			<li class="<?=$tab == 'general' ? 'active' : '';?>"><a href="?tab=general">General</a></li>
			<li class="<?=$tab == 'password' ? 'active' : '';?>"><a href="?tab=password">Password</a></li>
			<li class="<?=$tab == 'player' ? 'active' : '';?>"><a href="?tab=player">Player</a></li>
			<li class="<?=$tab == 'clan' ? 'active' : '';?>"><a href="?tab=clan">Clan</a></li>
		</ul>
	</div>
	<div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">
		<?require('showMessages.php');?>
		<ol class="breadcrumb">
			<li><a href="/home.php">Home</a></li>
			<li class="active">Account Settings</li>
		</ol>
		<h1>Account Settings</h1><br>
		<?require($tab . 'Settings.php');?>
	</div>
</div>
<?
require('footer.php');
