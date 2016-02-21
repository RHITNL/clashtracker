<h3>Password Settings</h3><hr>
<h4>Change Password</h4><br>
<form class="form-horizontal" action="/processChangePassword.php" method="POST">
	<div class="col-sm-8">
		<div class="form-group">
			<label class="col-sm-4 control-lable" for="oldPassword">Current Password:</label>
			<div class="col-sm-8">
				<input type="password" class="form-control" id="oldPassword" name="oldPassword" placeholder="********" value="<?=$_SESSION['oldPassword'];?>">
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-4 control-lable" for="newPassword">New Password:</label>
			<div class="col-sm-8">
				<input type="password" class="form-control" id="newPassword" name="newPassword" placeholder="********" value="<?=$_SESSION['newPassword'];?>">
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-4 control-lable" for="confirmPassword">Confirm Password:</label>
			<div class="col-sm-8">
				<input type="password" class="form-control" id="confirmPassword" name="confirmPassword" placeholder="********" value="<?=$_SESSION['confirmPassword'];?>">
			</div>
		</div>
		<div class="row">
			<div class="col-sm-12 text-right btn-actions">
				<br>
				<button type="submit" class="btn btn-success" name="submit" value="submit">Submit</button>
			</div>
		</div>
	</div>
	<div class="col-sm-4"></div><br><br>
</form>