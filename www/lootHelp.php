<?if(isset($loggedInUserClan)){
	$clan = $loggedInUserClan;
}else if(isset($loggedInUserPlayer)){
	$clan = $loggedInUserPlayer->get('clan');
}
$yourClanPage .= "your clan page";
if(isset($clan)){
	$yourClanPage = "<a href=\"/clan.php?clanId=".$clan->get('id')."\">" . $yourClanPage . "</a>";
}?>
<div>
	<h1 class="page-header">Loot</h1>
	<div class="blog-post">
		<p>Here you'll find everything you need to know about recording loot, creating a loot report and viewing loot statistics in Clash&nbsp;Tracker.</p>
		<hr>
		<h3>Clan Loot & Loot Reports</h3>
		<p>You can record loot statistics for your entire clan at once as long as you have access to update the clan information and once recorded create a Loot Report to compare your clan members.</p>
		<h4>Recording Clan Loot</h4>
		<p>Recording loot for your clan is really easy. Just click 'Record Loot' on <?=$yourClanPage;?> and enter the relevant achievement values for each player. The values can be found on each players profile in Clash of Clans under the Gold Grab, Elixir Escapade and Heroic Heist achievements. Once you've filled in the values you want to record, just hit save and you're done.</p>
		<div class="row">
			<div class="col-md-8">
				<h4>Loot Reports</h4>
				<p>A Loot Report is a record of how much each player in your clan raided in the past week. In order to start creating Loot Reports, record loot for your clan once, then wait a week and record it again. After you record it the second time, a 'Generate Loot Report' button should become available on <?=$yourClanPage;?>. The 'Generate Loot Report' button appears when you have at least 1 member who has 2 recordings of at least 1 loot type (Gold/Elixir/Dark Elixir) within the past week. Generating a Loot Report takes the values over the past week for all your clan members that meet these requirements and finds the difference between the earliest and latest recording for each member and each loot type. It will bring you to the Loot Report when it has finished, which displays your clan members ordered by value stolen in the past week for each loot type.</p>
			</div>
			<div class="col-md-4 blog-sidebar">
				<div class="sidebar-module sidebar-module-inset">
					<h4>Tip!</h4>
					<p>Pick a time of the week where you're normally free and record and generate a loot report for your clan during that time each week. For example, I record loot and generate the loot report for my clan every Monday evening.</p>
				</div>
			</div>
		</div>
		<hr>
		<h3>Player Loot</h3>
		<p>If you do not have access to record loot for your clan or you do not want to record it for the entire clan, you can record a single player's loot from their player page. Doing so requires access to update the player's information. Just click 'Record Loot' on the player page and enter the values and hit save. Easy as pie.</p>
		<p>While you're on the player page, you can see basic statistics about the player's raiding. This includes a graph showing how much they've raided overtime and an average for each loot type per week.</p>
	</div>
</div>