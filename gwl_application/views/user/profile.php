<ul class="breadcrumb">
    <li itemscope="itemscope" itemtype="http://data-vocabulary.org/Breadcrumb"><span itemprop="title"><a href="<?php echo $baseUrl ?>">Home</a></span></li>       
    <li itemscope="itemscope" itemtype="http://data-vocabulary.org/Breadcrumb" class="active"><span itemprop="title"><?php echo $user->Username ?></span></li>
</ul>

<h2><?php echo $user->Username ?></h2>

<div class="row">
	<div class="col-sm-4">
		<?php 
			if($user->ProfileImage != null) 
			{
				echo "<img src='" . $baseUrl . '/uploads/' . $user->ProfileImage . "' class='largeProfileImage gameBoxArt' />";
			}
			if($user->Bio != null) 
			{
				echo "<div class='userBio'>" . $user->Bio . "</div>";
			}
		?>
	</div>
	<div class="col-sm-8"> 
		<ul class="nav nav-tabs profileTabs">
			<li class="active"><a href="">Feed</a></li>
			<li><a href="">Collection</a></li>
			<?php 
				if($sessionUserID != null && $sessionUserID == $user->UserID) 
				{
					echo "<li><a href='" . $baseUrl . "user/edit'>Settings</a></li>";
				} 
			?>
		</ul>  
    	<?php 
	    	foreach ($events as $event)
			{
		?>
		<div class="panel panel-default"> 
            <div class="panel-body media">
				<a class='pull-left' href='#'>
					<img class='media-object gameBoxArt eventImage' src='<?php echo $event->ImageSmall ?>' />
				</a>
				<div class="media-body">
					<?php
			    		echo '<p><b>' . $event->Username;
			    		
			    		$eventItems = array();
						if($event->CurrentlyPlaying) array_push($eventItems, ' is playing');
			    		if($event->ListID != null) array_push($eventItems, ' <span class="label label-' . $event->ListStyle . '">' . $event->ListThirdPerson . '</span>');
				    	if($event->StatusID != null) array_push($eventItems, ' <span class="label label-' . $event->StatusStyle . '">' . $event->StatusThirdPerson . '</span>');
				    	$i = 1;
				    	foreach($eventItems as $item) 
				    	{
				    		echo $item;
			    			if($i === count($eventItems)-1) {
							    echo " and ";
							} else if($i < count($eventItems)) {
							    echo ", ";
							}
							$i++;
				    	}
				    	
				    	echo ' <a href="' . $baseUrl . 'game/' . $event->GBID . '">' . $event->Name . '</a></b>';
			    		
			    		if(count($event->platforms) > 0) {
			    			echo " on ";
			    			$i = 1;
				    		foreach($event->platforms as $platfrom)
				    		{
				    			echo $platfrom->Abbreviation;
				    			if($i !== count($event->platforms)) {
								    echo ", ";
								}
								$i++;
				    		}
				    	}

				    	echo '</p>';
			    		echo '<p class="gameDeck">' . $event->Deck . '</p>';
			    		echo '<p class="datestamp">' . timespan(human_to_unix($event->DateStamp), time()) . ' ago</p>';
					?>
				</div>
			</div>
		</div>
		<?php
			}
    	?>
    </div>
</div>