<?
require('init.php');
require('session.php');
if(!isset($loggedInUser)){
	$_SESSION['curError'] = NO_ACCESS;
	header('Location: /home.php');
	exit;
}

$playerTag = $_GET['playerTag'];
if(strlen($playerTag)==0){
	$_SESSION['curError'] = 'Player tag cannot be blank.';
	header('Location: /accountSettings.php?tab=player');
	exit;
}

try{
	$loggedInUser->linkWithPlayer($playerTag);
}catch(noResultFoundException $e){
	//ignore, that's what this page is for
}catch(Exception $e){
	$_SESSION['curError'] = $e->getMessage();
	header('Location: /accountSettings.php?tab=player');
	exit;
}

require('header.php');
?>
<div class="col-md-12">
	<ol class="breadcrumb">
		<li><a href="/home.php">Home</a></li>
		<li><a href="/accountSettings.php?tab=player">Account Settings</a></li>
		<li class="active">Link Player</li>
	</ol>
	<?require('showMessages.php');?>
	<h1>Link Player</h1><br>
	<div class="alert alert-info" role="alert">
		<strong>On no!</strong> We don't have any records for a player with that Player Tag. To add one, enter its name below.
	</div>
	<div class="">
		<form class="form-horizontal" action="/processLinkPlayer.php" method="POST">
			<div class="col-sm-6">
				<div class="form-group">
					<label class="col-sm-4 control-lable" for="name">Player Name:</label>
					<div class="col-sm-8">
						<input type="text" class="form-control" id="name" name="name" placeholder="Angry Neeson 52">
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-4 control-lable">Player Tag:</label>
					<div class="col-sm-8">
						<input disabled type="text" class="form-control" placeholder="#JKFH83J" value="<?=$playerTag;?>">
					</div>
				</div>
				<input type="text" class="form-control hidden" id="playerTag" name="playerTag" value="<?=$playerTag;?>">
			</div>
			<div class="row">
				<div class="col-sm-12 text-right btn-actions">
					<br>
					<button type="submit" class="btn btn-default" name="cancel" value="cancel">Cancel</button>
					<button type="submit" class="btn btn-success" name="submit" value="submit">Link</button>
				</div>
			</div>
		</form>
	</div>
</div>
<?
require('footer.php');