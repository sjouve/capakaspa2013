<?
// Activity
function insertActivity($playerID, $type, $entityID, $message, $msgType)
{
	$res_activity = mysql_query("INSERT INTO activity (playerID, type, entityID, postDate, message, msgType)
			VALUES (".$playerID.", '".$type."', ".$entityID.", now(), '".$message."', '".$msgType."')");
	return $res_activity;
}

function listActivity($start, $limit, $type, $playerID)
{
	$tmpQuery = "SELECT A.activityID, A.playerID, A.type, A.entityID, A.msgType, A.message, A.postDate, L.likeID,
				G.gameID, G.eco, G.position, G.gameMessage, G.lastMove, G.dateCreated, G.type, G.flagBishop, G.flagKnight, G.flagRook, G.flagQueen, E.name ecoName,
				WP.playerID wPlayerID, WP.firstName wFirstName, WP.lastName wLastName, WP.elo wElo, WP.socialNetwork wSocialNetwork, WP.socialID wSocialID,
				BP.playerID bPlayerID, BP.firstName bFirstName, BP.lastName bLastName, BP.elo bElo, BP.socialNetwork bSocialNetwork, BP.socialID bSocialID
		FROM activity A left join like_entity L on L.type = '".ACTIVITY."' AND L.entityID = A.activityID AND L.playerID = ".$_SESSION['playerID'].", games G left join eco E on E.eco = G.eco AND E.ecoLang = '".getLang()."', players WP, players BP";
		
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
	
	return mysql_query($tmpQuery);
}

// Comment
function insertComment($playerID, $type, $entityID, $message)
{
	$res_comment = mysql_query("INSERT INTO comment (playerID, type, entityID, postDate, message)
			VALUES (".$playerID.", '".$type."', ".$entityID.", now(), '".addslashes(strip_tags($message))."')");
	return $res_comment;
}

function deleteComment($commentID)
{
	$res_comment = mysql_query("DELETE FROM comment WHERE commentID = ".$commentID);
		
	return $res_comment;
}

function listEntityComments($type, $entityID)
{
	$tmpQuery = "SELECT C.commentID, C.message, C.postDate, L.likeID, P.playerID, P.firstName, P.lastName
	FROM comment C left join like_entity L on L.type = '".COMMENT."' AND L.entityID = C.commentID AND L.playerID = ".$_SESSION['playerID'].", players P 
	WHERE C.type = '".$type."' 
	AND C.entityID = ".$entityID." 
	AND C.playerID = P.playerID 
	ORDER BY postDate asc";
	//LIMIT ".$debut.", ".$limit;
	
	return mysql_query($tmpQuery);
}

// Like
function insertLike($playerID, $type, $entityID)
{
	$res_like = mysql_query("INSERT INTO like_entity (playerID, type, entityID, postDate)
			VALUES (".$playerID.", '".$type."', ".$entityID.", now())");
	return $res_like;
}

function deleteLike($likeID)
{
	$res_like = mysql_query("DELETE FROM like_entity WHERE likeID = ".$likeID);
	
	return $res_like;
}

function countLike($type, $entityID)
{
	$res_count = mysql_query("SELECT count(likeID) nbLike FROM like_entity WHERE type = '".$type."' AND entityID = ".$entityID);
	$res = mysql_fetch_array($res_count, MYSQL_ASSOC);
	
	return $res['nbLike'];
}

function searchLike($playerID, $type, $entityID)
{
	
}
?>