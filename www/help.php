<?
require('init.php');
require('session.php');
require('header.php');
$article = $_GET['article'];
$article = isset($article) ? $article : 'overview';
?>
<div id="wrapper" class="col-md-12">
	<div id="sidebar-wrapper">
		<div id="sidebar">
			<ul class="nav nav-sidebar">
				<li class="<?=$article == 'overview' ? 'active' : '';?>"><a href="/help.php?article=overview">Overview</a></li>
				<li class="<?=$article == 'wars' ? 'active' : '';?>"><a href="/help.php?article=wars">Wars</a></li>
				<li class="<?=$article == 'loot' ? 'active' : '';?>"><a href="/help.php?article=loot">Loot</a></li>
			</ul>
		</div>
	</div>
	<div id="page-content-wrapper" class="main">
		<?require('showMessages.php');?>
		<ol class="breadcrumb">
			<li><a href="/home.php">Home</a></li>
			<li class="active">Help</li>
		</ol>
		<h1 class="page-header"><?=ucwords($article);?></h1>
		<div class="hidden-sm hidden-md hidden-lg">
			<a href="#menu-toggle" class="btn btn-default" id="menu-toggle">Show Menu</a>
		</div>
		<?require($article . 'Help.php');?>
	</div>
</div>
<script>
	$("#menu-toggle").click(function(e) {
		e.preventDefault();
		$("#wrapper").toggleClass("toggled");
	});
</script>
<?
// require('footer.php');
