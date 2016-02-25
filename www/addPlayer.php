<?
require('init.php');
require('session.php');

$clanId = $_GET['clanId'];
try{
	$clan = new clan($clanId);
	$clanId = $clan->get('id');
	if(!userHasAccessToUpdateClan($clan)){
		$_SESSION['curError'] = NO_ACCESS;
		header('Location: /clan.php?clanId=' . $clanId);
		exit;
	}
}catch(Exception $e){
	$clan=null;
}

require('header.php');
?>
<div class="col-md-12">
	<ol class="breadcrumb">
		<li><a href="/home.php">Home</a></li>
		<?if(isset($clan)){?>
			<li><a href="/clans.php">Clans</a></li>
			<li><a href="/clan.php?clanId=<?=$clan->get('id');?>"><?=htmlspecialchars($clan->get('name'));?></a></li>
			<li class="active">Add Member</li>
		<?}else{?>
			<li><a href="/players.php">Players</a></li>
			<li class="active">Add Player</li>
		<?}?>
	</ol>
	<?require('showMessages.php');?>
	<?if(isset($clan)){?>
		<h1>Add Member</h1><br>
	<?}else{?>
		<h1>Add Player</h1><br>
	<?}?>
	<div class="">
		<form class="form-horizontal" action="/processAddPlayer.php" method="POST">
			<div class="col-sm-6">
				<div class="form-group">
					<label class="col-sm-4 control-lable" for="name">Player Name:</label>
					<div class="col-sm-8">
						<input type="text" class="form-control" id="name" name="name" placeholder="Angry Neeson 52" value="<?=$_SESSION['name'];?>"></input>
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-4 control-lable" for="playerTag">Player Tag:</label>
					<div class="col-sm-8">
						<input type="text" class="form-control" id="playerTag" name="playerTag" placeholder="#JKFH83J" value="<?=$_SESSION['playerTag'];?>"></input>
					</div>
				</div>
			</div>
			<div class="col-sm-6">
				<?if(!isset($loggedInUserPlayer) && isset($loggedInUser)){?>
					<div class="form-group">
						<label class="col-sm-4 control-lable" for="link">Link to Account?</label>
						<div class="col-sm-8">
							<input id="link" name="link" value="true" class="stars" type="checkbox">
						</div>
					</div>
				<?}if(isset($clan)){?>
					<input hidden id="clanId" name="clanId" value="<?=$clan->get('id');?>"></input>
				<?}?>
			</div>
			<div class="row">
				<div class="col-sm-12 text-right btn-actions">
					<br>
					<button type="submit" class="btn btn-default" name="cancel" value="cancel">Cancel</button>
					<button type="submit" class="btn btn-success" name="submit" value="submit">Submit</button>
				</div>
			</div>
		</form>
	</div>
</div>
<?
require('footer.php');