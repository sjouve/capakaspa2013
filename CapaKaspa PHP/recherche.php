<?
	session_start();
	$titre_page = 'Résultat de la recherche';
    require 'page_header.php';
    $image_bandeau = 'bandeau_capakaspa_global.jpg';
    $barre_progression = "Résultat de la recherche";
    require 'page_body.php';
?>
    <div id="contentlarge">
    <div class="blogbody">
	 <!-- Google Search Result Snippet Begins -->

    <div id="cse-search-results"></div>
    <script type="text/javascript">
      var googleSearchIframeName = "cse-search-results";
      var googleSearchFormName = "cse-search-box";
      var googleSearchFrameWidth = 800;
      var googleSearchDomain = "www.google.fr";
      var googleSearchPath = "/cse";
    </script>
    <script type="text/javascript" src="http://www.google.com/afsonline/show_afs_search.js"></script>
    
    <!-- Google Search Result Snippet Ends -->
        
      </div>
	  </div>
<?
    require 'page_footer.php';
?>
