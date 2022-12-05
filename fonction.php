<?php 


global $tab_mots_vides;
$nbr=0;
//explodee chaine
function explode_bis($separateurs,$chaine)
{
$tab =array();
$tok =strtok($chaine,"$separateurs");
if (strlen(trim($tok))>2) $tab[]= $tok;
	while($tok != false)
	{		
		$tok =strtok($separateurs);
		if (strlen(trim($tok))>2 && !in_array(trim($tok),$tab))  $tab[]= $tok; 		
	}
	return $tab;
}

//afficher le tableau avec les indices et valeurs
 function print_tab($tab)
{
	foreach ($tab 	as $indice=>$mot)
         echo $indice," :  ",$mot,"<br>";
}


//extraction des keywords et description des metas html
function get_keywords_description($source_html)
{
	//les  metas keywords +description
	$chaine_metas = "";
	$tab_metas = get_meta_tags($source_html);
	if(isset($tab_metas["keywords"])) $chaine_metas .= $tab_metas["keywords"];
	if(isset($tab_metas["description"])) $chaine_metas .= " ". $tab_metas["description"];

	return strtolower($chaine_metas);
}

//Recuperer le descriptif
function get_description($source_html)
{
	$chaine_metas = "";
	$tab_metas = get_meta_tags($source_html);
	if(isset($tab_metas["description"])) $chaine_metas .= " ". $tab_metas["description"];
	return strtolower($chaine_metas);

}



//extraction de title de html 
function get_title($source_html)
{
	$chaine_html = implode(" ",file ($source_html)) ;

	$modele ="/<title>(.*)<\/title>/si";

	if (preg_match($modele,$chaine_html,$titre))	{
		return strtolower($titre[1]);
	}
	else return "titre existe pas";
}

//extraction de body de html en texte 
function get_body($source_html){
	$chaine_html = implode(" ",file ($source_html)) ;

	$modele_body ="/<body[^>]*>(.*)<\/body>/is";
	$modele_balises_scripts = '/<script[^>]*?>.*?<\/script>/is';

	//Remplacer les scripts par des vides dans HTML
	$html_sans_script = preg_replace($modele_balises_scripts, '', $chaine_html);
	
	//Récuperer le body sans script
	preg_match($modele_body,$html_sans_script,$body);
		
	$chaine_text = strtolower(strip_tags_bis($body[1])) ;
	
	return $chaine_text;

}

//Strip_tags_bis

function strip_tags_bis($html_sans_script)
{   $modele = '/<\/(.+?)>/is';
    $chaine = preg_replace($modele," ",$html_sans_script);
    $chaine_sans_html = strip_tags($chaine); 
    return $chaine_sans_html;
}


	// Mise en bdd des resultats de l'indexation 
	
	function insertion_BDD($source_html, $titre, $descriptif, $tab_mots_poids){
	
		$connexion = mysqli_connect("localhost","root","","index3");
			$idMot = 0;
			$idSource = 0;
			$select_document = "SELECT * FROM document WHERE source = '".$source_html."' and titre = '".$titre."' ";
			$resultats_select_document = mysqli_query($connexion,$select_document);
	
			//Ajouter un document à la base de données
			if (mysqli_num_rows($resultats_select_document)==0) {
	
				$insert_document = " insert into document(source,titre,descriptif) values ('$source_html','$titre','$descriptif') ";
				$resultats_insert_document = mysqli_query($connexion,$insert_document);
					
					if ($resultats_insert_document) {
						$idSource = mysqli_insert_id($connexion);
					}
			}
	
			else{	
				$idSource = mysqli_fetch_row($resultats_select_document)[0];
			}
	
	
			//Ajouter un mot à la base de données
			foreach ($tab_mots_poids as $mot => $poids) {
			
				$select_mot = "SELECT * FROM mot WHERE mot = '".$mot."' ";
				$resultats_select_mot = mysqli_query($connexion,$select_mot);
	
				
					$insert_mot = " insert into mot(mot) values ('$mot') ";	
					$resultats_insert_mot = mysqli_query($connexion,$insert_mot);
					
					if ($resultats_insert_mot) {
						$idMot = mysqli_insert_id($connexion);
					}
	
			//insertion dans la table d'association
			$select_mot_document = "SELECT * FROM mot_document WHERE idMot = '".$idMot."' and idSource = '".$idSource."' ";
			$resultats_select_mot_document = mysqli_query($connexion,$select_mot_document);
			
				if (mysqli_num_rows($resultats_select_mot_document)==0) {
				$sql = "INSERT INTO mot_document (idMot, idSource, poids) VALUES ($idMot, $idSource, $poids)";
				$resultat = mysqli_query($connexion, $sql);
				}
	
			}
		mysqli_close($connexion);
	}

//Augmenter le coefficient des occirences
function occurenceHead ($tab, $coefficient){
	foreach ($tab as $key => $value) {
		$tab[$key] *= $coefficient;
	}
	return $tab;
}

