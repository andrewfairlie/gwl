<ul class="breadcrumb">
    <li itemscope="itemscope" itemtype="http://data-vocabulary.org/Breadcrumb"><span itemprop="title"><a href="<?php echo $baseUrl ?>">Home</a></span></li>       
    <li itemscope="itemscope" itemtype="http://data-vocabulary.org/Breadcrumb" class="active"><span itemprop="title">Blog</span></li>
</ul>

<?php
	foreach($posts as $post)
	{
?>
	<h2><a href="<?php echo $baseUrl . "blog/" . $post->URL ?>"><?php echo $post->Title ?></a></h2>
	<p>By <a href="<?php echo $baseUrl . 'user/' . $post->UserID; ?>"><?php echo $post->Username ?></a></p>

	<div class="clearfix">
		<?php echo $post->Post ?>
	</div>

	<p class="postFooter">Posted <?php echo date_format(date_create($post->Date), 'jS F, Y') ?></p>
<?php
	}
?>