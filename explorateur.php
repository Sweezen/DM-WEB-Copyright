<?php

if (isset($_REQUEST["nomRep"]))  $nomRep = $_REQUEST["nomRep"];
else $nomRep = false;

if (isset($_REQUEST["action"]))
{
	switch($_REQUEST["action"])
	{
		case 'Creer' : 
		if (isset($_GET["nomRep"]) && ($_GET["nomRep"] != ""))
		if (!is_dir("./" . $_GET["nomRep"])) 
		{
			// A compléter : Code de création d'un répertoire
			mkdir("./" . $_GET["nomRep"]);
		}
		break;

		case 'Supprimer' : 
		if (isset($_GET["nomRep"]) && ($_GET["nomRep"] != ""))
		if (isset($_GET["fichier"]) && ($_GET["fichier"] != ""))
		{
			$nomRep = $_GET["nomRep"];
			$fichier = $_GET["fichier"];
			
			// A compléter : Supprime le fichier image
			unlink($nomRep . "/" . $fichier);
	
			// A compléter : Supprime aussi la miniature si elle existe					
			unlink($nomRep . "/thumbs/" . $fichier);	
		}
		break;

		case 'Renommer' : 
		if (isset($_GET["nomRep"]) && ($_GET["nomRep"] != ""))
		if (isset($_GET["fichier"]) && ($_GET["fichier"] != ""))
		if (isset($_GET["nomFichier"]) && ($_GET["nomFichier"] != ""))
		{
			$nomRep = $_GET["nomRep"];
			$fichier = $_GET["fichier"];
			$nomFichier = $_GET["nomFichier"]; // nouveau nom 

			// A compléter : renomme le fichier et sa miniature si elle existe
			if (file_exists("./$nomRep/$fichier"))			
				rename("./$nomRep/$fichier","./$nomRep/$nomFichier");

			if (file_exists("./$nomRep/thumbs/$fichier"))			
				rename("./$nomRep/thumbs/$fichier","./$nomRep/thumbs/$nomFichier");
			
			
		}
		break;

		case 'Uploader' : 
		if (!empty($_FILES["FileToUpload"]))
		{

			if (is_uploaded_file($_FILES["FileToUpload"]["tmp_name"]))
			{
				//print("Quelques informations sur le fichier récupéré :<br>");
				//print("Nom : ".$_FILES["FileToUpload"]["name"]."<br>");
				//print("Type : ".$_FILES["FileToUpload"]["type"]."<br>");
				//print("Taille : ".$_FILES["FileToUpload"]["size"]."<br>");
				//print("Tempname : ".$_FILES["FileToUpload"]["tmp_name"]."<br>");
				$name = $_FILES["FileToUpload"]["name"];
				copy($_FILES["FileToUpload"]["tmp_name"],"./$nomRep/$name");

				// créer le répertoire miniature s'il n'existe pas
				if (!is_dir("./$nomRep/thumbs")) 
				{
					mkdir("./$nomRep/thumbs");
				}
					
				$dataImg = getimagesize("./$nomRep/$name");  
				$type= substr($dataImg["mime"],6);// on enleve "image/" 

				// créer la miniature dans ce répertoire 
				miniature($type,"./$nomRep/$name",200,"./$nomRep/thumbs/$name");
			}
			else
			{
				echo "pb";
			}
		}

		break;

		case 'Supprimer Repertoire':
			// On ne peut supprimer que des répertoires vide !
			if (isset($_GET["nomRep"]) && ($_GET["nomRep"] != ""))
			{
				// A compléter : Supprime le répertoire des miniatures s'il existe, puis le répertoire principal

				if (is_dir("./$nomRep/thumbs"))
				{
					$rep = opendir("./$nomRep/thumbs"); 		// ouverture du repertoire 
					while ( $fichier = readdir($rep))	// parcours de tout le contenu de ce répertoire
					{

						if (($fichier!=".") && ($fichier!=".."))
						{
							// Pour éliminer les autres répertoires du menu déroulant, 
							// on dispose de la fonction 'is_dir'
							if (!is_dir("./$nomRep/thumbs/" . $fichier))
							{
								unlink("./$nomRep/thumbs/" . $fichier);
							}
						}
					}
					rmdir("./$nomRep/thumbs");
				}

				// répertoire principal
				$rep = opendir("./$nomRep"); 		// ouverture du repertoire 
				while ( $fichier = readdir($rep))	// parcours de tout le contenu de ce répertoire
				{

					if (($fichier!=".") && ($fichier!=".."))
					{
						// Pour éliminer les autres répertoires du menu déroulant, 
						// on dispose de la fonction 'is_dir'
						if (!is_dir("./$nomRep/" . $fichier))
						{
							unlink("./$nomRep/" . $fichier);
						}
					}
				}

				rmdir("./$nomRep");
				$nomRep = false;
			}
		break;
	}
}





