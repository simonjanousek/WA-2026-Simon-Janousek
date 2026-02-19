<?php
$name= "";
$message = "";
$age = 0;

 

if($_SERVER["REQUEST_METHOD"] == "POST"){
    $name = $_POST["my_name"];
    if($name == "Šimon")
        {
        $message = "Ahoj Šimone";
    }
        else{

 

            $message = "neznam te";
        }
    
    }

 

?>



 

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>test_php</title>
</head>
<body>
    <h1>Test formuláře</h1>
    <p>Lorem ipsum dolor sit amet consectetur, adipisicing elit. Architecto, nobis officia? Culpa aperiam excepturi magnam quasi, eaque, consequatur dolore odit numquam accusamus repudiandae dolores soluta in? Tenetur cumque ullam necessitatibus!</p>
<p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Quibusdam quas unde expedita minima aut eligendi id, architecto culpa voluptatem ea suscipit quos quo recusandae sapiente ratione natus cum, quasi dicta.</p>
    <form method="post">
        <input type="text" name="my_name" placeholder="zadejte jmeno">
        <input type="number" name="vek" placeholder="zadejte vek">
        <button type="submit">odeslat</button>

 

    </form>


 

    <p>
        <?php  
        echo " jmenuješ se";
        echo $message; 
        echo " a je ti $vek let"
        ?>
    </p>

 

</body>
</html>