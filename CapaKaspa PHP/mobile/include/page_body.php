<script type="text/javascript">

  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', 'UA-2529972-9']);
  _gaq.push(['_trackPageview']);

  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();

</script>
</head>
<body <?echo(isset($attribut_body)?$attribut_body:"")?>>
<?
if (isset($_SESSION['playerID']) && $_SESSION['playerID']!=-1)
{
	//$nbUnreadMessages = countUnreadPM($_SESSION['playerID']);
	$nbTurns = getNbGameTurns($_SESSION['playerID']);
	$nbUnreadNews = countUnreadActivity($_SESSION['playerID']);
	$nbUnreadNotif = countUnreadNotification($_SESSION['playerID']);
}
?>
<div id="top">
	<a href="player_view.php?playerID=<?php echo $_SESSION['playerID'];?>"><img src="images/icon_home.gif"> CapaKaspa</a>
	<?
	if (isset($_SESSION['playerID']) && $_SESSION['playerID']!=-1)
	{?>
	<span style="float: right; margin-right: 20px;"><a href="activity_notification.php"><img height="18px" width="18px" src="images/notification.png" style="vertical-align: -2px;"/><? if ($nbUnreadNotif > 0) echo("<span class='newplayer' style='font-size: 9px; padding-left: 2px; padding-right: 2px;border-radius: 6px;' title='"._("Unread notifications")."'>".$nbUnreadNotif."</span>");?></a></span>
	<?}?>
</div>
<div id="advertisement">
	<center>
		<script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
		<!-- CapaKaspa Mobile Adaptable Top -->
		<ins class="adsbygoogle"
		     style="display:block"
		     data-ad-client="ca-pub-8069368543432674"
		     data-ad-slot="5941854862"
		     data-ad-format="auto"></ins>
		<script>
		(adsbygoogle = window.adsbygoogle || []).push({});
		</script>
	</center>
</div>
<?
if (isset($_SESSION['playerID']) && $_SESSION['playerID']!=-1)
{
?>
<div id="onglet">
		<table width="100%" cellpadding="0" cellspacing="0">
		<tr>
			<? if ($activeMenu == 10) {?>
			<td><div class="ongletenable"><? echo _("Games"); if ($nbTurns > 0) echo(" <span class='newplayer' title='"._("Moves to play")."'>".$nbTurns."</span>");?></div></td>
			<? } else {?>
			<td><div class="ongletdisable" onclick="location.href='game_in_progress.php'"><a href="game_in_progress.php"><? echo _("Games"); if ($nbTurns > 0) echo(" <span class='newplayer' title='"._("Moves to play")."'>".$nbTurns."</span>");?></a></div></td>
			<? }?>
			
			<? if ($activeMenu == 20) {?>
			<td><div class="ongletenable"><? echo _("Tournaments");?></div></td>
			<? } else {?>
			<td><div class="ongletdisable" onclick="location.href='tournament_list.php'"><a href="tournament_list.php"><? echo _("Tournaments");?></a></div></td>
			<? }?>
			
			<? if ($activeMenu == 30) {?>
			<td><div class="ongletenable"><? echo _("News"); if ($nbUnreadNews > 0) echo(" <span class='newplayer' title='"._("Unread news")."'>".$nbUnreadNews."</span>");?></div></td>
			<? } else {?>
			<td><div class="ongletdisable" onclick="location.href='activity.php'"><a href="activity.php"><? echo _("News"); if ($nbUnreadNews > 0) echo(" <span class='newplayer' title='"._("Unread news")."'>".$nbUnreadNews."</span>");?></a></div></td>
			<? }?>
			
			<? if ($activeMenu == 40) {?>
			<td><div class="ongletenable"><? echo _("Players"); echo(" <span class='newplayer' title='"._("Online players")."'>".getNbOnlinePlayers()."</span>");?></div></td>
			<? } else {?>
			<td><div class="ongletdisable" onclick="location.href='player_search.php'"><a href="player_search.php"><? echo _("Players"); echo(" <span class='newplayer' title='"._("Online players")."'>".getNbOnlinePlayers()."</span>");?></a></div></td>	
			<? }?>
		</tr>
		</table>
</div>
<? }?>