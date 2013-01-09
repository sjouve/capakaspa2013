<?
	session_start();
	$titre_page = "Résultat de la recherche";
	$desc_page = "Résultat d'une recherche sur les sites des échecs CapaKaspa : la zone de jeu d'échecs, le blog et le forum.";
    require 'page_header.php';
    require 'page_body_no_menu.php';
?>
    <div id="contentxlarge">
    	<div class="blogbody">
    		<center>
    		
	 		<div id="cse-search-results"></div>
			<script type="text/javascript">
			  var googleSearchIframeName = "cse-search-results";
			  var googleSearchFormName = "cse-search-box";
			  var googleSearchFrameWidth = 800;
			  var googleSearchDomain = "www.google.fr";
			  var googleSearchPath = "/cse";
			</script>
			<script type="text/javascript" src="http://www.google.com/afsonline/show_afs_search.js"></script>
      		</center>  
      	</div>
	  </div>
<?
    require 'page_footer.php';
?>