// Fusionner deux tableaux 
function fusion_deux_tableaux ($tab_mots_occurrences_head, $tab_mots_occurrences_body){	
	foreach ($tab_mots_occurrences_head as $mot_head => $occ_head){
		if (array_key_exists("$mot_head", $tab_mots_occurrences_body))
			$tab_mots_occurrences_body ["$mot_head"] += $occ_head;
		else
			$tab_mots_occurrences_body += [ "$mot_head" => $occ_head];
	}
return $tab_mots_occurrences_body;
}

//traduction des caractéres html en ascii
function toASCII($chaine)
{
   
    $table_caracts_html = get_html_translation_table(HTML_ENTITIES); 

    $tableau_html_caracts =  array_flip($table_caracts_html);

    // retourne une chaine de caractères après avoir remplacé les éléments/clés par les éléments/valeurs  du tableau associatif de paires  $tableau_html_caracts dans la chaîne $chaine.
    $chaine  =  strtr ($chaine,$tableau_html_caracts); 

    return $chaine;
}

//elever les mots vides de la table des mots récupérés
function enlever_mots_vides($tab_mot_occurrence){
$tab_mots_vides = file("mots_vides.txt");

$tab_test=array();
foreach ($tab_mots_vides  as $key => $value) {
	array_push($tab_test,trim($value));
}
$tab_mots_vides = array_flip($tab_test);

foreach ($tab_mot_occurrence as $mot => $occ){
	if (array_key_exists("$mot", $tab_mots_vides)){
		unset($tab_mot_occurrence["$mot"]);
	}
}
return $tab_mot_occurrence;
}


function genererNuage( $data = array() , $source_html, $minFontSize = 15, $maxFontSize = 40 )
{

        $tab_colors=array("#3087F8", "#000080", "#FF0000", "#7F814E", "#EC1E85","#14E414","#9EA0AB", "#9EA414", "#800080");

        $minimumCount = min( array_values( $data ) );
        $maximumCount = max( array_values( $data ) );
        $spread = $maximumCount - $minimumCount;
        $cloudHTML = '';
        $cloudTags = array();

        $spread == 0 && $spread = 1;
        //Mélanger un tableau de manière aléatoire
        srand((float)microtime()*1000000);
        $mots = array_keys($data);
        shuffle($mots);

        foreach( $mots as $tag )
        {       
                $count = $data[$tag];
                //La couleur aléatoire
                $color=rand(0,count($tab_colors)-1);

                $size = $minFontSize + ( $count - $minimumCount )
                        * ( $maxFontSize - $minFontSize ) / $spread;
                $cloudTags[] ='<a style="font-size: '.
                        floor( $size ) .
                        'px' .
                        '; color:' .
                        $tab_colors[$color].
                        '; " title="' .
                        $tag .
                        ' est répété '.round($data[$tag]).' fois dans ce document " href="'.$source_html.'">' .
                        $tag .
                        '</a>';
        }
        return join( "\n", $cloudTags ) . "\n";
}       















//Indexer un fichier html
function indexer($source_html){
	
	//séparateur tokenisation
	$separateurs = " ;,.^@§$()£:!?»«\t\"\n\r\'-+/*%{}[]#0123456789";

		$title = get_title($source_html);
		$descriptif= get_description($source_html);
		
		//extraction des keywords et description des metas html
		$key_desc = get_keywords_description($source_html);
		
		$text_head = $title." ".$key_desc;

		//traduction des entités html en ascii
	    $chaine_head = toASCII($text_head);

		//tokenisation de la chaine en mot 
		$tab_title_metas = explode_bis($separateurs,$chaine_head);
		$tab_head_mot_occurrence = array_count_values($tab_title_metas);
        
		$nombreMotsHeadSelectionnes = sizeof($tab_title_metas);
		//Appliquer le coefficient
		$coefficient = 1.5;
		$tab_head = occurenceHead ($tab_head_mot_occurrence, $coefficient);

		$text_body = get_body($source_html);

		//traduction des entités html en ascii
	    $chaine_body = toASCII($text_body);

		//tokenisation de la chaine en mot 
		$tab_body = explode_bis($separateurs,$chaine_body);
		$tab_body_mot_occ = array_count_values($tab_body);
		$nombreMotsBodyTotal = $GLOBALS["nbr"];
		$nombreMotsBodySelectionnes = sizeof($tab_body);
		$nombreMotsHeadTotal = $GLOBALS["nbr"];

		//$nombreMotsTotal = $nombreMotsHeadTotal + $nombreMotsBodyTotal;
        $nombreMotsSelectionnes = $nombreMotsHeadSelectionnes + $nombreMotsBodySelectionnes;
		
		
		

echo "<table border ='1px solid red;'>";
echo "<tr><td>Titre du document</td><td>$title</td></tr>";
echo "<tr><td>Source</td><td>$source_html</td></tr>";
echo "</br>";
echo '<h3 style="color:red"> </h3>';

echo "<tr><td>Nombre des mots insérés</td><td>$nombreMotsSelectionnes</td></tr>";
echo"</table>";
echo "<hr>";








		
//Fusion des tables du Head et Body
$tab_mots_poids = fusion_deux_tableaux ($tab_head, $tab_body_mot_occ);

$tab_mots_poids_final = enlever_mots_vides($tab_mots_poids);
// Mise en bdd des resultats de l'indexation 

insertion_BDD($source_html,$title,$descriptif,$tab_mots_poids_final);



}




?>
