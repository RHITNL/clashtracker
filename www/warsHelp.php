<?
if(isset($loggedInUserClan)){
	$clan = $loggedInUserClan;
}else if(isset($loggedInUserPlayer)){
	$clan = $loggedInUserPlayer->get('clan');
}
$yourClanPage .= "your clan page";
if(isset($clan)){
	$yourClanPage = "<a href=\"/clan.php?clanId=".$clan->get('id')."\">" . $yourClanPage . "</a>";
}
?>
<div>
	<div class="blog-post">
		<p>Here you'll find everything you need to know about anything to do with wars in Clash Tracker. Adding/Updating wars can only be done by users who have access to update the clan information. If the clan has restricted access, then you might need to ask your clanmates to give you access before you can Add or Update war information.</p>
		<hr>
		<h3>Adding a War</h3>
		<p>When you want to record a war for your clan. Go to <?=$yourClanPage;?> and click 'Add War', which will only show up once there is at least one member saved in our system. You will be directed to a page where you must enter the opponent's Clan Tag and select the size of the war. Once this is done, Clash&nbsp;Tracker will automagically get the name of the clan and create the war in our system. The next step is to add clan members to the war.</p>
		<p>Note: Adding a new war will make the previous war uneditable, so make sure the previous war is completely up to date before adding the next one.</p>
		<hr>
		<h3>Updating a War</h3>
		<p>If you haven't just created the war, go to <?=$yourClanPage;?> and click 'Current War' to get to the current war. </p>
		<div class="row">
			<div class="col-md-8">
				<h4>Adding Members to a War</h4>
				<p>Once a war has been created, you need to add the players from both clans into the war. This can be done by clicking 'Add Players' on the war page. You will be directed to a page where you must select the players in the clan that are in the war or add players who are not currently in the clan. If you accidentally add a player who is not in the war, you can easily remove them by clicking the &times;.</p>
				<p>You can reorder the players in the war for each clan using the up and down arrows beside each of their names.</p>
				<p>Once you've added the players to the war for both clans, the next step is to add the attacks.</p>
			</div>
			<div class="col-md-4 blog-sidebar">
				<div class="sidebar-module sidebar-module-inset">
					<h4>Tip!</h4>
					<p>You can go to the opponent's clan page to add the players in the war to the clan. Doing this avoids the need to input each player's name and tag individually; all players can be added at once by inputting their player tags and then later selected quickly on the Add Players to War page.</p>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="hidden-xs hidden-sm col-md-4 blog-sidebar">
				<div class="sidebar-module sidebar-module-inset">
					<h4>Note!</h4>
					<p>For better statistics, enter the attacks in the order they happen in the war. This allows us to properly determine which stars for each attack count towards the war and use this information for better statistics later on.</p>
				</div>
			</div><br>
			<div class="col-md-8">
				<h4>Adding Attacks to a War</h4>
				<p>Attacks for each player can be added by clicking 'Add Attack' beside the player's name on the war page. You will be directed to a page where you must select who the player attacked in the opposite clan and how many total stars they got for the attack.</p>
				<p>Attacks can be edited or deleted by clicking on 'War Events', which has an ordered list of all the attacks recorded thus far.</p>
			</div>
			<div class="hidden-md hidden-lg col-sm-12 blog-sidebar">
				<div class="sidebar-module sidebar-module-inset">
					<h4>Note!</h4>
					<p>For better statistics, enter the attacks in the order they happen in the war. This allows us to properly determine which stars for each attack count towards the war and use this information for better statistics later on.</p>
				</div>
			</div>
		</div>
		<hr>
		<h3>Viewing War Statistics</h3>
		<p>Once you've inputted the information for your clan, you can start to view statistics about your members. There are two ways to do this; viewing all members at once or looking at clan members individually.</p>
		<h4>Clan War Statistics</h4>
		<p>To view statistics for all your clan members at once, go to <?=$yourClanPage;?> and click 'War Statistics'. This button shows up once you've added at least 2 wars because it only draws upon completed wars for statistics. On this page you can view how many stars that each clan member gets on average for both attacks, along with average stars lost on defence, average number of times they get attacked and average number of attacks they use.</p>
		<p>This page also includes a score that is calculated based on their offensive and defensive performance in wars. The higher the score the better their performance in wars. (Coming&nbsp;Soon: The ability to change how the score is calculated for your clan.)</p>
		<br>
		<h4>Player War Statistics</h4>
		<p>In addition to seeing an overview of all clan members' war statistics, you can view an individual's statistics by going to their player page. If they've participated in at least one war, then Pie Charts will show giving a breakdown of how their attacks and defenses have gone. It also shows the average number of stars won and lost per attack/defence.</p>
		<p>You can get even more detailed information by click on the title 'Wars' on their player page. This will direct you to a page giving a summary of all the wars that the player has participated in along with how many stars they got for each attack and lost on defence for the given war.</p>
	</div>
</div>