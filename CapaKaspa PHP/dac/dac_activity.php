<?
// Activity
function insertActivity($playerID, $type, $entityID, $message, $msgType)
{
	global $dbh;
	$res_activity = mysqli_query($dbh,"INSERT INTO activity (playerID, type, entityID, postDate, message, msgType)
			VALUES (".$playerID.", '".$type."', ".$entityID.", now(), '".$message."', '".$msgType."')");
	return $res_activity;
}

function listActivity($start, $limit, $type, $playerID)
{
	global $dbh;
	$tmpQuery = "SELECT A.activityID, A.playerID, A.type, A.entityID, A.msgType, A.message, A.postDate, L.likeID,
				G.gameID, G.eco, G.position, G.gameMessage, G.lastMove, G.dateCreated, G.type, G.flagBishop, G.flagKnight, G.flagRook, G.flagQueen, E.name ecoName,
				WP.playerID wPlayerID, WP.firstName wFirstName, WP.lastName wLastName, WP.nick wNick, WP.elo wElo, WP.elo960 wElo960, WP.socialNetwork wSocialNetwork, WP.socialID wSocialID,
				BP.playerID bPlayerID, BP.firstName bFirstName, BP.lastName bLastName, BP.nick bNick, BP.elo bElo, BP.elo960 bElo960, BP.socialNetwork bSocialNetwork, BP.socialID bSocialID,
				(SELECT COUNT(commentID) FROM comment WHERE type='activity' and entityID=A.activityID) nbComment,
				(SELECT COUNT(likeID) FROM like_entity WHERE type='activity' and entityID=A.activityID) nbLike
		FROM activity A left join like_entity L on L.type = '".ACTIVITY."' AND L.entityID = A.activityID AND L.playerID = ".$_SESSION['playerID'].", 
			games G left join eco E on E.eco = G.eco AND E.ecoLang = '".getLang()."', players WP, players BP";
		
		if ($type == 0) // Following
			$tmpQuery .= ", fav_players F 
			WHERE A.playerID = F.favPlayerID
			AND F.playerID = ".$playerID;
		else // For a player
			$tmpQuery .= "
			WHERE A.playerID = ".$playerID;
			
		$tmpQuery .= " AND A.entityID = G.gameID
		AND G.whitePlayer = WP.playerID
		AND G.blackPlayer = BP.playerID
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