function miniature($type,$nom,$dw,$nomMin)
{
	// Crée une miniature de l'image $nom
	// de largeur $dw
	// et l'enregistre dans le fichier $nomMin 


	// lecture de l'image d'origine, enregistrement dans la zone mémoire $im
	switch($type)
	{
		case "jpeg" : $im =  imagecreatefromjpeg ($nom);break;
		case "png" : $im =  imagecreatefrompng ($nom);break;
		case "gif" : $im =  imagecreatefromgif ($nom);break;		
	}

	$sw = imagesx($im); // largeur de l'image d'origine
	$sh = imagesy($im); // hauteur de l'image d'origine
	$dh = $dw * $sh / $sw;

	$im2 = imagecreatetruecolor($dw, $dh);

	$dst_x= 0;
	$dst_y= 0;
	$src_x= 0; 
	$src_y= 0; 
	$dst_w= $dw ; 
	$dst_h= $dh ; 
	$src_w= $sw ; 
	$src_h= $sh ;
	
	imagecopyresized ($im2,$im,$dst_x , $dst_y  , $src_x  , $src_y  , $dst_w  , $dst_h  , $src_w  , $src_h);
	
	
	switch($type)
	{
		case "jpeg" : imagejpeg($im2,$nomMin);break;
		case "png" : imagepng($im2,$nomMin);break;
		case "gif" : imagegif($im2,$nomMin);break;		
	}

	imagedestroy($im);
	imagedestroy($im2);
}
?>

<html>
<head>
<style>

.mini
{
	position:relative;
	width:200px;
	height:400px;
	float:left;
	border:1px black solid;
	margin-right:5px;
	margin-bottom:5px;
}
div img
{
	margin : 0 auto 0 auto;
	border : none;
}
div div 
{
	position:absolute;
	bottom:0px;
	width:100%;
	background-color:lightgrey;
	border-top:1px black solid;
	text-align:center;
}

.renommer
{
	width:150px;
}
.btn_renommer
{

	width:35px;
}

</style>
</head>

<body>

<h1>Gestion des répertoires </h1>
<form>
<label>Créer un nouveau répertoire : </label>
<input type="text" name="nomRep"/>
<input type="submit" name="action" value="Creer" />
</form>

<form>
<label>Choisir un répertoire : </label>
<select name="nomRep">
<?php
	$rep = opendir("./"); // ouverture du repertoire 
	while ( $fichier = readdir($rep))
	{
		// On élimine le résultat '.' (répertoire courant) 
		// et '..' (répertoire parent)

		if (($fichier!=".") && ($fichier!=".."))
		{
			// Pour éliminer les autres fichiers du menu déroulant, 
			// on dispose de la fonction 'is_dir'
			if (is_dir("./" . $fichier))
				printf("<option value=\"$fichier\">$fichier</option>");
		}
	}
	closedir($rep);
?>
</select>
<input type="submit" value="Explorer"> <input type="submit" name="action" value="Supprimer Repertoire">
</form>

<?php
	if (!$nomRep)  die("Choisissez un répertoire"); 
	// interrompt immédiatement l'exécution du code php
?>

<hr />
<h2> Contenu du répertoire '<?php echo$_GET["nomRep"]?>' </h2>


<form enctype="multipart/form-data" method="post">
	<input type="hidden" name="MAX_FILE_SIZE" value="10000000">
	<input type="hidden" name="nomRep" value="<?php echo $nomRep; ?>">
	<label>Ajouter un fichier image : </label>
	<input type="file" name="FileToUpload">
	<input type="submit" value="Uploader" name="action">
</form>

<?php

	$numImage = 0;
	$rep = opendir("./$nomRep"); 		// ouverture du repertoire 
	while ( $fichier = readdir($rep))	// parcours de tout le contenu de ce répertoire
	{
	
		if (($fichier!=".") && ($fichier!=".."))
		{
			// Pour éliminer les autres répertoires du menu déroulant, 
			// on dispose de la fonction 'is_dir'
			if (!is_dir("./$nomRep/" . $fichier))
			{
				// Un fichier... est-ce une image ?
				// On ne liste que les images ... 
				$formats = ".jpeg.jpg.gif.png";
				if (strstr($formats,strrchr($fichier,"."))) 
				{
					$numImage++;
					$dataImg = getimagesize("./$nomRep/$fichier"); 

					// A compléter : récupérer le type d'une image, et sa taille 
					$width= $dataImg[0];
					$height= $dataImg[1]; 
					$type= substr($dataImg["mime"],6);

					// A compléter : On cherche si une miniature existe pour l'afficher...
					// Si non, on crée éventuellement le répertoire des miniatures, 
					// et la miniature que l'on place dans ce sous-répertoire				

					echo "<div class=\"mini\">\n";
					echo "<a target=\"_blank\" href=\"$nomRep/$fichier\"><img src=\"$nomRep/thumbs/$fichier\"/></a>\n";
					echo "<div>$fichier \n";			
					echo "<a href=\"?nomRep=$nomRep&fichier=$fichier&action=Supprimer\" >Supp</a>\n";
					echo "<br />($width * $height $type)\n";
					echo "<br />\n";

					echo "<form>\n";
					echo "<input type=\"hidden\" name=\"fichier\" value=\"$fichier\" />\n";
					echo "<input type=\"hidden\" name=\"nomRep\" value=\"$nomRep\" />\n";
					echo "<input type=\"hidden\" name=\"action\" value=\"Renommer\" />\n";
					echo "<input type=\"text\" class=\"renommer\" name=\"nomFichier\" value=\"$fichier\" onclick=\"this.select();\" />\n";
					echo "<input type=\"submit\" class=\"btn_renommer\" value=\">\" />\n";
					echo "</form>\n";

					echo "</div></div>\n";

					// A compléter : appeler echo "<br style=\"clear:left;\" />"; si on a affiché 5 images sur la ligne actuelle
					
					if (($numImage%5) ==0)
					echo "<br style=\"clear:left;\" />";
				}
			}
		}

	
	}
	closedir($rep);

	// A compléter : afficher un message lorsque le répertoire est vide
	if ($numImage==0) echo "<h3>Aucune image dans le répertoire</h3>";

?>


</body>
