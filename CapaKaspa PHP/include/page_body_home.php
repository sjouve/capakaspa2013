</head>
<body style="background-color: #EEEEEE">

<? 
if (isset($toPlayerID))
	displayPrivateMessage($toPlayerID, $toFirstName, $toLastName, $toNick, $toEmail);
?>

<div id="topbar">
	<div id="container">
		<span class="title"><a href="index.php" title="<?php echo _("CapaKaspa : Play chess and share your games");?>"><?php echo _("CapaKaspa");?></a></span>
		<span class="social"><? if (isset($_SESSION['playerID'])&&$_SESSION['playerID']!=-1) {?>
	      <form name="logout" action="game_in_progress.php" method="post">
		        <input type="hidden" name="ToDo" value="Logout">
		        <input type="submit" value="<?php echo _("Sign out");?>" class="button">
		  </form>
	  	<? } ?>
	  	</span>
		<span class="social"><a href="http://www.facebook.com/capakaspa"><img src="images/icone_facebook.png" alt="<?php echo _("Follow us on Facebook");?>" title="<?php echo _("Follow us on Facebook");?>" width="32" height="32" style="border: 0;"/></a>
		<a href="http://www.twitter.com/CapaKaspa"><img src="http://twitter-badges.s3.amazonaws.com/t_logo-a.png" title="<?php echo _("Follow us on Twitter");?>" alt="<?php echo _("Follow us on Twitter");?>" width="32" height="32" style="border: 0;"/></a>
		<a href="https://plus.google.com/114694270583726807082/?prsrc=3" style="text-decoration: none;"><img src="https://ssl.gstatic.com/images/icons/gplus-32.png" title="<?php echo _("Follow us on Google+");?>" alt="<?php echo _("Follow us on Google+");?>" width="32" height="32" style="border: 0;"/></a>
		<a href="http://www.youtube.com/user/CapaKaspaEchecs?feature=creators_cornier-http%253A%2F%2Fs.ytimg.com%2Fyt%2Fimg%2Fcreators_corner%2FYouTube%2Fyoutube_32x32.png"><img src="http://s.ytimg.com/yt/img/creators_corner/YouTube/youtube_32x32.png" title="<?php echo _("Follow us on YouTube");?>" alt="<?php echo _("Follow us on YouTube");?>"/></a><img src="http://www.youtube-nocookie.com/gen_204?feature=creators_cornier-http%3A//s.ytimg.com/yt/img/creators_corner/YouTube/youtube_32x32.png" style="display: none"/>
		</span>
	</div>
</div>

<div id="container_home">
<div id="contentxlarge">
	<br>
    <div class="skyscraper">
	  	<script type="text/javascript"><!--
	    google_ad_client = "ca-pub-8069368543432674";
	    /* CapaKaspa Accueil Bandeau Droite */
	    google_ad_slot = "2254640927";
	    google_ad_width = 160;
	    google_ad_height = 600;
	    //-->
	    </script>
	    <script type="text/javascript"
	    src="http://pagead2.googlesyndication.com/pagead/show_ads.js">
	    </script>
  	</div>
</div>
  
    

