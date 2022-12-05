<P>
        
<B>Début de l'indexation :</B>
<BR>
<?php echo " ", date ("h:i:s"); ?>

</P>
<?php
//lecture récursive de contenu de dossiers
include 'fonction.php';
//Augmentation du temps
//d'exécution de ce script
set_time_limit (500);
$path= "repertoire";

explorerDir($path);

function explorerDir($path)
{
        $folder = opendir($path);
        while($entree = readdir($folder))
        {
                //On ignore les entrées

                if($entree != "." && $entree != "..")
                {
                        // On vérifie si il s'agit d'un répertoire
                        if(is_dir($path."/".$entree))
                        {
                                $sav_path = $path;
                                // Construction du path jusqu'au nouveau répertoire
                                $path .= "/".$entree;
                                //echo "DOSSIER = ", $path, "<BR>";
                                // On parcours le nouveau répertoire
                                explorerDir($path);
                                $path = $sav_path;
                        }
                        else
                        {
                                //C'est un fichier html ou pas
                                $path_source = $path."/".$entree;

                                if(stripos($path_source, '.htm'))
                                {
                                        echo 'Document:';
                                        echo $path_source, '<br>';
                                        indexer($path_source);
                                }
                                //Si c'est un .html
                                //On appelle la fonction d'indexation
                                //Dans le module_indexation.php
                                //Par un include
                        }
                }
        }
        closedir($folder);
}
?>
<P>
<B>fin de l'indéxation :</B>
<BR>
<?php echo " ", date ("h:i:s"); ?>
</P>