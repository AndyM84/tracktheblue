<?php   
        require_once('functions.php');
    

        $formSubmitted = false;
        $citation_id = $citation_date = $city = $state = $precinct = $citation_classification = "";
        $fine_ammount= $mandatory_court = $video ="";
        if(array_key_exists('citation_id', $_POST)) {
            $citation_id = clean_input($_POST["citation_id"]);
            $citation_date = clean_input($_POST["citation_date"]);
            $city = clean_input($_POST["city"]);
            $state = clean_input($_POST["state"]);
            $precinct= clean_input($_POST["precinct"]);
            $citation_classification = clean_input($_POST["citation_classification"]);
            $mandatory_court = clean_input($_POST["mandatory_court"]);
            $fine_ammount = $_POST['fine_ammount'];
            $video = clean_input($_POST['video']);
            
            $formSubmitted = true;
            
        }    
 ?>





<!DOCTYPE html>
<html lang="en">
  <head>
<!-- Required meta tags -->
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

<!-- Bootstrap CSS -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
<link  rel="stylesheet"href=style/stylesheet.css>

    <title>Tracking The Blue</title>

  </head>
  
    <body>
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>


    <div id =content class="d-flex justify-content-center" >
 <!--TODO: need to fix Jumbotron so that it fills the screen, jmbotron-fluid should
        Have fixed this, but it fills it's parrent element, but it's parrent item doese not fill the screen-->
          
    <div class="container-fluid">
        <div class="jumbotron jumbotron-fluid" style="background-color: blue; color: yellow;">
            <div>
                <h1>Track the Blue</h1>
            </div>
        </div>
    
    
        <div id="about_the_project">
            <p>Isum Copy... sdfskdhf;kjhsdjfh</p>    
            <p>sdfjhk sdjfhksdhf</p> 
            <p>skdkfjlsdkjf sdfkjsdl sdlfkj dirn sldkfj</p> 
            <p>KDSjflskjd  twefkljs kjliji nfdsnl. </p>    
        </div>
        
        <div>
            <div id="citation_information">
                <p>Citation Information</p>
            </div>
        </div>
        
        <!-- PHP Variables set to null for the form-->
        
      

        <form  action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>"  method="post">
        
            <div class="form-group">
                <label for="citation_id" >Citation #</label>
                <input type ="text" class="form-control" name="citation_id" value="<?php echo $citation_id;?>">
            </div>
        
            <div class="form-group">
                <label for="$citation_date" >Date citation was issued</label>
                <input type ="datetime-local" class="form-control" name="citation_date" value="<?php echo $citation_date;?>">
            </div>

            <div class="form-group">
                <label for="city" >City</label>
                <input type ="text" class="form-control" name="city" value="<?php echo $city;?>">
            </div>

            <div class="form-group">
                <label for="state" >State</label>
                <input type ="text" class="form-control" name="state" value="<?php echo  $state;?>">
            </div>

            <div class="form-group">
                <label for="precinct" >Precinct</label>
                <input type ="text" class="form-control" name="precinct" value="<?php echo $precinct;?>">
            </div>    

            <div class="form-group">
                <label for="citation_classification" >citation classification</label>
                <input type ="text" class="form-control" name="citation_classification" value="<?php echo $citation_classification;?>">
            </div>    

            <div class="form-group">
                <label for="mandatory_court" >Mandatory court</label>
                <input type ="text" class="form-control" name="mandatory_court" value="<?php echo $mandatory_court;?>">
            </div>    

            <div class="form-group">
                <label for="fine_ammount" >Fine Amount</label>
                <input type ="number" class="form-control" name="fine_ammount" value="<?php echo $fine_ammount;?>">
            </div>    

            <div class="form-group">
                <label for="vido" >Link to video of event</label>
                <input type ="hyperlink" class="form-control" name="video" value="<?php echo $video;?>">
            </div>    

            <div class="form-group d-flex justify-content-center">
            <!--  <a href="Civi_into.html"><button type="submit" class="btn btn-default">Your Info</button></a>-->
            <button type="submit" class="btn btn-default" name="submit" value="Submit"> Submit</button>
        </div>

        </form>
        
        


    <?php
        if($formSubmitted){  

        echo"<h2>Your Input:</h2>";
        echo $citation_id;
        echo "<br>";
        echo $citation_date;
        echo "<br>" ; 
        echo $city ;
        echo "<br>";
        echo $state; 
        echo "<br>";
        echo $precinct;
        echo "<br>";
        echo $citation_classification;
        echo "<br>";
        echo $mandatory_court;
        echo "<br>";
        echo $citation_date;
        echo "<br>";
        echo $fine_ammount;
        echo "<br>";
        echo $video;
        }      
    ?>
       
        </div>    
    </body>
</html>