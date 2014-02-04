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
		<?echo _("A problem with a game ? A question about the site ?");?><br>
		<?echo _("To contact us send an email at capakaspa@capakaspa.info");?>
		</p>
		<h2><?echo _("Frequently Asked Questions");?></h2>
		
		<h4><?echo _("Is that the site is compatible with all browsers ?");?></h4>
		<?echo _("No. The site is compatible with all recent browsers. It does not work with Internet Explorer under version 9.");?>
		
		<h4><?echo _("How is calculated the chess ranking of players on the site ?");?></h4>
		<?echo _("Elo ranking is calculated monthly and takes into account the classic chess games completed during the month past.");?>

		<h4><?echo _("How to share chess games ?");?></h4>
		<?echo _("You can share invitations, results and move with yours followers.");?><br>
		<?echo _("By default invitations and results are shared. You can stop sharing on your profile page.");?><br>
		<?echo _("To share a move check the share option before validate your move.");?><br>		
	</div>
</div>
<?
require 'include/page_footer.php';
?>
