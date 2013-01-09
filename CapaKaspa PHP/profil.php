<?	require 'mobilecheck.php';
	session_start();

	/* load settings */
	if (!isset($_CONFIG))
		require 'config.php';

	/* load external functions for setting up new game */
	require_once('bwc/bwc_chessutils.php');
	
	/* connect to database */
	require 'connectdb.php';
	
	require 'dac/dac_games.php';
	require 'bwc/bwc_players.php';
	
	/* check session status */
	require 'sessioncheck.php';
	
	$err = 1;
	$ToDo = isset($_POST['ToDo']) ? $_POST['ToDo']:Null;
	switch($ToDo)
	{
		
		case 'UpdateProfil':
			
			$err = updateProfil($_SESSION['playerID'], $_POST['pwdPassword'], $_POST['pwdOldPassword'], strip_tags($_POST['txtFirstName']), strip_tags($_POST['txtLastName']), $_POST['txtEmail'], strip_tags($_POST['txtProfil']), strip_tags($_POST['txtSituationGeo']), $_POST['txtAnneeNaissance'], $_POST['rdoTheme'], $_POST['txtEmailNotification'], $_POST['rdoSocialNetwork'], $_POST['txtSocialID']);
			break;
			
		case 'CreateVacation':
		
			$err = createVacation($_SESSION['playerID'], $_POST['nbDays'], $CFG_EXPIREGAME);	
			break;
			
	}
		
 	$titre_page = "Echecs en différé - Modifier votre profil";
 	$desc_page = "Jouer aux échecs en différé. Modifier votre profil de joueur de la zone de jeu d'échecs en différé";
    require 'page_header.php';
?>
<script type="text/javascript" src="javascript/formValidation.js">
 /* fonctions de validation des champs d'un formulaire */
</script>
<script type="text/javascript">
		function validatePersonalInfo()
		{
			var dayDate = new Date();
			var annee = dayDate.getFullYear();
			if (isEmpty(document.Profil.txtFirstName.value)
				|| isEmpty(document.Profil.txtLastName.value)
				|| isEmpty(document.Profil.txtSituationGeo.value)
				|| isEmpty(document.Profil.txtProfil.value)
				|| isEmpty(document.Profil.txtAnneeNaissance.value))
			{
				alert("Toutes les informations personnelles sont obligatoires.");
				return;
			}
			
			if (!isNumber(document.Profil.txtAnneeNaissance.value) || !isWithinRange(document.Profil.txtAnneeNaissance.value, 1900, annee))
			{
				alert("L'année de naissance est un nombre à 4 chiffres compris entre 1900 et l'année courante.");
				return;
			}
			
			if (!isEmpty(document.Profil.pwdPassword.value)
				&& isEmpty(document.Profil.pwdOldPassword.value))
			{
				alert("Vous devez saisir votre ancien mot de passe.");
				return;
			}
			
			if (!isEmpty(document.Profil.pwdPassword.value) && !isAlphaNumeric(document.Profil.pwdPassword.value))
			{
				alert("Le mot de passe doit être alphanumérique.");
				return;
			}
			
			if (document.Profil.pwdPassword.value == document.Profil.pwdPassword2.value)
				document.Profil.submit();
			else
				alert("Vous avez fait une erreur de saisie de mot de passe.");
		}
		
		function validateVacation()
		{
			if (!isWithinRange(document.Vacation.nbDays.value, 1, 30))
			{
				alert("Le nombre de jours doit être compris entre 0 et 30.");
				return;
			}
			var vok=false;
			vok = confirm("L'ajout de cette absence ne peut être annulée et toutes vos parties seront immédiatement ajournées. Veuillez confirmer sa prise en compte ?");
			if (vok)
			{
				document.Vacation.submit();
			}
		}
	</script>
<?
    $image_bandeau = 'bandeau_capakaspa_zone.jpg';
    $barre_progression = "<a href='/'>Accueil</a> > Echecs en différé > Mon profil";
    require 'page_body.php';
