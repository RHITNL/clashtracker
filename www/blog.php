<?
require('init.php');
require('session.php');
require('header.php');
require('showMessages.php');

$month = $_GET['month'];
if(isset($month)){
	$afterDate = strtotime($month);
	$beforeDate = strtotime("+1 month", $afterDate);
}
$blogPosts = blogPost::getBlogPosts($beforeDate, $afterDate);
?>
<div class="col-md-12">
	<ol class="breadcrumb">
		<li><a href="/home.php">Home</a></li>
		<li class="active">Updates<?=isset($month) ? ": " . $month : "";?></li>
	</ol>
	<div class="blog-header">
		<h1 class="blog-title">Updates</h1>
		<?if(isset($month)){?>
			<p class="lead blog-description"><?=$month;?></p>
		<?}?>
	</div>
	<?if(count($blogPosts)>0){
		foreach ($blogPosts as $blogPost) {?>
		  	<div class="row">
				<div class="col-sm-8 blog-main">
			  		<div class="blog-post">
						<h2 class="blog-post-title"><?=$blogPost->get('name');?></h2>
						<p class="blog-post-meta"><?=date('F j, Y', strtotime($blogPost->get('dateCreated')));?></p>
						<p><?=$blogPost->get('content');?></p>
					</div>
					<hr>
				</div>
			</div>
		<?}
	}else{?>
		<div class="alert alert-info" role="alert">
			<strong>Oh no!</strong> There are no posts<?=isset($month) ? " for " . $month : "";?>.
		</div>
	<?}?>
</div>
<?require('footer.php');?>