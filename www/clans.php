<?
require(__DIR__ . '/../config/functions.php');

$clans = array();
try{
	$clans = clan::getClans();
}catch(Exception $e){
	$_SESSION['curError'] = $e->getMessage();
}

require('header.php');
?>
<div class="col-md-12">
	<ol class="breadcrumb">
		<li><a href="/home.php">Home</a></li>
		<li class="active">Clans</li>
	</ol>
	<?require('showMessages.php');?>
	<h1>Clans</h1><br>
	<a type="button" class="btn btn-success" href="/addClan.php">Add Clan</a><br><br>
	<?if(count($clans)>0){?>
		<div class="table-responsive">
			<table class="table table-hover">
				<thead>
					<tr>
						<th>Clan name</th>
						<th>Wars Won</th>
						<th>Members</th>
						<th>Type</th>
						<th>War Frequency</th>
						<th>Required Trophies</th>
						<th class="text-right">Clan Tag</th>
					</tr>
				</thead>
				<tbody>
					<?foreach ($clans as $clan) {?>
						<tr style="cursor: pointer;" onclick="clickRow('clan.php?clanId=<?=$clan->get("id");?>');">
							<td><?=$clan->get('name');?></td>
							<td><?=$clan->getNumWarsWon();?></td>
							<td><?=$clan->getNumMembers();?></td>
							<td><?=clanTypeFromCode($clan->get('clanType'));?></td>
							<td><?=warFrequencyFromCode($clan->get('warFrequency'));?></td>
							<td><?=$clan->get('minimumTrophies');?></td>
							<td class="text-right"><?=$clan->get('tag');?></td>
						</tr>
					<?}?>
				</tbody>
			</table>
		</div>
	<?}else{?>
		<div class="alert alert-info" role="alert">
			<strong>Oh no!</strong> There's no clans in our records. You can start by adding one above.
		</div>
	<?}?>
</div>
<script type="text/javascript">
function clickRow(href){
	window.document.location = href;
}
</script>
<?
require('footer.php');