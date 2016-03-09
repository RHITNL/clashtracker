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

$members = $clan->getMembers();
$members = sortPlayersByWarScore($members);

$moreInfo = array();
$moreInfo[] = "A score calculated from the player's offensive and defensive performance in wars.";
$moreInfo[] = "The average number of total stars the player got with their first attack.";
$moreInfo[] = "The average number of stars that counted towards the war that the player got with their first attack.";
$moreInfo[] = "The average number of total stars the player got with their second attack.";
$moreInfo[] = "The average number of stars that counted towards the war that the player got with their second attack.";
$moreInfo[] = "The average number of stars the player lost per defence.";
$moreInfo[] = "The average number of times the player was attacked per war.";
$moreInfo[] = "The average number of times the player attacked per war.";

require('header.php');
?>
<div class="col-md-12">
	<ol class="breadcrumb">
		<li><a href="/home.php">Home</a></li>
		<li><a href="/clans.php">Clans</a></li>
		<li><a href="/clan.php?clanId=<?=$clanId;?>"><?=htmlspecialchars($clan->get('name'));?></a></li>
		<li class="active">War Stats</li>
	</ol>
	<?require('showMessages.php');?>
	<h1>War Stats</h1>
	<?if(count($members)>0){?>
		<div class="table-responsive">
			<table class="table table-hover">
				<thead>
					<tr>
						<th></th>
						<!-- <th><div data-toggle="popover" data-trigger="hover" data-placement="top" data-content="Player's Name">Name</div></th> -->
						<th>Name</th>
						<th><div data-toggle="popover" data-trigger="hover" data-placement="top" data-content="<?=$moreInfo[0];?>">Score</div></th>
						<th><div data-toggle="popover" data-trigger="hover" data-placement="top" data-content="<?=$moreInfo[1];?>">1<sup>st</sup> Attack Stars</div></th>
						<th><div data-toggle="popover" data-trigger="hover" data-placement="top" data-content="<?=$moreInfo[2];?>">1<sup>st</sup> Attack New Stars</div></th>
						<th><div data-toggle="popover" data-trigger="hover" data-placement="top" data-content="<?=$moreInfo[3];?>">2<sup>nd</sup> Attack Stars</div></th>
						<th><div data-toggle="popover" data-trigger="hover" data-placement="top" data-content="<?=$moreInfo[4];?>">2<sup>nd</sup> Attack New Stars</div></th>
						<th><div data-toggle="popover" data-trigger="hover" data-placement="top" data-content="<?=$moreInfo[5];?>">Stars on Defence</div></th>
						<th><div data-toggle="popover" data-trigger="hover" data-placement="top" data-content="<?=$moreInfo[6];?>">Number of Defences</div></th>
						<th><div data-toggle="popover" data-trigger="hover" data-placement="top" data-content="<?=$moreInfo[7];?>">Attacks Used</div></th>
						<th class="text-right">Player Tag</th>
					</tr>
				</thead>
				<tbody>
					<?foreach ($members as $member) {?>
						<tr style="cursor: pointer;" onclick="clickRow('player.php?playerId=<?=$member->get("id");?>&clanId=<?=$clan->get('id');?>');">
							<td width="20">
								<?$url = $member->get('leagueUrl');
								if(strlen($url)>0){?>
									<img src="<?=$url;?>" height="20" width="20">
								<?}?>
							</td>
							<td><?=htmlspecialchars($member->get('name'));?></td>
							<td><?=number_format($member->getScore(), 2);?></td>
							<td><?=number_format($member->get('firstAttackTotalStars') / $member->get('numberOfWars'), 2);?></td>
							<td><?=number_format($member->get('firstAttackNewStars') / $member->get('numberOfWars'), 2);?></td>
							<td><?=number_format($member->get('secondAttackTotalStars') / $member->get('numberOfWars'), 2);?></td>
							<td><?=number_format($member->get('secondAttackNewStars') / $member->get('numberOfWars'), 2);?></td>
							<td><?=number_format($member->get('starsOnDefence') / $member->get('numberOfWars'), 2);?></td>
							<td><?=number_format($member->get('numberOfDefences') / $member->get('numberOfWars'), 2);?></td>
							<td><?=number_format($member->get('attacksUsed') / $member->get('numberOfWars'), 2);?></td>
							<td class="text-right"><?=$member->get('tag');?></td>
						</tr>
					<?}?>
				</tbody>
			</table>
		</div>
	<?}else{?>
		<div class="alert alert-info">
			<strong>Oh no!</strong> There's no members currently in this clan that have been in any wars.
		</div>
	<?}?>
</div>
<script type="text/javascript">
function clickRow(href){
	window.document.location = href;
}
$(document).ready(function(){
    $('[data-toggle="popover"]').popover();   
});
</script>
<?
require('footer.php');