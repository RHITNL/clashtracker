<?
require('init.php');
require('session.php');
require('header.php');
$article = $_GET['article'];
$article = isset($article) ? $article : 'overview';
?>
<div class="col-md-12">
	<div class="col-sm-3 col-md-2 sidebar">
		<ul class="nav nav-sidebar">
			<li class="<?=$article == 'overview' ? 'active' : '';?>"><a href="/help.php?article=overview">Overview</a></li>
			<li class="<?=$article == 'wars' ? 'active' : '';?>"><a href="/help.php?article=wars">Wars</a></li>
			<li class="<?=$article == 'loot' ? 'active' : '';?>"><a href="/help.php?article=loot">Loot</a></li>
		</ul>
	</div>
	<div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">
		<?require('showMessages.php');?>
		<ol class="breadcrumb">
			<li><a href="/home.php">Home</a></li>
			<li class="active">Help</li>
		</ol>
		<?require($article . 'Help.php');?>
	</div>
</div>
<?
require('footer.php');