<h3>General Settings</h3><hr>
<div class="col-md-12">
	<label>Email:</label>
	<span> <?=$loggedInUser->get('email');?></span>
	<button id="showChangePasswordButton" type="button" class="btn btn-xs btn-primary" onclick="showChangeEmailDiv();">Change</button>
</div>
<div id="changePasswordDiv" class="col-md-12 hidden">
	<br>
	<h4>Change Email</h4><br>
	<form class="form-horizontal" action="/processChangeEmail.php" method="POST">
		<div class="col-md-12">
			<div class="form-group">
				<label class="col-sm-4 control-lable" for="newEmail">New Email:</label>
				<div class="col-sm-8">
					<input type="text" class="form-control" id="newEmail" name="newEmail" placeholder="angryneeson52@example.com" value="<?=$_SESSION['newEmail'];?>">
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-4 control-lable" for="password">Password:</label>
				<div class="col-sm-8">
					<input type="password" class="form-control" id="password" name="password" placeholder="********" value="<?=$_SESSION['password'];?>">
				</div>
			</div>
			<div class="row">
				<div class="col-sm-12 text-right btn-actions">
					<br>
					<button type="cancel" class="btn btn-default" name="cancel" value="cancel" onclick="return hideChangeEmailDiv();">Cancel</button>
					<button type="submit" class="btn btn-success" name="submit" value="submit">Submit</button>
				</div>
			</div>
			<br>
		</div>
	</form>
</div>
<script type="text/javascript">
	function showChangeEmailDiv(){
		$('#showChangePasswordButton').addClass('hidden');
		$('#changePasswordDiv').removeClass('hidden');
	}
	function hideChangeEmailDiv(){
		$('#showChangePasswordButton').removeClass('hidden');
		$('#changePasswordDiv').addClass('hidden');
		return false;
	}
</script>