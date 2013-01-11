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
	
}

function searchComment($playerID, $type, $entityID)
{
	
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