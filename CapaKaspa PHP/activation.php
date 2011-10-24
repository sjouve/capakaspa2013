<?
session_start();
/* load settings */
if (!isset($_CONFIG))
	require 'config.php';

/* connect to database */
require 'connectdb.php';
require 'gui_rss.php';
require 'bwc_players.php';
require 'bwc_games.php';
	
/* Traitement des actions */
$err=1;
$ToDo = isset($_POST['ToDo']) ? $_POST['ToDo']:$_GET['ToDo'];

switch($ToDo)
{
	case 'Valider':
		$err = activationRequest($_POST['txtNick'], $_POST['txtPassword'], $_POST['txtEmail']);
		break;
}	

$titre_page = "Echecs en différé - Activation du compte";
$desc_page = "Activez votre compte pour accéder à la zone de jeu d'échecs en différé et jouer des parties à votre rythme.";
require 'page_header.php';
$image_bandeau = 'bandeau_capakaspa_global.jpg';
$barre_progression = "<a href='/'>Accueil</a> > Echecs en différé > Activation du compte";
require 'page_body.php';
?>
  <div id="content">
    <div class="blogbody">
    <?/* Traiter les erreurs */
		if ($err == 0)
			echo("<div class='error'>Le compte n'existe pas</div>");
		if ($err == -1)
			echo("<div class='error'>Un problème technique a empêché l'envoi du message</div>");
		if ($err == -2)
			echo("<div class='error'>L'adresse de messagerie est déjà utilisée</div>");
		if ($err == -3)
			echo("<div class='error'>L'adresse de messagerie est invalide</div>");
			
	?>
	<? if ($err == 1 && $ToDo == 'Valider') {?>
		<b>Un message d'activation a été envoyé à l'adresse de messagerie indiquée.</b>
	<? } else {?>
	<b>Votre compte n'est pas activé.</b>
	<p>Vous êtes sûrement dans l'un des cas suivant :
		<ul>
		<li>Vous avez reçu le message contenant le lien d'activation de votre compte mais vous ne l'avez pas encore utilisé,</li>
		<li>vous n'avez pas reçu de message contenant le lien d'activation.</li>
		</ul>
     </p>
	 <p>Dans ce dernier cas, vous pouvez utiliser le formulaire ci-dessous pour mettre à jour votre adresse de messagerie associée à votre compte et relancer le processus d'activation.</p>
	 <br/>
	 <form name="userdata" method="post" action="activation.php">
			<table align="center">
				<tr>
		            <td> Surnom : </td>
		            <td><input name="txtNick" type="text" size="20" maxlength="20" value="<?echo($_POST['txtNick']);?>">
		            </td>
		        </tr>
		        <tr>
					<td> Passe : </td>
		            <td><input name="txtPassword" type="password" size="16" maxlength="16" value="<?echo($_POST['txtPassword']);?>">
		            </td>
		        </tr>
		        <tr>
					<td> Email : </td>
		            <td><input name="txtEmail" type="text" size="50" maxlength="50" value="<?echo($_POST['txtEmail']);?>">
		            </td>
		        </tr>
			</table>
	
			<center><input name="ToDo" value="Valider" type="submit"></center>
		</form> 
		<br/>
		<? } ?>
		<br/>
		<br/>
      <h3>En direct du forum ... <?displayIconRSS(URL_RSS_FORUM);?></h3>
      
      	<?
				displayBodyRSS(URL_RSS_FORUM, 2);
		?>
			
			
          <h3>En direct du blog ... <?displayIconRSS(URL_RSS_BLOG);?></h3>
      	<?
			displayBodyRSS(URL_RSS_BLOG, 2);
		?>
      <br />
    </div>
  </div>
  <div id="rightbar">
    <div class="navlinks">
    
		<div class="title">Statistiques</div>
		  <ul>
			<li><img src="images/hand.gif" /> Parties en cours : <? echo(getNbActiveGameForAll())?></li>
			<li><img src="images/joueur_actif.gif" /> Joueurs actifs : <? echo(getNbActivePlayers())?></li>
			<li><img src="images/joueur_passif.gif" /> Joueurs passifs : <? echo(getNbPassivePlayers())?></li>
		  </ul>
		
	
    	<object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,29,0" width="180" height="150">
		  <param name="movie" value="images/capakaspa.swf" />
		  <param name="quality" value="high" />
		  <embed src="images/capakaspa.swf" quality="high" pluginspage="http://www.macromedia.com/go/getflashplayer" type="application/x-shockwave-flash" width="180" height="150"></embed>
		</object>
	

 	</div>
 </div>

<?
    require 'page_footer.php';
    mysql_close();
?>
