<?
// Activity
function insertActivity($playerID, $type, $entityID, $message)
{
	$res_activity = mysql_query("INSERT INTO activity (playerID, type, entityID, postDate, message)
			VALUES (".$playerID.", '".$type."', ".$entityID.", now(), '".$message."')");
	return $res_activity;
}

function listActivityFollowing($debut, $limit, $playerID)
{
	$tmpQuery = "SELECT *
		FROM activity A, fav_players F
		WHERE A.playerID = F.favPlayerID
		AND F.playerID=".$playerID."
		ORDER BY postDate desc
		LIMIT ".$debut.", ".$limit;
	
	return mysql_query($tmpQuery);
}

// Comment
function insertComment($playerID, $type, $entityID, $message)
{
	$res_comment = mysql_query("INSERT INTO comment (playerID, type, entityID, postDate, message)
			VALUES (".$playerID.", '".$type."', ".$entityID.", now(), '".$message."')");
	return $res_comment;
}

function deleteComment($commentID)
{
	$res_comment = mysql_query("DELETE FROM comment WHERE commentID = ".$commentID);
		
	return $res_comment;
}

function listEntityComments($type, $entityID)
{
	$tmpQuery = "SELECT C.commentID, P.playerID, P.firstName, P.lastName, C.message, C.postDate
	FROM comment C, players P 
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
	$res_like = mysql_query("INSERT INTO like (playerID, type, entityID, postDate)
			VALUES (".$playerID.", '".$type."', ".$entityID.", now())");
	return $res_like;
}

function deleteLike($likeID)
{
	
}

function searchLike($playerID, $type, $entityID)
{
	
}
?>