</head>
<body <?echo(isset($attribut_body) ? $attribut_body:"")?>>

<? 
if (isset($toPlayerID))
	displayPrivateMessage($toPlayerID, $toFirstName, $toLastName, $toNick, $toEmail);
?>

<div id="topbar">
	<div id="container">
		<span class="title"><a href="index.php" title="<?php echo _("CapaKaspa : Play chess and share your games");?>"><?php echo _("CapaKaspa");?></a></span>
		<span class="social"><? if (isset($_SESSION['playerID'])&&$_SESSION['playerID']!=-1) {?>
	      <form name="logout" action="index.php" method="post">
		        <input type="hidden" name="ToDo" value="Logout">
		        <input type="submit" value="<?php echo _("Sign out");?>" class="button">
		  </form>
	  	<? } ?>
	  	</span>
		<span class="social"><a href="http://www.facebook.com/capakaspa" target="_blank"><img src="images/icone_facebook.png" alt="<?php echo _("Follow us on Facebook");?>" title="<?php echo _("Follow us on Facebook");?>" width="32" height="32" style="border: 0;"/></a>
		<a href="http://www.twitter.com/CapaKaspa" target="_blank"><img src="http://twitter-badges.s3.amazonaws.com/t_logo-a.png" title="<?php echo _("Follow us on Twitter");?>" alt="<?php echo _("Follow us on Twitter");?>" width="32" height="32" style="border: 0;"/></a>
		<a href="https://plus.google.com/114694270583726807082/?prsrc=3" style="text-decoration: none;" target="_blank"><img src="https://ssl.gstatic.com/images/icons/gplus-32.png" title="<?php echo _("Follow us on Google+");?>" alt="<?php echo _("Follow us on Google+");?>" width="32" height="32" style="border: 0;"/></a>
		<a href="http://www.youtube.com/user/CapaKaspaEchecs?feature=creators_cornier-http%253A%2F%2Fs.ytimg.com%2Fyt%2Fimg%2Fcreators_corner%2FYouTube%2Fyoutube_32x32.png" target="_blank"><img src="http://s.ytimg.com/yt/img/creators_corner/YouTube/youtube_32x32.png" title="<?php echo _("Follow us on YouTube");?>" alt="<?php echo _("Follow us on YouTube");?>"/></a><img src="http://www.youtube-nocookie.com/gen_204?feature=creators_cornier-http%3A//s.ytimg.com/yt/img/creators_corner/YouTube/youtube_32x32.png" style="display: none"/>
		</span>
	</div>
</div>

<div id="container">
	
<div id="leftbar">
    <div class="navlinks">
    	<div id="connexion">
	      <? if (!isset($_SESSION['playerID'])||$_SESSION['playerID']==-1) {?>
	      <form method="post" action="index.php">
	        
	        <div class="item"><input name="txtNick" type="text" size="20" maxlength="20" placeholder="<?php echo _("User name");?>"/> </div>
	        <div class="item"><input name="pwdPassword" type="password" size="20" maxlength="16" placeholder="<?php echo _("Password");?>"/> </div>
	        <?if (isset($_GET['err'])&&$_GET['err']=='login') {?>
	        <div class='error'><? echo _("Invalid user name or password !");?></div>
	        <?}?>
	        <div class="item"><input name="chkAutoConn" type="checkbox"/> <? echo _("Remember me");?></div>
	        <input name="ToDo" value="Login" type="hidden" /><input name="login" value="<? echo _("Sign in");?>" type="submit" class="button"/>
	      </form>
	      <ul>
	      	<li><img src="images/puce.gif"/> <a href="password.php"><? echo _("Forgot password ?");?></a></li>
	      </ul>
	      <? } else {?>
	        <div class="item">
	        	<a href="player_view.php?playerID=<?echo($_SESSION['playerID'])?>"><img src="<?echo(getPicturePath($_SESSION['socialNetwork'], $_SESSION['socialID']));?>" width="40" height="40" border="0" style="float: left;margin-right: 10px;"/></a> 
	        	<b><a href="player_view.php?playerID=<?echo($_SESSION['playerID'])?>"><? echo($_SESSION['firstName'])?> <? echo($_SESSION['lastName'])?> (<? echo($_SESSION['nick'])?>)</a></b>
	        	<br><a href="player_update.php"><? echo _("Update info");?></a>
	        </div>
	        <div class="item"></div>
	      <? } ?>
		</div>
	</div>
		
	<? if (isset($_SESSION['playerID'])&&$_SESSION['playerID']!=-1) {?>
	<div class="navlinks">
	<div class="title"><?php echo _("Chess games");?></div>		
      <ul>
        <li id="menu1"><img src="images/puce.gif"/> <a href="game_new.php"><?php echo _("New game");?></a></li>
        <li id="menu2"><img src="images/puce.gif"/> <a href="index.php"><?php echo _("My games in progress");?></a></li>
		<li id="menu3"><img src="images/puce.gif"/> <a href="game_list_ended.php"><?php echo _("My games ended");?></a></li>
        <!-- <li><img src="images/puce.gif"/> <a href="game_list_all.php"></a></li> -->
		<!-- <li><img src="images/icone-mobile.png"/> <a href="http://mobile.capakaspa.info">Version mobile</a></li> -->
      </ul>  
	</div>
	<div class="navlinks">
	<div class="title"><?php echo _("Players");?></div>		
      <ul>
        <li id="menu4"><img src="images/puce.gif"/> <a href="activity.php"><?php echo _("News feed");?></a></li>
		<li id="menu5"><img src="images/puce.gif"/> <a href="player_search.php"><?php echo _("Search");?></a></li>
		<li id="menu6"><img src="images/puce.gif"/> <a href="message.php"><?php echo _("Messages");?></a></li>      
      </ul>  
	</div>
	<? } ?>
	 
	<? if (!isset($_SESSION['playerID'])||$_SESSION['playerID']==-1) {?>
	<div class="navlinks">
		<div class="title"><?php echo _("Statistics");?></div>
	  		<ul>
				<li><img src="images/hand.gif" /> <?php echo _("Games in progress");?> : <? echo(getNbActiveGameForAll())?></li>
				<li><img src="images/joueur_actif.gif" /> <?php echo _("Active players");?> : <? echo(getNbActivePlayers())?></li>
				<li><img src="images/joueur_passif.gif" /> <?php echo _("Passive players");?> : <? echo(getNbPassivePlayers())?></li>
				<li><img src="images/user_online.gif" /> <?php echo _("Online players");?> : <? echo(getNbOnlinePlayers())?></li>
	  		</ul>
	</div>
	<? } ?>
	<? if (isset($_SESSION['playerID'])&&$_SESSION['playerID']!=-1) {?>
	<div class="navlinks">
		<div class="title"><?php echo _("Applications");?></div>
			<ul>
		        <li id="menu7"><img src="images/puce.gif"/> <a href="app_jchess.php"><?php echo _("Play JChess");?></a></li>
		        <li id="menu8"><img src="images/puce.gif"/> <a href="app_flashchess.php"><?php echo _("Play flashChess");?></a></li>
      		</ul>
	</div>
	<? } ?>
</div>

<div id="advertisement">
	<div class="leaderboard">
		<script type="text/javascript"><!--
	      google_ad_client = "ca-pub-8069368543432674";
	      /* CapaKaspa Leaderboard */
	      google_ad_slot = "6207727366";
	      google_ad_width = 728;
	      google_ad_height = 90;
	      //-->
		</script>
		<script type="text/javascript"
	      src="http://pagead2.googlesyndication.com/pagead/show_ads.js">
		</script>
	</div>
	
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