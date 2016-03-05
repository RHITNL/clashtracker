<?
require('init.php');
require('session.php');

$lootReportId = $_GET['lootReportId'];
try{
	$lootReport = new lootReport($lootReportId);
	$clan = $lootReport->get('clan');
	$results = $lootReport->get('results');
}catch(Exception $e){
	$_SESSION['curError'] = 'No Loot Report with id ' . $lootReportId . ' found.';
	header('Location: /clans.php');
	exit;
}

$previousLootReports = count($clan->getLootReports())>1;
$colSize = 12 / count($results);
$types = array(
	'GO' => '<i class="fa fa-coins" style="color: gold;"></i>',
	'EL' => '<i class="fa fa-tint" style="color: #FF09F4;"></i>',
	'DE' => '<i class="fa fa-tint"></i>'
);

require('header.php');
?>
<div class="col-md-12">
	<ol class="breadcrumb">
		<li><a href="/home.php">Home</a></li>
		<li><a href="/clans.php">Clans</a></li>
		<li><a href="/clan.php?clanId=<?=$clan->get('id');?>"><?=htmlspecialchars($clan->get('name'));?></a></li>
		<li class="active">Loot Report</li>
	</ol>
	<?require('showMessages.php');?>
	<h1 style="margin-bottom: 0px;">Loot Report</h1>
	<h5 style="margin-top: 0px;"><?=date('F j, Y', strtotime($lootReport->get('dateCreated')));?></h5>
	<div class="col-md-12">
		<?foreach($types as $type => $symbol){
			$result = $results[$type];?>
			<div class="col-md-<?=$colSize;?>">
				<h3><?=lootTypeFromCode($type);?>&nbsp;<?=$symbol;?></h3>
				<div class="table-responsive">
					<table class="table table-hover">
						<thead>
							<tr>
								<th>Name</th>
								<th class="text-right">Amount</th>
							</tr>
						</thead>
						<tbody>
							<?foreach($result as $rank => $playerResult){?>
								<tr style="cursor: pointer;" onclick="clickRow('player.php?playerId=<?=$playerResult['player']->get("id");?>&clanId=<?=$clan->get('id');?>');">
									<td><?=$rank+1 . '. ' . htmlspecialchars($playerResult['player']->get('name'));?></td>
									<td class="text-right"><?=number_format($playerResult['amount'], 0, '.', ',');?></td>
								</tr>
							<?}?>
						</tbody>
					</table>
				</div>
			</div>
		<?}?>
	</div>
	<?if($previousLootReports){?>
		<a type="button" class="btn btn-success" href="/lootReports.php?clanId=<?=$clan->get('id');?>">Previous Loot Reports</a>
	<?}
	if(userHasAccessToUpdateClan($clan)){?>
		<a type="button" class="btn btn-danger" onclick="return confirm('This action cannot be undone. Are you sure you\'d like to delete it?');" href="/processDeleteLootReport.php?lootReportId=<?=$lootReport->get('id');?>">Delete Report</a>
	<?}?>
</div>
<script type="text/javascript">
function clickRow(href){
	window.document.location = href;
}
</script>
<?
require('footer.php');