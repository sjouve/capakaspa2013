<?
	require_once('rss_fetch.inc');			
	
	// Les URLs des fils RSS utilisés
	define ("URL_RSS_BLOG", "http://blog.capakaspa.info/atom.php");
	define ("URL_RSS_FORUM", "http://forum.capakaspa.info/topics_anywhere.php?mode=show&out=rss2.0&f=a&n=5&sfn=y&r=y&sr=y&a=y&so=d&b=non&lpb=0&lpd=0&af=p2xibKcgKKdmbqcpIKd0dKd8p2xwYacsIKdscHSnICincmVwpyk%3D");
	define ("URL_RSS_ABOUT", "http://z.about.com/6/g/chess/b/index.xml");
	define ("URL_RSS_ABOUT_POP", "http://z.about.com/6/o/m/chess_p.xml");
	define ("URL_RSS_ABOUT_ACT", "http://z.about.com/6/o/m/chess_t.xml");
	define ("URL_RSS_FOUNUM", "http://perso.wanadoo.fr/lefouduroi/lefounumerique.rss");	
	
	/* Affichage de l'icône qui ouvre le fil RSS */
	function displayIconRSS($url)
	{
		echo("<a href='".$url."' target='_blank'><img src='images/xml.gif' border='0'/></a>");
	}
	
	/* Affichage du fil RSS dans le body */
	function displayBodyRSS($url, $nbItem)
	{
		
		$nb = 0;
		if ( $url )
		{
			$rss = fetch_rss( $url );
			foreach ($rss->items as $item)
			{
				$href = $item['link'];
				$title = $item['title'];
				$description = $item['summary'];
				$pubDate = $item['updated'];
				
				if ($nb==0) 
				{	echo("<div class='rsstitlefirst'><img src='images/porte_voix.png'><b> $title</b>");}
				else
				{	echo("<div class='rsstitle'><b>$title</b>");}
				
				if ($pubDate)
				{
					list($annee, $mois, $jour) = explode("-", substr($pubDate, 0,10)); 
					echo(" - ".$jour.'/'.$mois.'/'.substr($annee,2, 2)."</div>");
				}
				else
				{
				  	echo("</div>");
				}
				if ($nb==0)
				{	echo("<div class='rssdescriptionfirst'>$description <a href=$href>lire la suite </a></div>");}
				else
				{	echo("<div class='rssdescription'>$description <a href=$href>lire la suite </a></div>");}
				
				$nb++;
				if ($nb>$nbItem) break;
			}
		}
	}
	
	/* Affichage du fil RSS dans le body */
	/* De 0 à n */
	function displayBodyRSSPlage($url, $deb, $fin)
	{
		
		$nb = 0;
		if ( $url )
		{
			$rss = fetch_rss( $url );
			foreach ($rss->items as $item)
			{
				if ($nb >= $deb)
				{
					$href = $item['link'];
					$title = $item['title'];
					$description = $item['summary'];
					$pubDate = substr($item['issued'], 0, 10);
					
					if ($nb==0) 
					{	echo("<div class='rsstitlefirst'><img src='images/porte_voix.png'><b> $title</b>");}
					else
					{	echo("<div class='rsstitle'><b>$title</b>");}
					
					if ($pubDate)
					{
						echo(" [$pubDate]</div>");
					}
					else
					{
					  	echo("</div>");
					}
					if ($nb==0)
					{	echo("<div class='rssdescriptionfirst'>$description <a href=$href>Lire sur le Forum</a></div>");}
					else
					{	echo("<div class='rssdescription'>$description <a href=$href>Lire</a></div>");}
				}
				
				$nb++;
				if ($nb > $deb+$fin) break;
			}
		}
	}
	
	/* Affichage du fil RSS dans une barre latérale */
	function displayBarRSS($url, $nbItem)
	{
		
    $nb = 0;
		if ( $url ) 
		{
			$rss = fetch_rss( $url );
			foreach ($rss->items as $item)
			{
				$href = $item['link'];
				$title = $item['title'];
				echo("<li><a href=$href target='_blank'>$title</a></li>");
				$nb++;
				if ($nb>$nbItem) break;
			}
		}
	}
	
?>