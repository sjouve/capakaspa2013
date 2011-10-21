<?
	session_start();
	require 'gui_rss.php';
	$titre_page = 'About Chess - Informations et articles';
    require 'page_header.php';
    $image_bandeau = 'bandeau_capakaspa_global.jpg';
    $barre_progression = "Découvrir les échecs > About Chess";
    require 'page_body.php';
?>
  <div id="content">
    <div class="blogbody">
      
	<h3><img src="images/abSb.gif" /> <?displayIconRSS(URL_RSS_ABOUT);?></h3>
		  	<?
				displayBodyRSS(URL_RSS_ABOUT, 10);
			?>
    </div>
  </div>
  <div id="rightbar">
  <div class="navlinks">
  	
      <div class="title"><img src="images/abSb.gif" /> populaires</div>
      <ul>
        <?
				displayBarRSS(URL_RSS_ABOUT_POP, 10);
			?>
      </ul>
   
      <div class="title"><img src="images/abSb.gif" /> Actifs </div>
      <ul>
        <?
				displayBarRSS(URL_RSS_ABOUT_ACT, 10);
			?>
      </ul>
   </div>
   </div>
<?
    require 'page_footer.php';
?>
