<?
require('init.php');
require('session.php');

$clanId = $_GET['clanId'];
try{
	$clan = new clan($clanId);
	$clanId = $clan->get('id');
}catch(Exception $e){
	$_SESSION['curError'] = 'No clan with id ' . $clanId . ' found.';
	header('Location: /clans.php');
	exit;
}

if(!$clan->canRequestAccess()){
	$_SESSION['curError'] = NO_ACCESS;
	header('Location: /clan.php?clanId=' . $clanId);
	exit;
}

require('header.php');
?>
<div class="col-md-12">
	<ol class="breadcrumb">
		<li><a href="/home.php">Home</a></li>
		<li><a href="/clans.php">Clans</a></li>
		<li><a href="/clan.php?clanId=<?=$clanId;?>"><?=htmlspecialchars($clan->get('name'));?></a></li>
		<li class="active">Request Access to Edit <?=htmlspecialchars($clan->get('name'));?></li>
	</ol>
	<h1>Request Access</h1>
	<div class="col-md-12"><br>
		<div class="alert alert-info" role="alert">
			<?=htmlspecialchars($clan->get('name'));?> has restricted access to updating their clan information, but you can request access to edit the clan below.
		</div>
		<form class="form-horizontal" action="/processRequestClanAccess.php" method="POST">
			<div class="col-md-12">
				<div class="col-md-6">
					<div class="form-group">
						<label class="col-sm-4 control-lable" for="message">Message:</label>
						<div class="col-sm-8">
							<textarea type="textarea" rows="6" class="form-control" id="message" name="message" placeholder="Hello, could I have access to update this clan's information?" value="Hello, could I have access to update this clan's information?"></textarea>
						</div>
					</div>
				</div>
			</div>
			<input hidden id="clanId" name="clanId" value="<?=$clanId;?>">
			<div class="row col-md-6">
				<div class="text-right btn-actions">
					<button type="submit" class="btn btn-success" name="submit" value="submit">Request</button>
				</div>
			</div>
		</form>
	</div>
	<?require('showMessages.php');?>
</div>
<?
require('footer.php');