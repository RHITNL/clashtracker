<?
require('init.php');
require('header.php');
?>
<div class="col-md-12">
	<?require('showMessages.php');?>
	<div class="col-md-4"></div>
	<div class="well col-md-4">
		<h2>Log In</h2>
			<form class="form-horizontal" action="/processLogin.php" method="POST">
				<a class="help-block" href="/signup.php">Don't have an account?</a>
				<div class="col-md-12">
					<div class="form-group">
						<label for="email">Email Address:</label>
						<input type="text" class="form-control" id="email" name="email" placeholder="angryneeson52@example.com" value="<?=$_SESSION['email'];?>">
					</div>
					<div class="form-group">
						<label for="password">Password:</label>
						<input type="password" class="form-control" id="password" name="password" placeholder="********" value="">
					</div>
				</div>
				<div class="row">
				<div class="col-sm-12 text-right btn-actions">
					<button type="submit" class="btn btn-success" name="submit" value="submit">Log in</button>
				</div>
				<div class="col-sm-12">
					<a class="help-block" href="/forgotPassword.php">Forgot password?</a>
				</div>
			</div>
			</form>
		</div>
	</div>
	<div class="col-md-4"></div>
<?
require('footer.php');
