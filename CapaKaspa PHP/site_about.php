<?
session_start();
require 'include/localization.php';
$titre_page = _("About and contact");
$desc_page = _("All details about us");
require 'include/page_header.php';
?>
<script type="text/javascript">

</script>
<?
require 'include/page_body_no_menu.php';
?>
<div id="contentlarge">
    <div class="contentbody">
		<h2><?echo _("About and contact");?></h2>
		<h4><?echo _("About");?></h4>
		<p>
		<?echo _("CapaKaspa is a website edited by Sébastien JOUVE (<a href='http://www.netassistant.fr'>NetAssistant</a>). It's a website to play chess games and share them.
		</p>");?>
		</p>
		<p>
		<?echo _("Official data");?> :<br>
		Lyon, 69003<br>
		FRANCE<br>
		</p>
		
		<h4><?echo _("Contact");?></h4>
		<p>
		<?echo _("To contact us send an email at capakaspa@capakaspa.info");?>
		</p>
		
		<h4><?echo _("Credits");?></h4>
		<p>
		<?echo _("CapaKaspa works with some PHP libraries")." : 
				<br>- "._("an old version of")." WebChess (http://webchess.sourceforge.net), 
				<br>- JpGraph (http://jpgraph.net/), 
				<br>- Securimage (http://www.phpcaptcha.org/), 
				<br>- pgn4web (http://pgn4web.casaschi.net),
				<br>- phpthumb (http://phpthumb.gxdlabs.com)";?>
		</p>
	</div>
</div>
<?
require 'include/page_footer.php';
?>
