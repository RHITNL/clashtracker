<?
require('init.php');
require('session.php');

$clanId = $_GET['clanId'];
try{
	$clan = new clan($clanId);
	$clanId = $clan->get('id');
}catch(Exception $e){
	$_SESSION['curError'] = 'No clan with id ' . $clanId . ' found.';
	header('Location: /clan.php?clanId=' . $clanId);
	exit;
}

$clanName = $clan->get('name');
if($clanName[strlen($clanName)-1] == 's'){
	$clanName .= "'";
}else{
	$clanName .= "'s";
}
$title = $clanName . ' Loot Reports';

$lootReports = $clan->getLootReports();

require('header.php');
?>
<div class="col-md-12">
	<ol class="breadcrumb">
		<li><a href="/home.php">Home</a></li>
		<li><a href="/clans.php">Clans</a></li>
		<li><a href="/clan.php?clanId=<?=$clan->get('id');?>"><?=htmlspecialchars($clan->get('name'));?></a></li>
		<li class="active">Loot Reports</li>
	</ol>
	<?require('showMessages.php');?>
	<h1><?=$title;?></h1>
	<?if(count($lootReports)>0){?>
		<div class="table-responsive">
			<table class="table table-hover">
				<thead>
					<tr>
						<th>Date</th>
					</tr>
				</thead>
				<tbody>
					<?foreach ($lootReports as $lootReport) {?>
						<tr style="cursor: pointer;" onclick="clickRow('lootReport.php?lootReportId=<?=$lootReport->get("id");?>');">
							<td><?=date('F j, Y', strtotime($lootReport->get('dateCreated')));?></td>
						</tr>
					<?}?>
				</tbody>
			</table>
		</div>
	<?}else{?>
		<div class="alert alert-info" role="alert">
			<strong>Oh no!</strong> There are no Loot Reports in our records for this clan.
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