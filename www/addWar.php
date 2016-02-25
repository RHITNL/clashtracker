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
	$_SESSION['curError'] = 'No clan with id ' . $clanId . ' found.';
	header('Location: /clans.php');
	exit;
}

require('header.php');
?>
<div class="col-md-12">
	<ol class="breadcrumb">
		<li><a href="/home.php">Home</a></li>
		<li><a href="/clans.php">Clans</a></li>
		<li><a href="/clan.php?clanId=<?=$clan->get('id');?>"><?=htmlspecialchars($clan->get('name'));?></a></li>
		<li class="active">Add War</li>
	</ol>
	<?require('showMessages.php');?>
	<h1>Add War</h1><br>
	<div class="">
		<form class="form-horizontal" action="/processAddWar.php" method="POST">
			<div class="col-sm-6">
				<div class="form-group">
					<label class="col-sm-4 control-lable" for="enemyClanTag">Enemy Clan Tag:</label>
					<div class="col-sm-8">
						<input type="text" class="form-control" id="enemyClanTag" name="enemyClanTag" placeholder="#D73HZFU" value="<?=$_SESSION['enemyClanTag'];?>"></input>
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-4 control-lable" for="description">War Size:</label>
					<div class="col-sm-8">
						<select class="form-control" id="size" name="size">
							<?for ($i=10; $i <= 50; $i+=5){?>
								<option <?=($_SESSION['size'] == $i) ? 'selected' : '';?> value="<?=$i;?>"><?=$i;?></option>
							<?}?>
						</select>
					</div>
				</div>
			</div>
			<input hidden name="clanId" value="<?=$clan->get('id');?>"></input>
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