<ul class="breadcrumb">
    <li itemscope="itemscope" itemtype="http://data-vocabulary.org/Breadcrumb"><span itemprop="title"><a href="<?php echo $baseUrl ?>">Home</a></span></li>   
    <li itemscope="itemscope" itemtype="http://data-vocabulary.org/Breadcrumb"><span itemprop="title"><a href="<?php echo $baseUrl ?>index.php/admin">Admin</a></span></li>    
    <?php if($post->PostID > 0) { ?>
    <li itemscope="itemscope" itemtype="http://data-vocabulary.org/Breadcrumb"><span itemprop="title"><a href="<?php echo $baseUrl ?>index.php/admin/blog/edit">Edit Blog Post</a></span></li>    
    <?php } ?>
    <li itemscope="itemscope" itemtype="http://data-vocabulary.org/Breadcrumb" class="active"><span itemprop="title"><?php echo $pagetitle ?></span></li>
</ul>

<h2><?php echo $pagetitle ?></h2>

<?php 
	if($formSuccess) {
		echo "<div class='alert alert-success'>Success! Your post is up!</div>";
	} else {
		echo validation_errors(); 
	}
?>
<form action="<?php echo $baseUrl ?>index.php/admin/blog/<?php echo $formType ?>" method="post" accept-charset="utf-8" role="form">
	<div class="form-group"> 
	    <label for="title">Title</label>
	    <input class="form-control" type="text" name="title" value="<?php echo $post->Title ?>">
	</div>
    <div class="form-group"> 
		<label for="url">URL</label>
    	<input class="form-control" type="text" name="url" value="<?php echo $post->URL ?>">
    </div>
    <div class="form-group"> 
    	<textarea id="blogPostTextArea" placeholder="Enter your text ..." class="form-control" name="post"><?php echo $post->Post ?></textarea>
    </div>
    <button type="submit" class="btn btn-default">Post</button>
    <?php if($post->PostID > 0) { ?>
    <a onclick="javascript:deleteBlogPost(<?php echo $post->PostID ?>);"><div class="btn btn-danger pull-right">Delete</div></a>
    <?php } ?>
</form>