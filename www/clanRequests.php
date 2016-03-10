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

if(isset($loggedInUserClan) && $loggedInUserClan->get('id') == $clanId){
	$requests = $clan->getRequests();
}else{
	$_SESSION['curError'] = NO_ACCESS;
	header('Location: /clan.php?clanId=' . $clanId);
	exit;
}

require('header.php');
?>
<div class="col-md-12">
	<ol class="breadcrumb">
		<li><a href="/home.php">Home</a></li>
		<li><a href="/clan.php?clanId=<?=$clanId;?>"><?=htmlspecialchars($clan->get('name'));?></a></li>
		<li class="active">Clan Requests</li>
	</ol>
	<?require('showMessages.php');?>
	<h1>Clan Requests</h1>
	<div class="col-md-12"><br>
		<div class="alert alert-info" role="alert">
			The following players have requested access to update your clan's information. Accepting their request will allow them to add/edit wars, add players, record loot and create loot reports for the clan. If you accept, you can revoke access at any time in your settings.
		</div>
		<div class="table-responsive">
			<table class="table table-hover">
				<thead>
					<tr>
						<th>Player Name</th>
						<th>Player Rank</th>
						<th>Email</th>
						<th>Message</th>
						<th class="text-right">Actions</th>
					</tr>
				</thead>
				<tbody>
					<?foreach($requests as $request){?>
						<tr>
							<td>
								<?$requestPlayer = $request->user->get('player');
								if(isset($requestPlayer)){
									print htmlspecialchars($requestPlayer->get('name'));
								}?>									
							</td>
							<td>
								<?if(isset($requestPlayer)){
									print rankFromCode($requestPlayer->get('rank'));
								}?>
							</td>
							<td><?=$request->user->get('email');?></td>
							<td><?=htmlspecialchars($request->message);?></td>
							<td class="text-right">
								<a type="button" class="btn btn-xs btn-danger" href="/processClanRequestResponse.php?clanId=<?=$clan->get('id');?>&userId=<?=$request->user->get('id');?>&response=decline">Decline</a>
								<a type="button" class="btn btn-xs btn-success" href="/processClanRequestResponse.php?clanId=<?=$clan->get('id');?>&userId=<?=$request->user->get('id');?>&response=accept">Accept</a>
							</td>
						</tr>
					<?}?>
				</tbody>
			</table>
		</div>
	</div>
</div>
<?
require('footer.php');