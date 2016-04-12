<?
require('init.php');
require('session.php');
if(isset($loggedInUser)){
	$_SESSION['curError'] = NO_ACCESS;
	header('Location: /home.php');
	exit;
}
require('header.php');
?>
<div class="col-md-12">
	<?require('showMessages.php');?>
	<div class="col-md-4"></div>
	<div class="well col-md-4">
		<h2>Forgotten Password</h2><br>
			<form class="form-horizontal" action="/processForgotPassword.php" method="POST">
				<div class="col-md-12">
					<div class="form-group">
						<label for="email">Email Address:</label>
						<input type="text" class="form-control" id="email" name="email" placeholder="angryneeson52@example.com" value="<?=$_SESSION['email'];?>">
					</div>
				</div>
				<div class="row">
				<div class="col-sm-12 text-right btn-actions">
					<button type="cancel" class="btn btn-default" name="cancel" value="cancel">Cancel</button>
					<button type="submit" class="btn btn-success" name="submit" value="submit">Reset Password</button>
				</div>
			</div>
			</form>
		</div>
	</div>
	<div class="col-md-4"></div>
<?
require('footer.php');
