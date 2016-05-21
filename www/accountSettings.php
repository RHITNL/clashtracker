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
<div id="wrapper" class="col-md-12">
	<div id="sidebar-wrapper">
		<div id="sidebar">
			<ul class="nav nav-sidebar">
				<li class="<?=$tab == 'general' ? 'active' : '';?>"><a href="?tab=general">General</a></li>
				<li class="<?=$tab == 'password' ? 'active' : '';?>"><a href="?tab=password">Password</a></li>
				<li class="<?=$tab == 'player' ? 'active' : '';?>"><a href="?tab=player">Player</a></li>
				<li class="<?=$tab == 'clan' ? 'active' : '';?>"><a href="?tab=clan">Clan</a></li>
			</ul>
		</div>
	</div>
	<div id="page-content-wrapper" class="main">
		<ol class="breadcrumb">
			<li><a href="/home.php">Home</a></li>
			<li class="active">Account Settings</li>
		</ol>
		<?require('showMessages.php');?>
		<h1>Account Settings</h1>
		<div class="hidden-sm hidden-md hidden-lg">
			<a href="#menu-toggle" class="btn btn-default" id="menu-toggle">More Settings</a>
		</div>
		<?require($tab . 'Settings.php');?>
	</div>
</div>
<script>
	$("#menu-toggle").click(function(e) {
		e.preventDefault();
		$("#wrapper").toggleClass("toggled");
	});
</script>
<?
// require('footer.php');
