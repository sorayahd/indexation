<!DOCTYPE html>
<html lang="en">
    
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/css/bootstrap.min.css" rel="stylesheet" 
        integrity="sha384-Zenh87qX5JnK2Jl0vWa8Ck2rdkQ2Bzep5IDxbcnCeuOxjzrPF/et3URy9Bv1WTRi" crossorigin="anonymous">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    
        <link rel="stylesheet" href="style.css">

        <title>recherche</title>
    </head>


    <style>
		
    @import url("https://fonts.googleapis.com/css2?family=Poppins:weight@100;200;300;400;500;600;700;800&display=swap");


body{
 background-color: white;
 font-family: "Poppins", sans-serif;
 font-weight: 00;
}

.height{
 height: 20vh;
}


.search{
position: relative;
box-shadow: 3 3 30px rgba(41, 41, 41, .2);
  
}

.search input{

 height: 60px;
 text-indent: 25px;
 border: 2px solid #D78207;


}


.search input:focus{

 box-shadow: none;
 border: 2px solid #D78207;


}

.search .fa-search{

 position: absolute;
 top: 20px;
 left: 16px;

}

.search button{

 position: absolute;
 top: 5px;
 right: 5px;
 height: 50px;
 width: 110px;
 background: #D78207;

}

.nuage{
    width: 60%;
    background:#B6D19D;
    color:#0066FF;
    padding: 10px;
    border: 1px solid #55d2ff;
    text-align:center;
    -moz-border-radius: 4px;
    -webkit-border-radius: 4px;
    border-radius: 4px;
  }



 
		</style>
    <body>


 
       <figure class="text-center">
            <blockquote class="blockquote">
               
                <img src="logo.png" width="200" height="150"> 
            </blockquote>
        </figure>  
        <div class="container">
            <div class="row height d-flex justify-content-center align-items-center">
                <div class="col-md-8">
                    <div class="search">
                    
                        <form action="rechercher.php" method="post" id="form-id">

                        <input type="search_input" name="query" id="search" class="form-control" placeholder="saisir votree recherche">
                        <a class="search_icon">
                        <button class="btn btn-light" name="submit" >rechercher</button>
                        </form> 
                        
                    </div>    

            </div>
        
        
    









  
  	<div class="col-md-2"></div>
  	 <div class="col-md-8">

<?php 

//Inclure la biblioth??que contenant nos fonctions  
include 'fonction.php';
  
    //R??cup??rer le mot saisi dans la barre de recherche
      if (isset($_POST["query"]))
        $query = $_POST["query"];

    //R??cup??rer le query du lien de la page actuelle
      if (isset($_GET['query'])) 
        $query = $_GET['query'];

    //Initialisation d'un message d'erreur si aucun mot n'est saisi 
      $error="";

    //V??rification si y'a eu un submit de la recherche
    if (isset($query)){

      //V??rification si query n'est pas juste un espace vide
      if (trim($query) == "") {
        $error = "Veuillez svp saisir un mot pour la recherche !!!";
      }

      //Si query est valide la recherche commence
      else{

      //Etablir une connexion avec la BDD
      $connexion = mysqli_connect("localhost","root","","index3");

      //R??cup??ration du num??ro de page actuelle du lien en haut 
      if(isset($_GET['page']))    $page=$_GET['page'];
      //Sinon c'est la premi??re page
      else    $page=1; 

     
      //Requ??te de r??cup??ration du nombre de r??sultats d??fini ?? partir du d??but calcul?? pour chaque page
        $sql = "SELECT document.id, document.source, document.titre, document.descriptif, mot_document.poids 
            FROM ((document INNER JOIN mot_document ON document.id = mot_document.idSource) INNER JOIN mot ON mot_document.idMot = mot.id) where
            mot.mot = '$query' ORDER BY poids DESC";






      //Requ??te de r??cup??ration de tous les r??sultats de la recherche pour query
        $sql_count = "SELECT * FROM
         ((document INNER JOIN mot_document ON document.id = mot_document.idSource) 
         INNER JOIN mot ON mot_document.idMot = mot.id) where mot.mot = '$query' ";
        
      //R??sultats ?? afficher dans une page
      	$resultat = mysqli_query($connexion,$sql);

      //Nombre total des r??sultats --> utilis?? pour calculer le nombre des pages n??cessaires 
      	$nbr_resultats = mysqli_num_rows(mysqli_query($connexion,$sql_count));
        
    
      //Affichage du nombre des r??sultats trouv??s pour le mot recherch??
      	echo "<br>$nbr_resultats R??sultats trouv??s pour <b>$query</b> :<br><br>";
      	
      //Afficher des attributs n??cessaires pour chaque r??sultat 
      	while ($ligne = mysqli_fetch_row($resultat)) {

          //Affichage du titre du document et poids du query dans ce document  
      	 	echo "<a href='$ligne[1]' target='_blank'><font color="."navy"."><b>$ligne[2]</b></font></a>"."($ligne[4])";
             
          //Affichage de la source du docmument + le bouton pour afficher e cacher le nuage 
      	 	echo "<br><font color="."green".">".$ligne[1]."</font>".'<button class="btn btn-link" onclick="myFunction(this,'.$ligne[0].')">
               <i class="fa fa-cloud"></i> (+)</button><br>';
           
          //Affichage du descriptif du document 
      	 	echo $ligne[3]."<br>";

      //Requ??te de r??cup??ration d'une liste de 35 mots al??atoires du document pour le nuage des mots cl??s
        $sql_nuage = "SELECT mot.mot, mot_document.poids  FROM ((document INNER JOIN mot_document ON document.id = mot_document.idSource) INNER JOIN mot ON mot_document.idMot = mot.id) where document.id = '$ligne[0]' ORDER BY rand() LIMIT 35";

        //R??sultats des mots pour le nuage
        $resultat_nuage = mysqli_query($connexion,$sql_nuage);

        //On met les r??sultats dans un tableau associatif pour donner en param??tres ?? la fonction generernuage()
        $tab_nuage=array();
        while ($lign = mysqli_fetch_row($resultat_nuage)) {
           $tab_nuage += [ $lign[0] => $lign[1] ];
        }

        // On v??rifie si query figure das la liste al??atoire, sinon on l'ajoute 
        if (!in_array($query, $tab_nuage)) {
          $tab_nuage += [ $query => $ligne[4] ];
        }

          //Affichage du nuage
          echo '<br><div class="nuage" style="display:none" id="'.$ligne[0].'">
          '.genererNuage($tab_nuage,$ligne[1]).'
          </div>

          <script>
          function myFunction(bouton,id) {
            var x = document.getElementById(id);
            if (x.style.display === "none") {
              x.style.display = "block";
              bouton.innerHTML="(-)";
            } else {
              x.style.display = "none";
              bouton.innerHTML="(+)";
            }
          }
          </script><br><br>';
          }
      	 } 
        }
      	?>
      
      </div>
      </div></div>

<br> 

<!-- Pagination des r??sultats -->


    <div>

    <!-- affichage du message d'erreur en cas de recherche sur un vide --> 
    <div style="text-align: center; color: red"><a> <b> <?php echo $error; ?> </b></a></div>
      
    </div>	





</body>

</html>
