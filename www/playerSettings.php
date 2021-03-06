<h3>Player Settings</h3><hr>
<?if(isset($loggedInUserPlayer)){
	$accessType = $loggedInUserPlayer->get('accessType');
	if($accessType == 'CL'){
		$minRank = $loggedInUserPlayer->get('minRankAccess');
	}else{
		$minRank = 'ME';
	}
	if($accessType == 'US'){
		$allowedUsers = $loggedInUserPlayer->getAllowedUsers();
	}else{
		$allowedUsers = array();
	}?>
	<div class="col-md-12">
		<div class="col-md-6">
			<label>Linked Player:</label>
			<a href="/player.php?playerId=<?=$loggedInUserPlayer->get('id');?>"> <?=$loggedInUserPlayer->get("name") . ' (' . $loggedInUserPlayer->get("tag") . ')';?></a>
		</div>
		<div class="col-md-6 text-right">
			<form class="form-inline" action="/processUnlinkPlayer.php" method="POST">
				<button type="submit" class="btn btn-danger text-right">Unlink Player</button>
			</form>
		</div><br><br>
	</div>
	<div class="col-md-12">
		<div class="col-md-12">
			<div class="alert alert-info" role="alert">
				Below you can control who has access to update loot for <strong><?=$loggedInUserPlayer->get("name");?></strong>.
			</div>
		</div>
		<form class="form-horizontal" action="/processPlayerAccess.php" method="POST">
			<div class="col-md-12">
				<div class="form-group">
					<label class="col-sm-2 control-lable" for="accessType">Type:</label>
					<div class="col-sm-4">
						<select class="form-control" id="accessType" name="accessType" onchange="showDiv(this.value)">
							<option <?=($accessType == 'AN') ? 'selected' : '';?> value="AN">Anyone</option>
							<option <?=($accessType == 'CL') ? 'selected' : '';?> value="CL">Clan Level (Recommended)</option>
							<option <?=($accessType == 'US') ? 'selected' : '';?> value="US">User Level</option>
						</select>
					</div>
				</div>
			</div>
			<div class="col-md-12">
				<div id="ANDiv" <?=($accessType != 'AN') ? 'hidden' : '';?>>
					<div class="alert alert-warning" role="alert">
						<strong>Um..</strong> This gives access to everyone to update loot stats on your player. You should consider choosing a more restrictive setting.
					</div>
				</div>
				<div id="CLDiv" <?=($accessType != 'CL') ? 'hidden' : '';?>>
					<div class="alert alert-info" role="alert">
						This setting allows only clan mates of a certain rank have access to update the loot stats on your player. You can choose the minimum rank that has access below.
					</div>
					<div class="form-group">
						<label class="col-sm-2 control-lable" for="minRank">Minimum Rank:</label>
						<div class="col-sm-4">
							<select class="form-control" id="minRank" name="minRank">
								<option <?=($minRank == 4) ? 'selected' : '';?> value="4">Member</option>
								<option <?=($minRank == 3) ? 'selected' : '';?> value="3">Elder</option>
								<option <?=($minRank == 2) ? 'selected' : '';?> value="2">Co-leader (Recommended)</option>
								<option <?=($minRank == 1) ? 'selected' : '';?> value="1">Leader</option>
							</select>
						</div>
					</div>
				</div>
				<div id="USDiv" <?=($accessType != 'US') ? 'hidden' : '';?>>
					<div class="alert alert-info" role="alert">
						This setting allows only users that you choose have access to update the loot stats on your player. If you want to be the only one that can, choose this setting and do <strong>not</strong> give access to anyone.
					</div>
					<a type="button" class="btn btn-success" onclick="addRow();">Add</a>
					<?if(count($allowedUsers)>0){?>
						<a type="button" class="btn btn-danger" href="/processDisallowAllUserAccess.php">Remove All</a>
					<?}?><br><br>
					<?if(count($allowedUsers)>0){?>
						<div id="USTable" class="table-responsive">
							<table class="table table-hover">
								<thead>
									<tr>
										<th>Email</th>
										<th></th>
									</tr>
								</thead>
								<tbody id="tableRows">
									<?foreach ($allowedUsers as $user) {?>
										<tr>
											<td><?=$user->get('email');?></td>
											<td class="text-right">
												<a type="button" class="btn btn-xs btn-danger" href="/processDisallowUserAccess.php?userId=<?=$user->get('id');?>">Revoke Access</a>
											</td>
										</tr>
									<?}?>
								</tbody>
							</table>
						</div>
					<?}else{?>
						<div id="noRows" class="alert alert-info" role="alert">
							You haven't granted access to any other user, this means you are the only one that can update your player's loot stats. If you'd like to give access to other users, you can do so by clicking 'Add' above and then 'Update' below.
						</div>
					<?}?>
				</div>
				<div class="row">
					<div class="col-sm-12 text-right btn-actions">
						<button type="submit" class="btn btn-success" name="submit" value="submit">Update</button>
					</div>
				</div><br>
			</div>
		</form>
	</div>
	<script type="text/javascript">
		var newRowCount = 0;
		var totalRows = <?=count($allowedUsers);?>;
		function showDiv(type){
			$('#ANDiv').hide();
			$('#CLDiv').hide();
			$('#USDiv').hide();
			$('#' + type + 'Div').show();
		}
		function addRow(){
			var newRow;
			if(totalRows == 0){
				var newTable;
				$('#noRows').remove();
				newTable  = '<div id="USTable" class="table-responsive">\n';
				newTable += '\t<table class="table table-hover">\n';
				newTable += '\t\t<thead>\n';
				newTable += '\t\t\t<tr>\n';
				newTable += '\t\t\t\t<th>Email</th>\n';
				newTable += '\t\t\t\t<th></th>\n';
				newTable += '\t\t\t</tr>\n';
				newTable += '\t\t</thead>\n';
				newTable += '\t\t<tbody id="tableRows">\n';
				newTable += '\t\t</tbody>\n';
				newTable += '\t</table>\n';
				newTable += '</div>';
				$('#USDiv').append(newTable);

			}
			var id = 'newRow-' + (newRowCount++);
			newRow  = '<tr id="'+id+'">\n';
			newRow += '\t<td>\n';
			newRow += '\t\t<input type="text" class="form-control" id="'+id+'" name="additionalEmails['+id+']" placeholder="angryneeson'+(51+newRowCount)+'@example.com">\n';
			newRow += '\t</td>\n';
			newRow += '\t<td class="text-right">\n';
			newRow += '\t\t<a type="button" class="btn btn-xs btn-danger" onclick="removeRow(\''+id+'\');">&times;</a>\n';
			newRow += '\t</td>\n';
			newRow += '</tr>\n';
			$("#tableRows").append(newRow);
			totalRows++;
		}
		function removeRow(id){
			totalRows--;
			$('#'+id).remove();
			if(totalRows==0){
				$('#USTable').remove();
				var noRows;
				noRows  = '<div id="noRows" class="alert alert-info" role="alert">';
				noRows += 'You haven\'t granted access to any other user, this means you are the only one that can update your player\'s loot stats. If you\'d like to give access to other users, you can do so by clicking \'Add\' above and then \'Update\' below.';
				noRows += '</div>';
				$('#USDiv').append(noRows);
			}
		}
	</script>
<?}else{?>
	<div class="alert alert-info" role="alert">
		<strong>On no!</strong> Your account isn't linked to a Clash of Clans player. You can do this below.
	</div>
	<div id="linkPlayerButtonDiv" class="col-md-12">
		<button type="button" class="btn btn-primary" onclick="showLinkPlayerForm();">Link Player</button>
	</div>
	<div id="linkPlayerDiv" hidden class="col-md-12">
		<form class="form-inline" action="/processLinkPlayer.php" method="POST">
			<div class="col-md-6">
				<div class="form-group">
					<label for="playerTag">Player Tag:</label>
					<input type="text" class="form-control" id="playerTag" name="playerTag" placeholder="<?=randomTag();?>">
				</div>
				<button type="cancel" class="btn btn-default text-right" onclick="return showLinkPlayerButton();">Cancel</button>
				<button type="submit" class="btn btn-primary text-right">Link</button>
			</div>
		</form>
	</div>
	<script type="text/javascript">
		function showLinkPlayerForm(){
			$('#linkPlayerButtonDiv').hide();
			$('#linkPlayerDiv').show();
		}
		function showLinkPlayerButton(){
			$('#linkPlayerButtonDiv').show();
			$('#linkPlayerDiv').hide();
			return false;
		}
	</script>
<?}?>