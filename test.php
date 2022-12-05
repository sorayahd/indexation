
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
 
		</style>
    <body>


        <figure class="text-center">
            <blockquote class="blockquote">
                <br>
                <img src="logo.png" width="400" height="220"> 
            </blockquote>
        </figure>  
        <form action="" method="POST">
            <div class="row height d-flex justify-content-center align-items-center">
                <div class="col-md-8">
                    <div class="search">
                        <i class="fa fa-search"></i>
                        <input type="search" name="search" id="search" class="form-control" placeholder="saisir votree recherche">
                        <button class="btn btn-light" name="submit" >rechercher</button>
                        
                    </div>    
                </div>
            </div>
        </form>  

        <div class="container" style="width:30%;text-align:center">


<?php 


if(isset($_POST["submit"])){
	$query = $_POST["search"];

	$connexion = mysqli_connect("localhost","root","","index3");
		//reécupérer les données
	$sql = " SELECT idSource,source,titre,descriptif,poids,mot FROM ((document
	LEFT JOIN  mot_document ON document.id= mot_document.idSource)
	RIGHT join mot ON mot.id = mot_document.idMot)
	where mot.mot = '$query' order by poids desc";
        

	$resultat = mysqli_query($connexion,$sql);
	-
	$nombre= mysqli_num_rows($resultat);


 

    
	
	echo "Resultat pour le mot : $query :  ".$nombre."<br>";
	
	$i = 1;
	$ligne = mysqli_fetch_row($resultat);


    $sql_nuage = "SELECT mot.mot, mot_document.poids  FROM ((document INNER JOIN mot_document ON document.id = mot_document.idSource) INNER JOIN mot ON mot_document.idMot = mot.id) where document.id = '$ligne[0]' ORDER BY rand() LIMIT 35";

    //Résultats des mots pour le nuage
    $resultat_nuage = mysqli_query($connexion,$sql_nuage);

    //On met les résultats dans un tableau associatif pour donner en paramètres à la fonction generernuage()
    $tab_nuage=array();

    while ($ligne = mysqli_fetch_row($resultat_nuage)) {
       $tab_nuage += [ $ligne[0] => $ligne[1] ];
    }

    // On vérifie si query figure das la liste aléatoire, sinon on l'ajoute 
    if (!in_array($query, $tab_nuage)) {
      $tab_nuage += [ $query => $ligne[4] ];
    }

      //Affichage du nuage
      echo '<br><div class="tagcloud" style="display:none" id="'.$ligne[0].'">
      '.genererNuage($tab_nuage,$ligne[1]).'
      </div>

      <script>
      function myFunction(bouton,id) {
        var x = document.getElementById(id);
        if (x.style.display === "none") {
          x.style.display = "block";
          bouton.innerHTML="nuage(-)";
        } else {
          x.style.display = "none";
          bouton.innerHTML="nuage(+)";
        }
      }
      </script><br><hr><br>';
      




		echo"<a class='lientitre' href='$ligne[1]' title='Titre du document'> $ligne[2]</a><br>";

		echo "<a class='liensource' href='$ligne[1]' title='La source'>$ligne[1]  </a><br>";  

		
		
	 }  	



?>

</body>

</html>