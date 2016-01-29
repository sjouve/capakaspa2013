<?
// Activity
function insertActivity($playerID, $type, $entityID, $message, $msgType)
{
	global $dbh;
	$res_activity = mysqli_query($dbh,"INSERT INTO activity (playerID, type, entityID, postDate, message, msgType)
			VALUES (".$playerID.", '".$type."', ".$entityID.", now(), '".$message."', '".$msgType."')");
	return $res_activity;
}

function listActivity($start, $limit, $type, $playerID, $activityID)
{
	global $dbh;
	$tmpQuery = "SELECT A.activityID, A.playerID, A.type entityType, A.entityID, A.msgType, A.message, A.postDate, L.likeID,
					P.firstName, P.lastName, P.nick, P.socialNetwork, P.socialID,
				G.gameID, G.eco, G.position, G.gameMessage, G.lastMove, G.dateCreated, G.type gameType, G.flagBishop, G.flagKnight, G.flagRook, G.flagQueen, E.name ecoName,
				WP.playerID wPlayerID, WP.firstName wFirstName, WP.lastName wLastName, WP.nick wNick, WP.elo wElo, WP.elo960 wElo960, WP.socialNetwork wSocialNetwork, WP.socialID wSocialID,
				BP.playerID bPlayerID, BP.firstName bFirstName, BP.lastName bLastName, BP.nick bNick, BP.elo bElo, BP.elo960 bElo960, BP.socialNetwork bSocialNetwork, BP.socialID bSocialID,
				(SELECT COUNT(commentID) FROM comment WHERE type='".ACTIVITY."' and entityID = A.activityID) nbComment,
				(SELECT COUNT(likeID) FROM like_entity WHERE type='".ACTIVITY."' and entityID = A.activityID) nbLike,
				(SELECT COUNT(gameID) nbMove FROM history H WHERE H.gameID = G.gameID) nbMoves
			FROM activity A 
					LEFT JOIN like_entity L on L.type = '".ACTIVITY."' AND L.entityID = A.activityID AND L.playerID = ".$_SESSION['playerID']." 
					LEFT JOIN games G ON A.entityID = G.gameID AND A.type = '".GAME."' 
					LEFT JOIN eco E ON E.eco = G.eco AND E.ecoLang = '".getLang()."' 
					LEFT JOIN players WP ON G.whitePlayer = WP.playerID 
					LEFT JOIN players BP ON G.blackPlayer = BP.playerID";
			if ($type == 0) // Following
				$tmpQuery .= " LEFT JOIN fav_players F ON A.playerID = F.favPlayerID AND F.playerID = ".$playerID;
			
				$tmpQuery .= ", players P ";
		
		if ($type == 0) // Following
			$tmpQuery .= "
			WHERE (A.playerID = 0 OR F.favoriteID is not null)";
		else if ($type == 1) // For a player
			$tmpQuery .= "
			WHERE A.playerID = ".$playerID;
		else
			$tmpQuery .= "
			WHERE A.activityID = ".$activityID;
			
		$tmpQuery .= " AND A.playerID = P.playerID 
		ORDER BY postDate desc
		LIMIT ".$start.", ".$limit;
	
	return mysqli_query($dbh,$tmpQuery);
}

function countActivityForPlayer($playerID)
{
	global $dbh;
	$tmpQuery = "SELECT count(A.activityID) nbActivity 
					FROM activity A, games G
					WHERE A.playerID = ".$playerID." 
					AND A.entityID = G.gameID";
	$res_count = $res_count = mysqli_query($dbh,$tmpQuery);
	$res = mysqli_fetch_array($res_count, MYSQLI_ASSOC);
	
	return $res['nbActivity'];
}

function deleteActivity($activityID)
{
	global $dbh;
	$res_activity = mysqli_query($dbh,"DELETE FROM activity WHERE activityID = ".$activityID);
	
	return $res_activity;
}

function countUnreadActivity($playerID)
{
	global $dbh;
	$res_count = mysqli_query($dbh,"SELECT count(A.activityID) nbUnreadActivity 
									FROM activity A LEFT JOIN fav_players F ON (A.playerID = F.favPlayerID AND F.playerID = ".$playerID."), players P 
									WHERE P.playerID = ".$playerID." 
									AND A.postDate > P.lastDisplayNews 
									AND (A.playerID = 0 OR favoriteID is not null)");
	$res = mysqli_fetch_array($res_count, MYSQLI_ASSOC);
	return $res['nbUnreadActivity'];
}

// Notification
/*
 * Like sur Activity du joueur
 * Like sur Partie du joueur
 * Like sur Tournoi du joueur
 * Like sur Commentaire d'une entitי
 * Comment sur Activity du joueur
 * Comment sur Partie du joueur
 * Comment sur Tournoi du joueur
 */
function listNotifications($start, $limit, $playerID)
{
	global $dbh;
	$tmpQuery = "SELECT * FROM 
				(SELECT 'like' notifType, L.likeID notifID, L.type, L.entityID, L.postDate, '' message,
		P.playerID, P.nick, P.firstName, P.lastName, P.socialNetwork, P.socialID,
        A.type subEntityType, A.entityID subEntityID
	 FROM like_entity L, activity A, players P
	 WHERE L.type = 'activity'
	 AND L.entityID = A.activityID
	 AND A.playerID = ".$playerID."
	 AND L.playerID = P.playerID
     AND L.playerID != ".$playerID."
	 UNION
	SELECT 'like' notifType, L.likeID notifID, L.type, L.entityID, L.postDate, '' message,
	 	P.playerID, P.nick, P.firstName, P.lastName, P.socialNetwork, P.socialID,
        '' subEntityType, 0 subEntityID
	 FROM like_entity L, games G, players P
	 WHERE L.type = 'game'
	 AND L.entityID = G.gameID
	 AND (G.whitePlayer = ".$playerID." OR G.blackPlayer = ".$playerID.")
	 AND L.playerID = P.playerID
     AND L.playerID != ".$playerID."
	 UNION
	SELECT 'like' notifType, L.likeID notifID, L.type, L.entityID, L.postDate, '' message,
	  		P.playerID, P.nick, P.firstName, P.lastName, P.socialNetwork, P.socialID,
           '' subEntityType, 0 subEntityID
	 FROM like_entity L, tournament T, tournament_players TP, players P
	 WHERE L.type = 'tournament'
	 AND L.entityID = T.tournamentID
	 AND T.tournamentID = TP.tournamentID
	 AND TP.playerID = ".$playerID."
	 AND L.playerID = P.playerID
     AND L.playerID != ".$playerID."
	 UNION
	SELECT 'like' notifType, L.likeID notifID, L.type, L.entityID, L.postDate, SUBSTR(C.message, 1, 35) message,
	  		P.playerID, P.nick, P.firstName, P.lastName, P.socialNetwork, P.socialID,
	  		C.type subEntityType, C.entityID subEntityID
	 FROM like_entity L, comment C, players P
	 WHERE L.type = 'comment'
	 AND L.entityID = C.commentID
	 AND C.playerID = ".$playerID."
	 AND L.playerID = P.playerID
     AND L.playerID != ".$playerID."
     UNION
     SELECT 'comment' notifType, C.commentID notifID, C.type, C.entityID, C.postDate, SUBSTR(C.message, 1, 35) message,
     		P.playerID, P.nick, P.firstName, P.lastName, P.socialNetwork, P.socialID,
        	'' subEntityType, 0 subEntityID
     FROM comment C, games G, players P
     WHERE C.type = 'game'
	 AND C.entityID = G.gameID
	 AND (G.whitePlayer = ".$playerID." OR G.blackPlayer = ".$playerID.")
	 AND C.playerID = P.playerID
     AND C.playerID != ".$playerID."
     UNION
     SELECT 'comment' notifType, C.commentID notifID, C.type, C.entityID, C.postDate, SUBSTR(C.message, 1, 35) message,
     		P.playerID, P.nick, P.firstName, P.lastName, P.socialNetwork, P.socialID,
        	'' subEntityType, 0 subEntityID
     FROM comment C, activity A, players P
     WHERE C.type = 'activity'
	 AND C.entityID = A.activityID
	 AND A.playerID = ".$playerID."
	 AND C.playerID = P.playerID
     AND C.playerID != ".$playerID."
     UNION
     SELECT 'comment' notifType, C.commentID notifID, C.type, C.entityID, C.postDate, SUBSTR(C.message, 1, 35) message,
     		P.playerID, P.nick, P.firstName, P.lastName, P.socialNetwork, P.socialID,
        	'' subEntityType, 0 subEntityID
     FROM comment C, tournament T, tournament_players TP, players P
     WHERE C.type = 'tournament'
	 AND C.entityID = T.tournamentID
	 AND T.tournamentID = TP.tournamentID
	 AND TP.playerID = ".$playerID."
	 AND C.playerID = P.playerID
     AND C.playerID != ".$playerID."
     ) as notifications
     ORDER BY postDate DESC";

	return mysqli_query($dbh,$tmpQuery);
}

function countUnreadNotification($playerID)
{
	global $dbh;
	$tmpQuery = "SELECT count(notifID) nbUnreadNotif FROM 
				(SELECT L.likeID notifID
	 FROM like_entity L, activity A, players P, players P2
	 WHERE L.type = 'activity'
	 AND L.entityID = A.activityID
	 AND A.playerID = ".$playerID."
	 AND L.playerID = P.playerID
     AND L.playerID != ".$playerID."
     AND P2.playerID = ".$playerID." AND L.postDate > P2.lastDisplayNotif
	 UNION
	SELECT L.likeID notifID
	 FROM like_entity L, games G, players P, players P2
	 WHERE L.type = 'game'
	 AND L.entityID = G.gameID
	 AND (G.whitePlayer = ".$playerID." OR G.blackPlayer = ".$playerID.")
	 AND L.playerID = P.playerID
     AND L.playerID != ".$playerID."
     AND P2.playerID = ".$playerID." AND L.postDate > P2.lastDisplayNotif
	 UNION
	SELECT L.likeID notifID
	 FROM like_entity L, tournament T, tournament_players TP, players P, players P2
	 WHERE L.type = 'tournament'
	 AND L.entityID = T.tournamentID
	 AND T.tournamentID = TP.tournamentID
	 AND TP.playerID = ".$playerID."
	 AND L.playerID = P.playerID
     AND L.playerID != ".$playerID."
     AND P2.playerID = ".$playerID." AND L.postDate > P2.lastDisplayNotif
	 UNION
	SELECT L.likeID notifID
	 FROM like_entity L, comment C, players P, players P2
	 WHERE L.type = 'comment'
	 AND L.entityID = C.commentID
	 AND C.playerID = ".$playerID."
	 AND L.playerID = P.playerID
     AND L.playerID != ".$playerID."
     AND P2.playerID = ".$playerID." AND L.postDate > P2.lastDisplayNotif
     UNION
     SELECT C.commentID notifID
     FROM comment C, games G, players P, players P2
     WHERE C.type = 'game'
	 AND C.entityID = G.gameID
	 AND (G.whitePlayer = ".$playerID." OR G.blackPlayer = ".$playerID.")
	 AND C.playerID = P.playerID
     AND C.playerID != ".$playerID."
     AND P2.playerID = ".$playerID." AND C.postDate > P2.lastDisplayNotif
     UNION
     SELECT C.commentID notifID
     FROM comment C, activity A, players P, players P2
     WHERE C.type = 'activity'
	 AND C.entityID = A.activityID
	 AND A.playerID = ".$playerID."
	 AND C.playerID = P.playerID
     AND C.playerID != ".$playerID."
     AND P2.playerID = ".$playerID." AND C.postDate > P2.lastDisplayNotif
     UNION
     SELECT C.commentID notifID
     FROM comment C, tournament T, tournament_players TP, players P, players P2
     WHERE C.type = 'tournament'
	 AND C.entityID = T.tournamentID
	 AND T.tournamentID = TP.tournamentID
	 AND TP.playerID = ".$playerID."
	 AND C.playerID = P.playerID
     AND C.playerID != ".$playerID."
     AND P2.playerID = ".$playerID." AND C.postDate > P2.lastDisplayNotif
     ) as notifications";
	
	$res_count = mysqli_query($dbh,$tmpQuery);
	$res = mysqli_fetch_array($res_count, MYSQLI_ASSOC);
	return $res['nbUnreadNotif'];
}

// Comment
function insertComment($playerID, $type, $entityID, $message)
{
	global $dbh;
	$res_comment = mysqli_query($dbh,"INSERT INTO comment (playerID, type, entityID, postDate, message)
			VALUES (".$playerID.", '".$type."', ".$entityID.", now(), '".addslashes(strip_tags($message))."')");
	return $res_comment;
}

function deleteComment($commentID)
{
	global $dbh;
	$res_comment = mysqli_query($dbh,"DELETE FROM comment WHERE commentID = ".$commentID);
		
	return $res_comment;
}

function listEntityComments($type, $entityID)
{
	global $dbh;
	$tmpQuery = "SELECT C.commentID, C.message, C.postDate, L.likeID, P.playerID, P.firstName, P.lastName, P.nick
				FROM comment C left join like_entity L on L.type = '".COMMENT."' AND L.entityID = C.commentID AND L.playerID = ".$_SESSION['playerID'].", players P 
				WHERE C.type = '".$type."' 
				AND C.entityID = ".$entityID." 
				AND C.playerID = P.playerID 
				ORDER BY postDate asc";
	//LIMIT ".$debut.", ".$limit;
	
	return mysqli_query($dbh,$tmpQuery);
}

// Like
function insertLike($playerID, $type, $entityID)
{
	global $dbh;
	$res_like = mysqli_query($dbh,"INSERT INTO like_entity (playerID, type, entityID, postDate)
			VALUES (".$playerID.", '".$type."', ".$entityID.", now())");
	return $res_like;
}

function deleteLike($likeID)
{
	global $dbh;
	$res_like = mysqli_query($dbh,"DELETE FROM like_entity WHERE likeID = ".$likeID);
	
	return $res_like;
}

function countLike($type, $entityID)
{
	global $dbh;
	$res_count = mysqli_query($dbh,"SELECT count(likeID) nbLike 
								FROM like_entity 
								WHERE type = '".$type."' 
								AND entityID = ".$entityID);
	$res = mysqli_fetch_array($res_count, MYSQLI_ASSOC);
	
	return $res['nbLike'];
}

function listLike($type, $entityID)
{
	global $dbh;
	$tmpQuery = "SELECT L.likeID, P.playerID, P.firstName, P.lastName, P.nick 
				FROM like_entity L, players P 
				WHERE L.type = '".$type."' 
				AND L.entityID = ".$entityID." 
				AND L.playerID = P.playerID";
				
	return mysqli_query($dbh,$tmpQuery);
}

// Private message
function updateUnreadPrivateMessage($playerID, $withPlayerID)
{
	global $dbh;
	$res_messages = mysqli_query($dbh,"UPDATE private_message 
								SET status = 1 
								WHERE status = 0 
								AND fromPlayerID = ".$withPlayerID." 
								AND toPlayerID = ".$playerID);
	 
	if ($res_messages)
		return TRUE;
	else
		return FALSE;
}

function insertPrivateMessage($fromPlayerID, $toPlayerID, $message)
{
	global $dbh;
	$res_pMessage = mysqli_query($dbh,"INSERT INTO private_message (fromPlayerID, toPlayerID, sendDate, message, status)
			VALUES (".$fromPlayerID.",".$toPlayerID.", now(), '".addslashes(strip_tags($message))."', 0)");
	return $res_pMessage;
}

function deletePrivateMessage($pMessageID)
{
	global $dbh;
	$res_pMessage = mysqli_query($dbh,"DELETE FROM private_message WHERE pMessageID = ".$pMessageID);
	
	return $res_pMessage;
}

function listPrivateMessageWith($playerID, $withPlayerID)
{
	global $dbh;
	$tmpQuery = "SELECT M.pMessageID, M.sendDate, M.status, M.message,
						FP.playerID, FP.nick, FP.firstName, FP.lastName, FP.socialNetwork, FP.socialID
					FROM private_message M, players FP
					WHERE (fromPlayerID = ".$playerID." OR toPlayerID = ".$playerID.") 
					AND (fromPlayerID = ".$withPlayerID." OR toPlayerID = ".$withPlayerID.")
					AND M.fromPlayerID = FP.playerID";
	
	return mysqli_query($dbh,$tmpQuery);
}

function listPMContact($playerID)
{
	global $dbh;
	$tmpQuery = "SELECT P.playerID, P.nick, P.firstName, P.lastName, P.email, P.socialNetwork, P.socialID, P.creationDate, O.lastActionTime, (SELECT count(pMessageID) FROM private_message WHERE toPlayerID = ".$playerID." AND fromPlayerID=P.playerID AND status=0) nbUnread
					FROM private_message M, players P left join online_players O on O.playerID = P.playerID
					WHERE (M.fromPlayerID = ".$playerID." OR M.toPlayerID = ".$playerID.")
					AND  (M.fromPlayerID = P.playerID OR M.toPlayerID = P.playerID)
					GROUP BY P.playerID, P.nick, P.firstName, P.lastName, P.email, P.socialNetwork, P.socialID, P.creationDate, O.lastActionTime, nbUnread
					ORDER BY nbUnread DESC";
	
	return mysqli_query($dbh,$tmpQuery);
}

function countUnreadPM($playerID)
{
	global $dbh;
	$res_count = mysqli_query($dbh,"SELECT count(pMessageID) nbUnreadPM 
				FROM private_message 
				WHERE toPlayerID = ".$playerID." 
				AND status = 0");
	$res = mysqli_fetch_array($res_count, MYSQLI_ASSOC);
	return $res['nbUnreadPM'];
}
?>