?>
  <div id="contentlarge">
    <div class="blogbody">
      
      	<?
      	if ($err == 0)
				echo("<div class='error'>Un problème technique a empêché l'opération</div>");
		if ($ToDo == 'UpdateProfil')
      	{
			
			if ($err == -1)
				echo("<div class='error'>Votre ancien mot de passe n'est pas celui que vous avez saisi</div>");
			if ($err == 1)
				echo("<div class='success'>Les modifications de votre profil ont bien été enregistrées</div>");
		}
		if ($ToDo == 'CreateVacation')
		{
			if ($err == -100)
				echo("<div class='error'>Le nombre de jours d'absence que vous demandez n'est pas valide</div>");
			if ($err == 1)
				echo("<div class='success'>Votre demande d'absence a bien été enregistrée</div>");
		}
		?>
      <form name="Profil" action="profil.php" method="post">
	  <h3>Mes informations personnelles</h3>
        <table border="0" width="650">
          <tr>
            <td width="180"> Surnom : </td>
            <td><? echo($_SESSION['nick']); ?> (<? echo($_SESSION['elo']); ?>)
            </td>
          </tr>
		  <tr>
            <td width="180"> Prénom : </td>
            <td><input name="txtFirstName" type="text" size="20" maxlength="20" value="<? echo($_SESSION['firstName']); ?>">
            </td>
          </tr>
          <tr>
            <td> Nom : </td>
            <td><input name="txtLastName" type="text" size="20" maxlength="20" value="<? echo($_SESSION['lastName']); ?>">
            </td>
          </tr>
		  <tr>
            <td> Email : </td>
            <td><? echo($_SESSION['email']); ?><input type="hidden" name="txtEmail" value="<? echo($_SESSION['email']); ?>">
            </td>
          </tr>
		  <tr>
            <td> Situation géographique : </td>
            <td><input name="txtSituationGeo" type="text" size="50" maxlength="50" value="<? echo($_SESSION['situationGeo']); ?>">
            </td>
          </tr>
		  <tr>
            <td> Année de naissance : </td>
            <td><input name="txtAnneeNaissance" type="text" size="4" maxlength="4" value="<? echo($_SESSION['anneeNaissance']); ?>">
            </td>
          </tr>
		  <tr>
            <td> Profil : </td>
            <td><TEXTAREA NAME="txtProfil" COLS="50" ROWS="5"><? echo($_SESSION['profil']); ?></TEXTAREA>
            </td>
          </tr>
          <tr>
            <td> Photo : </td>
            <td>
            	<img src="<?echo(getPicturePath($_SESSION['socialNetwork'], $_SESSION['socialID']));?>" width="50" height="50" style="float: left;margin-right: 30px;"/>
            	Afficher la photo de votre profil :<br/>
            	<input name="rdoSocialNetwork" type="radio" value="" <? if ($_SESSION['socialNetwork']=="") echo("checked");?>> Aucun
            	<input name="rdoSocialNetwork" type="radio" value="FB" <? if ($_SESSION['socialNetwork']=="FB") echo("checked");?>> Facebook
            	<input name="rdoSocialNetwork" type="radio" value="GP" <? if ($_SESSION['socialNetwork']=="GP") echo("checked");?>> Google+
            	<input name="rdoSocialNetwork" type="radio" value="TW" <? if ($_SESSION['socialNetwork']=="TW") echo("checked");?>> Twitter
            </td>
          </tr>
          
          <tr>
            <td>&nbsp;</td>
            <td>ID réseau : <input name="txtSocialID" type="text" size="50" maxlength="100" value="<? echo($_SESSION['socialID']); ?>"> <a href="manuel-utilisateur-jouer-echecs-capakaspa.pdf#page=14" target="_blank"><img src="images/point-interrogation.gif" border="0"/></a>
            </td>
          </tr>
		  <tr>
            <td colspan="2">&nbsp</td>
          </tr>
          <tr>
            <td> Mot de passe : </td>
            <td><input name="pwdOldPassword" size="30" type="password" value="">
            </td>
          </tr>
          <tr>
            <td> Nouveau : </td>
            <td><input name="pwdPassword" size="30" type="password" value="">
            </td>
          </tr>
          <tr>
            <td> Confirmation: </td>
            <td><input name="pwdPassword2" size="30" type="password" value="">
            </td>
          </tr>
          <tr>
            <td colspan="2" align="center">&nbsp            </td>
          </tr>
        </table>
        
      
      <h3>Mes préférences</h3>
      
        <table border="0" width="650">
          <tr>
            <td width="180">Notification par email :</td>
            <td><?
					if ($_SESSION['pref_emailnotification'] == 'oui')
					{
				?>
              <input name="txtEmailNotification" type="radio" value="oui" checked>
              Oui 
              <input name="txtEmailNotification" type="radio" value="non">
              Non (Evènements partie, commentaires et messages)
              <?
					}
					else
					{
				?>
              <input name="txtEmailNotification" type="radio" value="oui">
              Oui 
              <input name="txtEmailNotification" type="radio" value="non" checked>
              Non (Evènements partie, commentaires et messages)
              <?	}
				?>
            </td>
          </tr>
          <tr>
            <td>Thème :</td>
            <td><?
					if ($_SESSION['pref_theme'] == 'beholder')
					{
				?>
              <input name="rdoTheme" type="radio" value="beholder" checked>
              	<img src="images/beholder/white_king.gif" height="30" width="30"/>
				<img src="images/beholder/white_queen.gif" height="30" width="30"/>
				<img src="images/beholder/white_rook.gif" height="30" width="30"/>
				<img src="images/beholder/white_bishop.gif" height="30" width="30"/>
				<img src="images/beholder/white_knight.gif" height="30" width="30"/>
				<img src="images/beholder/white_pawn.gif" height="30" width="30"/> <br>
              <input name="rdoTheme" type="radio" value="plain">
             	<img src="images/plain30x30/white_king.gif" />
				<img src="images/plain30x30/white_queen.gif" />
				<img src="images/plain30x30/white_rook.gif" />
				<img src="images/plain30x30/white_bishop.gif" />
				<img src="images/plain30x30/white_knight.gif" />
				<img src="images/plain30x30/white_pawn.gif" />
              <?
					}
					else
					{
				?>
              <input name="rdoTheme" type="radio" value="beholder">
              	<img src="images/beholder/white_king.gif" height="30" width="30"/>
				<img src="images/beholder/white_queen.gif" height="30" width="30"/>
				<img src="images/beholder/white_rook.gif" height="30" width="30"/>
				<img src="images/beholder/white_bishop.gif" height="30" width="30"/>
				<img src="images/beholder/white_knight.gif" height="30" width="30"/>
				<img src="images/beholder/white_pawn.gif" height="30" width="30"/> <br>
              <input name="rdoTheme" type="radio" value="plain" checked>
				<img src="images/plain30x30/white_king.gif" />
				<img src="images/plain30x30/white_queen.gif" />
				<img src="images/plain30x30/white_rook.gif" />
				<img src="images/plain30x30/white_bishop.gif" />
				<img src="images/plain30x30/white_knight.gif" />
				<img src="images/plain30x30/white_pawn.gif" />
              <?	}
				?>
            </td>
          </tr>
          
          <tr>
            <td colspan="2" align="center"><input name="Update" type="button" value="Valider" onClick="validatePersonalInfo()">
            </td>
          </tr>
        </table>
		<input type="hidden" name="ToDo" value="UpdateProfil">
      </form>
      
      <!-- 
      Gestion des absences
      Le joueur saisie la durée de son congé qui est effectif à partir du lendemain
      On demande confirmation car toute annulation est impossible
      La saisi du congé n'est plus possible pendant la durée d'un congé
      Le solde de congé du joueur est décrémenté du nombre de jour saisi
      Le système enregistre la date de début du congé (date du jour + 1), la durée et la date de fin (date de début + durée)
      
      Lors de la saisie du congé il faut modifier la date du dernier des parties du joueur :
      Pour chaque partie
      	Si pas de congé en cours pour l'adversaire on ajoute la durée du congé saisi +1 à la date du dernier coup
      	Sinon on ajoute la durée du congé saisi - (date de fin du congé en cours de l'adversaire - date de début du congé saisi)      
      
      Tant qu'un des joueurs d'une partie est en congé la partie est gelée (il est impossible de jouer un coup)
       -->
      
      <h3>Gestion des absences <a href="manuel-utilisateur-jouer-echecs-capakaspa.pdf#page=15" target="_blank"><img src="images/point-interrogation.gif" border="0"/></a></h3>
      Vous disposez encore de <b><?echo(countAvailableVacation($_SESSION['playerID']));?> jours</b> d'absence pour l'année <?echo(date('Y'))?> (tous les jours d'une éventuelle absence à cheval sur l'année précédente sont décomptés en <?echo(date('Y'))?>).<br/>
      <br/>
      <?	
      		$tmpVacations = getCurrentVacation($_SESSION['playerID']);
			$nbCurrentVacation = mysql_num_rows($tmpVacations);
			if ($nbCurrentVacation == 0)
				echo("Vous n'avez pas d'absences en cours.");
			else
			{
				$tmpVacation = mysql_fetch_array($tmpVacations, MYSQL_ASSOC);
				echo("Votre avez un absence à prendre en compte du ");
				echo("<b>".$tmpVacation['beginDateF']."</b> ");
    			echo(" au " );
				echo("<b>".$tmpVacation['endDateF']."</b>.");
			}
    	
      		if ($nbCurrentVacation == 0)
      	{
      		
      	?>
      	<br/><br/>
		<form name="Vacation" action="profil.php" method="post">
	  	<?	$tomorrow  = mktime(0, 0, 0, date("m")  , date("d")+1, date("Y")); 
	  		$today = date("d/m/Y", $tomorrow);
	  	?> 
	        Vous souhaitez vous absenter pour <input name="nbDays" size="2" maxlength="2" type="text" value=""> jour(s) <input name="Validate" type="button" value="Valider" onClick="validateVacation()"> à compter du <? echo($today)?> (vos parties seront ajournées immédiatement).
	      	<input type="hidden" name="ToDo" value="CreateVacation">
    	</form>
    	<? }?>
    	<br/>
    	
    	<h3>Statistiques</h3>
		<?
		$dateDeb = date("Y-m-d", mktime(0,0,0, 1, 1, 1990));
		$dateFin = date("Y-m-d", mktime(0,0,0, 12, 31, 2020));
		$countLost = countLost($_SESSION['playerID'], $dateDeb, $dateFin);
		$nbDefaites = $countLost['nbGames'];
		$countDraw = countDraw($_SESSION['playerID'], $dateDeb, $dateFin);
		$nbNulles = $countDraw['nbGames'];
		$countWin = countWin($_SESSION['playerID'], $dateDeb, $dateFin);
		$nbVictoires = $countWin['nbGames'];
		$nbParties = $nbDefaites + $nbNulles + $nbVictoires;
		?>
		<table border="0" width="650">
          <tr>
            <td width="180"> Victoires : </td>
            <td><a href="partiesterminees.php#victoires"><? echo($nbVictoires); ?></a></td>
          </tr>
		  <tr>
            <td> Nulles : </td>
            <td><a href="partiesterminees.php#nulles"><? echo($nbNulles); ?></a></td>
          </tr>
		  <tr>
            <td> Défaites : </td>
            <td><a href="partiesterminees.php#defaites"><? echo($nbDefaites); ?></a></td>
          </tr>
		 </table>	
		 <br/>
		 <img src="graph_elo_progress.php?playerID=<?php echo($_SESSION['playerID']);?>&elo=<?php echo($_SESSION['elo']);?>" width="650" height="250" />
    	
    	
    </div>
  </div>
<?
    require 'page_footer.php';
    mysql_close();
?>
