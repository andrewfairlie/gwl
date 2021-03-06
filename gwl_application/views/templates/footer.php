    <hr>

    <footer>
      <p>Website created by <a href="http://www.clidus.com/" target="_blank">Joshua Marketis</a>. Logo design by <a href="http://www.drugs4kids.com/" target="_blank">Michael Orson</a>. Video game data provided by the top men at <a href="http://www.giantbomb.com" target="_blank">Giant Bomb</a>.</p>
    </footer>

  </div>

  <!-- Javascript-->
  <script>
    <?php 
      echo "var baseUrl = '" . $baseUrl . "';";
      if($pagetemplate == "Collection") {
        echo "var UserID = " . $user->UserID . ";";
      } 
    ?>
  </script>
  <script src="<?php echo $baseUrl ?>js/jquery-2.0.3.min.js"></script>
  <script src="<?php echo $baseUrl ?>bootstrap/js/bootstrap.min.js"></script>
  <script src="<?php echo $baseUrl ?>/js/global.js"></script>
  <?php 
    if($pagetemplate == "Search" || $pagetemplate == "Game") 
    { 
      echo "<script src='" . $baseUrl . "js/game.js'></script>"; 
    }
    else if($pagetemplate == "Admin") 
    { 
      echo "<script src='" . $baseUrl . "js/admin.js'></script>"; 
    } 
    else if($pagetemplate == "Collection") 
    { 
      echo "<script src='" . $baseUrl . "js/collection.js'></script>"; 
    } 
    if($pagetemplate == "User" || $pagetemplate == "Game")
    {
      echo "<script src='" . $baseUrl . "js/jquery.autogrow-textarea.js'></script>"; 
      echo "<script src='" . $baseUrl . "js/comments.js'></script>"; 
    }
  ?>
  <script>
    $(function() {
      <?php
          switch($pagetemplate)
          {
            case "Game":
              echo "$('.textAreaAutoGrow').autogrow();";
              break;
            case "User":
              echo "$('.textAreaAutoGrow').autogrow();";
              echo "$('#navFeed').addClass('active');";
              break;
            case "Collection":
              echo "loadCollection();";
              echo "$('#navCollection').addClass('active');";
              break;
            case "Settings":
              echo "$('#dateFormat').val('" . $user->DateTimeFormat . "');";
              echo "$('#navSettings').addClass('active');";
              break;
            case "ImageUpload":
            case "Password":
              echo "$('#navSettings').addClass('active');";
              break;
          }
      ?>
    });
    
    (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
    (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
    m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
    })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

    ga('create', 'UA-19451189-7', 'gamingwithlemons.com');
    ga('send', 'pageview');
  </script>
</body>
</html>
