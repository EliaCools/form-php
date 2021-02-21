<?php


//this line makes PHP behave in a more strict way
declare(strict_types=1);


//we are going to use session variables so we need to enable sessions
session_start();

function whatIsHappening() {
    echo '<h2>$_GET</h2>';
    var_dump($_GET);
    echo '<h2>$_POST</h2>';
    var_dump($_POST);
    echo '<h2>$_COOKIE</h2>';
    var_dump($_COOKIE);
    echo '<h2>$_SESSION</h2>';
    var_dump($_SESSION);
}

//whatIsHappening();


//your products with their price.


if($_GET['food']==0){
    $products = [
        ['name' => 'Cola', 'price' => 2],
        ['name' => 'Fanta', 'price' => 2],
        ['name' => 'Sprite', 'price' => 2],
        ['name' => 'Ice-tea', 'price' => 3],
    ];
}
if($_GET['food']==1 ||$_GET['food']===null ){
    $products = [
        ['name' => 'Club Ham', 'price' => 3.20],
        ['name' => 'Club Cheese', 'price' => 3],
        ['name' => 'Club Cheese & Ham', 'price' => 4],
        ['name' => 'Club Chicken', 'price' => 4],
        ['name' => 'Club Salmon', 'price' => 5]
    ];
}




if (!isset($_SESSION)) {
    session_start();
}


function deliverytime(){
    $date=getdate();
    $hour=$date["hours"];
    $min=$date["minutes"];

    $newhour = new DateTime($hour.':'.$min);

    if(isset($_POST['express_delivery'])){
        date_add($newhour, date_interval_create_from_date_string('45 minutes'));
    }else{
        date_add($newhour, date_interval_create_from_date_string('2 hours'));
    }

    return date_format($newhour,'H:i');
}

if(isset($_SESSION["time"])){
    echo' <div class="alert alert-success" role="alert">
              order '. $_SESSION["ordercount"] .' send <br/>
            Time of delivery: '. $_SESSION["time"] .'
            </div>';
}



$email = $_POST['email'];
$street = $_POST['street'];
$streetNumber = $_POST['streetnumber'];
$city = $_POST['city'];
$zipcode = $_POST['zipcode'];


if (!isset($_COOKIE['count'])){
   $_COOKIE['count']=0;
}

$totalValue=$_COOKIE['count'];
global $products;





if(isset($_SESSION["wronginput"])){
    $emailErr= $streetErr = $streetNumberErr = $streetNumberNANErr = $cityErr= $zipcodeErr = $zipcodeNANErr = $producterr= "";
    if (!filter_var($_SESSION["email"], FILTER_VALIDATE_EMAIL)) {$emailErr=  "Invalid email <br/>";}
    if (empty($_SESSION["street"])) {$streetErr = " Street required field <br/>";}
    if (empty($_SESSION["streetNumber"])) {$streetNumberErr =  " Street number required field <br/>";} elseif (!is_numeric($_SESSION["streetNumber"])) {
    $streetNumberNANErr = "Street number field can only contain numbers <br/>";}
    if (empty($_SESSION["city"])) { $cityErr = " City required field <br/>";}
    if (empty($_SESSION['producterr'])){$producterr= "No food or drinks selected";}

    echo ' <div class="alert alert-warning" role="alert">
     ' . $emailErr . $streetErr . $streetNumberErr . $streetNumberNANErr . $cityErr . $zipcodeErr . $zipcodeNANErr . $producterr . '
          </div>';

}


    if (isset($_POST['button'])) {





        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {$_SESSION['email']=$email;}
        if (empty($street)){unset($_SESSION['street']);} else {$_SESSION['street']=$street;}
        if (empty($streetNumber)){unset($_SESSION['streetNumber']);}else{$_SESSION['streetNumber']=$streetNumber;}
        if (empty($city)){unset($_SESSION['city']);}else{$_SESSION['city']=$city;}
        if (empty($zipcode)){unset($_SESSION['zipcode']);}else{$_SESSION['zipcode']=$zipcode;}
        if (array_sum($_POST["products"])==0){$_SESSION['producterr'];}else{unset($_SESSION['producterr']);}



        if (!filter_var($email, FILTER_VALIDATE_EMAIL) || empty($street) || empty($streetNumber) || !is_numeric($streetNumber) || empty($city) || !is_numeric($zipcode) || array_sum($_POST["products"])==0 ){


             header("Refresh:0");
             unset($_SESSION["time"]);
             $_SESSION['wronginput']=true;
        }

        else{
            //for($i=0;$i<count($products);$i++){
//    if(isset($_POST['products'][$i])) {
//        if($_POST["express_delivery"]){
//            $totalValue += $products[$i]["price"]+$_POST["express_delivery"];
//        }else{
//            $totalValue += $products[$i]["price"];
//        }
//        setcookie("count",strval($totalValue));
//    }
//}
            for($i=0;$i<count($products);$i++){
                if($_POST['products'][$i]>=1) {
                    if($_POST["express_delivery"]){
                        $totalValue += $products[$i]["price"]*$_POST["products"][$i]+$_POST["express_delivery"];
                    }else{
                        $totalValue += $products[$i]["price"]*$_POST["products"][$i];
                    }
                    setcookie("count",strval($totalValue));
                }
            }



            unset($_SESSION["wronginput"]);
            //mail("eliacools@hotmail.com","orderform","test");
            if(!isset($_SESSION["ordercount"])){
                $_SESSION["ordercount"]=1;
            }else{$_SESSION["ordercount"]++;}

            $_SESSION["time"]=deliverytime();

            if(isset($_GET["food"])){
            header("Location:http://orderform.local/?food=" .$_GET["food"] );
            }else{
            header("Location:http://orderform.local/" );
            }
            exit;

        }

}

require 'form-view.php';






