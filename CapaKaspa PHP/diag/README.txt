Pour installer DIAGOL :

0) Editer le fichier config.inc.php.

1) Choisir la taille désirée pour les figurines (29 ou 35 pixels).

2) Déplacer le contenu du répertoire "pieces" correspondant dans le
répertoire de votre choix.

3) Indiquer le nom de ce répertoire dans le fichier
config.inc.php (variable $base_url). Modifier éventuellement la valeur
de la variable $image_size.
Par exemple, si vous choisissez une taille de 35 pixels pour les figurines, vous pouvez copier le contenu du répertoire pieces35/ dans un nouveau répertoire pieces/, et définir
$base_url = "./pieces/";
$image_size = 35;

4) Toujours dans le fichier de configuration config.inc.php, définir les couleurs des cases, l'épaisseur de la bordure de l'échiquier, l'affichage ou non des coordonnées (le mieux consistant à effectuer des essais ...). La valeur de la variable $hdr sera "false" en général.

5) Placer les fichiers diagol.php, fen2classic.php, includes.inc.php, sub_fonction.inc.php et config.inc.php dans le même répertoire.
Si votre hébergeur en est resté à la version 3 de PHP, il peut être utile de renommer diagol.php en diagol.php3

6) Le script s'appelle en fournissant une valeur valide à la variable "position". Exemple de code HTML correct :
<img src="http://ajec-echecs.org/forum/diagol.php?position=B:Rg1,Dd1,Ta1,e1,Pa2,f6/N:Rb8,Pa6,f5">
ou encore (position FEN) :
<img src="http://ajec-echecs.org/forum/diagol.php?position=r1bqkbnr/pp2pppp/2n5/2p1P3/3p4/2P2N2/PP1P1PPP/RNBQKB1R">

7) Les options disponibles sont détaillées à l'URL http://diagol.ajec-echecs.org/diagol.html

8) Si l'un des points précédents est obscur, contactez-moi à l'adresse
webmaster@ajec-echecs.org !
