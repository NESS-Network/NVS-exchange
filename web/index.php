<?php
ini_set('display_errors', true);
require __DIR__ . '/../lib/Container.php';

use lib\Container;

$error = false;

$config = require __DIR__ . '/../config/config.php';
$fdb = $config['db'];

if (!empty($_POST['name']) && !empty($_POST['value']) && !empty($_POST['days'])) {
    $name = $_POST['name'];
    $value = $_POST['value'];
    $address = $_POST['address'];
    $days = (int)$_POST['days'];
    
    if ($days < 100) {
        $days = 100;
    }

    $slots = Container::createSlots();

    if (!empty($slots->locateSlot($_POST['name']))) {
        $error = 'nvs';
    } else {
        $last_slot_time = $slots->lastSlotTime();

        if ((time() - $slots->lastSlotTime()) < 30) {
            header($_SERVER["SERVER_PROTOCOL"] . " 403 Denied");
            die('Time restriction (30 sec)');
        }

        $slot = $slots->findSlot($_POST['name']);

        if (!empty($slot)) {
            header($_SERVER["SERVER_PROTOCOL"] . " 403 Denied");
            $error = 'db';
        } else {
            $slot_id = $slots->createSlot($_POST['name'], $_POST['value'], $_POST['address'], (int) $_POST['days']);
            header('location: /slot.php?slot=' . $slot_id );
        }

    }
} else {
    $name = '';
    $value = '';
    $address = '';
    $days = 100;
}
?>
<!doctype html>
<html lang="en">
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=Edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Emercoin Name:Value System CRUD interface</title>
    
    <!-- Custom CSS for appealing UI -->
    <style>
       
       /* Laptops */
       @media only screen and (min-width: 1025px) and (max-width: 1280px) {
            
            .btn-primary {
               
               cursor: pointer;
    
           }
    
           .btn-primary:hover {
               background-color: #1F4E79;
           }
    
           input:hover {
                border: 3px solid slateblue;
                border-radius: 6px;
                 }
                  
              }
    
              /* Desktops */
            @media only screen and (min-width: 1281px) {
                .btn-primary, .btn-success, .btn-danger, .copy-button {
               
                cursor: pointer;
    
            }
    
            .btn-primary:hover {
                background-color: #1F4E79;
            }
             
            input:hover {
                border: 3px solid slateblue;
                border-radius: 6px;
                 }
          
              }
        body {
            background-color: #367CA5;
            color: black;
            font-family: Arial, sans-serif;
        }

        .container {
            padding: 20px;
            margin: 20px auto;
            max-width: 90%;
            background-color: #FFFFFF;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .form-label {
            font-weight: bold;
            color: grey;
            position: absolute;
            margin-top: 6%;
            margin-left: 50px;
        }
        
        

        .btn-primary {
            background-color: #367CA5;
            border: none;
            margin-top: 7%;
            margin-left: 50px;
            color: white;
            border-radius: 5px;
            padding: 8px 15px;
            cursor: pointer;
        }

        .btn-primary:active {
            background-color: #1B4F73;
        }
        
 
      
        
        h1, h3 {
            color: #367CA5;
            text-align: center;
        }

        .form-control {
            border: 2px solid #367CA5;
            border-radius: 5px;
            color: #367CA5;
            outline-color: skyblue;
            margin-top: 7%;
            margin-left: 50px;
            width: 90%;
            max-width: 500px;
        }
        
        input {
             padding: 6px;
        }
        

        .form-text {
            color: #1B4F73;
            font-size: 10px;
            margin-left: 55px;
        }
        
        textarea {
           
           outline-color: skyblue;
        }
        
        .alert {
             color: red;
             font-size: medium;
             font-weight: bold;
             display: none;
        }
        
        .nvs-show-alert, .db-show-alert {
             display: block;
        }
        
        .result {
            color: green;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="row">
        <div class="col">
            <h1>EmerNVS CRUD Interface</h1>
            <h3>XBTSX.NCH, NESS, EMC </h3>

  <form method="POST">
    <div class="mb-3">
         <label 
          for="name"     
          class="form-label">
          Name:
         </label><br>
         
   <input 
    
    type="name" 
    class="form-control" 
    id="name" 
    name="name" 
    value="<?=$name?>" 
    placeholder=" dns:ness.bts" 
    required>
                   
                   
      <div 
        id="nameHelp" 
        class="form-text">
        Enter desired or requested NVS name
      </div>
   </div>


    <div class="mb-3">
         <label 
          for="days"     
          class="form-label">
          Days:
         </label><br>
         
   <input 
    
    type="number" 
    class="form-control" 
    id="days" 
    name="days" 
    value="<?=$days?>" 
    min="100"
    required>
                   
                   
      <div 
        id="nameHelp" 
        class="form-text">
        Days (amount of days to store your NVS record):
      </div>
   </div>

    <div class="mb-3">
         <label 
          for="name"     
          class="form-label">
          Address (optional):
         </label><br>
         
   <input 
    
    type="text" 
    class="form-control" 
    id="address" 
    name="address" 
    value="<?=$address?>"
    required>
                   
                   
      <div 
        id="nameHelp" 
        class="form-text">
        Enter your emercoin address (if you want to export this NVS to outer wallet)
      </div>
   </div>
               
      <div class="mb-3">
         <label 
           for="value" 
           class="form-label">
           Value:
         </label><br>
         
   <textarea 
     name="value" 
     id="value" 
     class="form-control" 
     cols="25" 
     rows="6" 
     placeholder="A=127.0.0.1|NS=seed1.emercoin.com,seed2.emercoin.com" 
     required><?=$value?></textarea>
   
   
       <div 
         id="valueHelp" 
         class="form-text">
         Enter desired or requested NVS Value</div>
     </div>
            
     <button 
       type="submit" 
       class="btn btn-primary">
       Create payment slot
     </button>
</form> <br>

<!-- Alert Messages are hidden in CSS by default -->


<?php if ('nvs' === $error): ?>

    <div class="alert alert-danger
     <?php if ('nvs' === $error) 
      echo 'nvs-show-alert'; ?>" 
        role="alert">
        
        NVS with the name 
        <b><?=htmlentities($name)?></b>     
        Already exists. 
    </div><br>
    
<?php elseif ('db' === $error): ?>

    <div
     class="alert alert-danger 
     <?php if ('db' === $error) echo 'db-show-alert'; ?>" 
        role="alert">
        
        Slot with the name 
        <b><?=htmlentities($name)?></b>  
        Already exists. 
        <br> 
        You can't pay it here! 
  
        <a  
            href="/slot.php?slot=<?=$slot['slot_id']?>"> 
            <?=$slot['slot_id']?> 
        </a> 
    </div> 
<?php endif; ?>


<?php if (isset($_GET['msg']) && ('deleted' === $_GET['msg'])): ?>

    <?php
        $name = '';

        if (isset($_GET['name'])) {
            $name = $_GET['name'];
        }
    ?>

    <h3 class='result'>
        Name-Value record <code><?=htmlentities($name)?></code> deleted
    </h3>
    
<?php endif; ?>

        </div>
    </div>
</div>

</body>
</html>
