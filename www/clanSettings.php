<h3>Clan Settings</h3><hr>
<?if(isset($loggedInUserClan)){
	$accessType = $loggedInUserClan->get('accessType');
	if($accessType == 'CL'){
		$minRank = $loggedInUserClan->get('minRankAccess');
	}else{
		$minRank = 'ME';
	}
	if($accessType == 'US'){
		$allowedUsers = $loggedInUserClan->getAllowedUsers();
	}else{
		$allowedUsers = array();
	}?>
	<div class="col-md-12">
		<div class="col-md-6">
			<label>Linked Clan:</label>
			<a href="/clan.php?clanId=<?=$loggedInUserClan->get('id');?>"> <?=$loggedInUserClan->get("name") . ' (' . $loggedInUserClan->get("tag") . ')';?></a>
		</div>
		<div class="col-md-6 text-right">
			<form class="form-inline" action="/processUnlinkClan.php" method="POST">
				<button type="submit" class="btn btn-danger text-right">Unlink Clan</button>
			</form>
		</div><br><br>
	</div>
	<div class="col-md-12">
		<div class="col-md-12">
			<div class="alert alert-info" role="alert">
				Below you can control who has access to update the information for <strong><?=$loggedInUserClan->get("name");?></strong>.
			</div>
		</div>
		<form class="form-horizontal" action="/processClanAccess.php" method="POST">
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
						<strong>Um..</strong> This gives access to everyone to update the information for your clan. You should consider choosing a more restrictive setting.
					</div>
				</div>
				<div id="CLDiv" <?=($accessType != 'CL') ? 'hidden' : '';?>>
					<div class="alert alert-info" role="alert">
						This setting allows only clan mates of a certain rank have access to update the information for your clan. You can choose the minimum rank that has access below.
					</div>
					<div class="form-group">
						<label class="col-sm-2 control-lable" for="minRank">Minimum Rank:</label>
						<div class="col-sm-4">
							<select class="form-control" id="minRank" name="minRank">
								<option <?=($minRank == 'ME') ? 'selected' : '';?> value="ME">Member</option>
								<option <?=($minRank == 'EL') ? 'selected' : '';?> value="EL">Elder</option>
								<option <?=($minRank == 'CO') ? 'selected' : '';?> value="CO">Co-leader (Recommended)</option>
								<option <?=($minRank == 'LE') ? 'selected' : '';?> value="LE">Leader</option>
							</select>
						</div>
					</div>
				</div>
				<div id="USDiv" <?=($accessType != 'US') ? 'hidden' : '';?>>
					<div class="alert alert-info" role="alert">
						This setting allows only users that you choose have access to update the information for your clan. If you want to be the only one that can, choose this setting and do <strong>not</strong> give access to anyone.
					</div>
					<a type="button" class="btn btn-success" onclick="addRow();">Add</a>
					<?if(count($allowedUsers)>0){?>
						<a type="button" class="btn btn-danger" href="/processDisallowAllUserAccessForClan.php">Remove All</a>
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
												<a type="button" class="btn btn-xs btn-danger" href="/processDisallowUserAccessForClan.php?userId=<?=$user->get('id');?>">Revoke Access</a>
											</td>
										</tr>
									<?}?>
								</tbody>
							</table>
						</div>
					<?}else{?>
						<div id="noRows" class="alert alert-info" role="alert">
							You haven't granted access to any other user, this means you are the only one that can update your clan's information. If you'd like to give access to other users, you can do so by clicking 'Add' above and then 'Update' below.
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
				noRows += 'You haven\'t granted access to any other user, this means you are the only one that can update your clan\'s war stats. If you\'d like to give access to other users, you can do so by clicking \'Add\' above and then \'Update\' below.';
				noRows += '</div>';
				$('#USDiv').append(noRows);
			}
		}
	</script>
<?}else{?>
	<div class="alert alert-info" role="alert">
		<strong>On no!</strong> Your account isn't linked to a clan. You can do this below.
	</div>
	<div id="linkClanButtonDiv" class="col-md-12">
		<button type="button" class="btn btn-primary" onclick="showLinkClanForm();">Link Clan</button>
	</div>
	<div id="linkClanDiv" hidden class="col-md-12">
		<form class="form-inline" action="/processLinkClan.php" method="POST">
			<div class="col-md-6">
				<div class="form-group">
					<label for="clanTag">Clan Tag:</label>
					<input type="text" class="form-control" id="clanTag" name="clanTag" placeholder="<?=randomTag();?>">
				</div>
				<button type="cancel" class="btn btn-default text-right" onclick="return showLinkClanButton();">Cancel</button>
				<button type="submit" class="btn btn-primary text-right">Link</button>
			</div>
		</form>
	</div>
	<script type="text/javascript">
		function showLinkClanForm(){
			$('#linkClanButtonDiv').hide();
			$('#linkClanDiv').show();
		}
		function showLinkClanButton(){
			$('#linkClanButtonDiv').show();
			$('#linkClanDiv').hide();
			return false;
		}
	</script>
<?}?>