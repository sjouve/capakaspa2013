<?
session_start();
require 'include/localization.php';
$titre_page = _("Help");
$desc_page = _("Frequently Asked Questions about how to play chess on CapaKaspa");
require 'include/page_header.php';
?>
<script type="text/javascript">

</script>
<?
require 'include/page_body_no_menu.php';
?>
<div id="contentlarge">
    <div class="contentbody">
		<p>
		<?echo _("A problem ? A question ?");?><br>
		<?echo _("To contact us send an email at capakaspa@capakaspa.info");?>
		</p>
		<h2><?echo _("Frequently Asked Questions");?></h2>
		
		<h4><?echo _("How is the ranking of players on the site ?");?></h4>
		<?echo _("Elo ranking is calculated quarterly and takes into account the classic games completed during the quarter past.");?>
		
	</div>
</div>
<?
require 'include/page_footer.php';
?>
