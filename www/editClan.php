<?
require(__DIR__ . '/../config/functions.php');

$clanId = $_GET['clanId'];
try{
	$clan = new clan($clanId);
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
		<li><a href="/clan.php?clanId=<?=$clan->get('id');?>"><?=$clan->get('name');?></a></li>
		<li class="active">Edit Clan</li>
	</ol>
	<?require('showMessages.php');?>
	<h1>Edit Clan</h1><br>
	<div class="">
		<form class="form-horizontal" action="/processEditClan.php?>" method="POST">
			<div class="col-sm-6">
				<input hidden name="clanId" value="<?=$clan->get('id');?>"></input>
				<div class="form-group">
					<label class="col-sm-4 control-lable" for="name">Clan Name:</label>
					<div class="col-sm-8">
						<input type="text" class="form-control" id="name" name="name" placeholder="The Most Awesome Clan Ever" value="<?=$clan->get('name');?>"></input>
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-4 control-lable" for="clanTag">Clan Tag:</label>
					<div class="col-sm-8">
						<input disabled type="text" class="form-control" id="clanTag" name="clanTag" placeholder="#JF73JOS" value="<?=$clan->get('tag');?>"></input>
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-4 control-lable" for="description">Clan Description:</label>
					<div class="col-sm-8">
						<textarea type="textarea" class="form-control" rows="4" id="description" name="description" style="resize: none;"><?=$clan->get('description');?></textarea>
					</div>
				</div>
			</div>
			<div class="col-sm-6">
				<div class="form-group">
					<label class="col-sm-4 control-lable" for="clanType">Clan Type:</label>
					<div class="col-sm-8">
						<select class="form-control" id="clanType" name="clanType">
							<option <?=($clan->get('clanType') == 'AN') ? 'selected' : '';?> value="AN">Anyone can join</option>
							<option <?=($clan->get('clanType') == 'IN') ? 'selected' : '';?> value="IN">Invite Only</option>
							<option <?=($clan->get('clanType') == 'CL') ? 'selected' : '';?> value="CL">Closed</option>
						</select>
					</div>
				</div>
			</div>
			<div class="col-sm-6">
				<div class="form-group">
					<label class="col-sm-4 control-lable" for="minimumTrophies">Required Trophies:</label>
					<div class="col-sm-8">
						<select class="form-control" id="minimumTrophies" name="minimumTrophies">
							<?for ($i=0; $i <=800 ; $i+=200) { ?>
								<option <?=($clan->get('minimumTrophies') == $i) ? 'selected' : '';?> value="<?=$i;?>"><?=$i;?></option>
							<?}?>
							<?for ($i=1000; $i <=4200 ; $i+=100) { ?>
								<option <?=($clan->get('minimumTrophies') == $i) ? 'selected' : '';?> value="<?=$i;?>"><?=$i;?></option>
							<?}?>
						</select>
					</div>
				</div>
			</div>
			<div class="col-sm-6">
				<div class="form-group">
					<label class="col-sm-4 control-lable" for="warFrequency">War Frequency:</label>
					<div class="col-sm-8">
						<select class="form-control" id="warFrequency" name="warFrequency">
							<option <?=($clan->get('warFrequency') == 'NS') ? 'selected' : '';?> value="NS">Not Set</option>
							<option <?=($clan->get('warFrequency') == 'AL') ? 'selected' : '';?> value="AL">Always</option>
							<option <?=($clan->get('warFrequency') == 'NE') ? 'selected' : '';?> value="NE">Never</option>
							<option <?=($clan->get('warFrequency') == 'TW') ? 'selected' : '';?> value="TW">Twice a week</option>
							<option <?=($clan->get('warFrequency') == 'OW') ? 'selected' : '';?> value="OW">Once a week</option>
							<option <?=($clan->get('warFrequency') == 'RA') ? 'selected' : '';?> value="RA">Rarely</option>
						</select>
					</div>
				</div>
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