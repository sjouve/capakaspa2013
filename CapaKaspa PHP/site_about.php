<?	
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
		<h4><?echo _("About");?><h4>
		<p>
		<?echo _("CapaKaspa is a website edited by Sébastien JOUVE (<a href='http://www.netassistant.fr'>NetAssistant</a>).");?>
		</p>
		<p>
		<?echo _("Official data");?> :<br>
		Lentilly, 69210<br>
		FRANCE<br>
		No SIRET : 53523014800020<br>
		Code NAF/APE : 6201Z<br>
		</p>
		
		<h4><?echo _("Contact");?><h4>
		<p>
		<?echo _("To contact us send an email at capakaspa@capakaspa.info");?>
		</p>
	</div>
</div>
<?
require 'include/page_footer.php';
?>