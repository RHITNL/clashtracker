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
	<ol class="breadcrumb">
		<li><a href="/home.php">Home</a></li>
		<li class="active">Account Settings</li>
	</ol>
	<?require('showMessages.php');?>
	<h1>Account Settings</h1><br>
	<div class="col-md-3 sidebar">
		<ul class="nav nav-sidebar">
			<li id="generalTab"><a href="?tab=general">General</a></li>
			<li id="passwordTab"><a href="?tab=password">Password</a></li>
			<li id="playerTab"><a href="?tab=player">Player</a></li>
		</ul>
	</div>
	<div class="col-md-9 main">
		<div id="<?$tab?>Main">
			<?require($tab . 'Settings.php');?>
		</div>
	</div>
</div>
<?
require('footer.php');
