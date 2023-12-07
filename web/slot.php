<?php
ini_set('display_errors', true);
error_reporting(E_ALL);

require __DIR__ . '/../lib/Container.php';

use lib\Container;

if (empty($_GET['slot'])) {
    header($_SERVER["SERVER_PROTOCOL"] . " 404 Not Found");
    die('Slot not found');
}

$slots = Container::createSlots();

$slot = $slots->showSlot($_GET['slot']);

if (empty($slot)) {
    header($_SERVER["SERVER_PROTOCOL"] . " 404 Not Found");
    die('Slot not found');
}

if ('PAYED' === $slot['status']) {
    $result = true;
} elseif (isset($_POST["check"])) {
    $error = false;

    try {
        $result = $slots->processSlot($_GET['slot']);
    } catch (Exception $e) {
        $result = false;

        if (false !== strpos($e->getMessage(), 'pending operations')) {
            $error = 'There are pending operation on that name (' . $slot['name'] . ') '
            . ' <br/> This can be New name or edit name operation '
            . ' <br/> Try to wait 10 min';
        } elseif (false !== strpos($e->getMessage(), 'this name tx is not yours')) {
            $error = 'This NVS was sent to address which is outside this exchange'
            . ' <br/> You can\'t modify this record here';
        } else {
            $error = $e->getMessage();
        }
    }
} elseif (isset($_POST["delete"])) {
    $result = $slots->deleteSlot($_GET['slot']);
    header('location: /?msg=deleted&name=' . urlencode($slot['name']));
    die();
}

$slot = $slots->showSlot($_GET['slot']);

$actual_link = (empty($_SERVER['HTTPS']) ? 'http' : 'https') . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible"   
    content="IE=Edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

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

           .btn-success:hover {

                    background-color: chartreuse;
                             }
          .btn-danger:hover {

                    background-color: firebrick;
                           }           
                  
              }
    
              /* Desktops */
            @media only screen and (min-width: 1281px) {
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

            .btn-success:hover {

                  background-color: chartreuse;
                     }
            .btn-danger:hover {

                  background-color: firebrick;
                    }             
          
              }
        body {
            background-color: #367CA5;
            color: black;
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }

        .container {
            padding: 20px;
            margin: 20px auto;
            max-width: 85%;
            background-color: #FFFFFF;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .card {
            border: none;
            height: 100%;
            
        }

        .copy-button {
            height: 25px;
            display: flex;
            justify-content: center;
            align-items: center;
            position: relative;
        }

        .tip {
            background-color: #263646;
            padding: 0 14px;
            line-height: 27px;
            position: absolute;
            border-radius: 4px;
            z-index: 100;
            color: #fff;
            font-size: 12px;
            animation-name: tip;
            animation-duration: 0.6s;
            animation-fill-mode: both;
        }

        .tip:before {
            content: "";
            background-color: #263646;
            height: 10px;
            width: 10px;
            display: block;
            position: absolute;
            transform: rotate(45deg);
            top: -4px;
            left: 17px;
        }

        #copied_tip {
            animation-name: come_and_leave;
            animation-duration: 1s;
            animation-fill-mode: both;
            bottom: -35px;
            left: 2px;
        }

        h1, h2, h3 {
            color: #367CA5;
            font-size: 18px;
        }

        .d-flex {
            display: flex;
        }

        .justify-content-between {
            justify-content: space-between;
        }

        .align-items-center {
            align-items: center;
        }

        .mb-3 {
            margin-bottom: 20px;
        }

        .me-2 {
            margin-right: 0.5rem;
        }

        .text-line {
            text-decoration: underline;
            color: white;
            font-style: italic;
            font-size: 0.8em;
        }
        
        .name, .value {
             color: white;
             font-style: italic;
             font-size: 0.8em;
        }

        .btn-primary {
            background-color: #367CA5;
            border: none;
            color: white;
            border-radius: 5px;
            padding: 8px;
            margin-top: 0.5%;
            cursor: pointer;
            
        }

        .btn-primary:active {
            background-color: #1B4F73;
        }
        
        .copy-button:active {
             background-color: lightgreen;
        }

        .btn-success {
            background-color: #5cb85c;
            border: none;
            color: white;
            border-radius: 5px;
            padding: 8px 15px;
            cursor: pointer;
        }

        .btn-danger {
            background-color: #d9534f;
            border: none;
            color: white;
            border-radius: 5px;
            padding: 8px 15px;
            cursor: pointer;
        }
        
        .btn-danger:active {
             background-color: lightcoral;
        }
        
        input {
             border: 2px solid #367CA5;
             outline-color: skyblue;
             border-radius: 3px;
             padding: 7px;
             
        }
        
        .payment-success-msg {
             color: #5cb85c;
             font-style: italic;
             font-weight: bold;
             display: none;
        }
        
        .tx-not-conf-yet-msg {
             color: #d9534f;
             font-style: italic;
             font-weight: bold;
             display: none;
        }
        
        .money-send-conf-form,
        .float-start,
        .deleting-slot-form {
             display: none;
        }
        
        .display-payment-conf-nvs-created,
        .display-money-send-conf-form,
        .display-tx-not-conf-yet-msg,
        .display-money-send-conf-form,
        .display-deleting-slot-form {
             display: block;
        }
        
    </style>

    <title>
         Slot # <?= $slot['slot_id'] ?>
    </title>
</head>

<body>

<div class="container">
   <div class="row">
      <div class="col">
           
         <h1> 
              <a href="/">
                   &lt;&lt;&lt; GO BACK
              </a> 
          </h1>

            <a href="javascript:void(0)" onclick="CreateBookmarkLink()">
                Bookmark page (press Ctrl+D to bookmark this page as permanent link)
            </a> 
            <br/><br/>
            <div id="copy_url">
                <a href="javascript:void(0)" onclick="copy('<?=$actual_link?>','#copy_url')" id="copy_url" >
                    Copy link
                </a> 
            </div>
    <?php if (!isset($result) || (false === $result)): ?>
            
    <?php foreach ($slot['addr'] as $name => $addr): ?>
    
     <h2>
           <b>
             <?=$addr['descr']?>
             </b> 
             Send 
             <?=$addr['min_sum']?>
             <?=$name?> to:
      <div 
          class="d-flex justify-content-between align-items-center mb-3">
         
     <span class="text-line me-2">
          <?=  $addr['addr'] ?>
     </span>
                    
     <button 
      onclick="copy('<?=  $addr['addr'] ?>','#copy_button_<?=$name?>')" 
      id="copy_button_<?=$name?>" 
      class="btn btn-success copy-button">
     Copy
     </button>
     </div>
  </h2>
  
    <?php endforeach; ?>

    <?php endif; ?>

    <?php if (isset($result) && $result && isset($slot['nvs'])): ?>
       <h3>NAME: <br/>
         <span class="name">
                <?= nl2br(htmlentities($slot['nvs']['name'])) ?>
         </span>
       </h3>
       <?php else: ?>
       <h3>NAME: </br/>
          <span class="name">
            <?= htmlentities($slot['name']) ?>
          </span>
       
       </h3>
        <?php endif; ?>
       
       <?php if (isset($result) && $result && isset($slot['nvs'])): ?>
       <h3>VALUE: <br/>
         <span class="value">
                <?= nl2br(htmlentities($slot['value'])) ?>
         </span>
       </h3>
       <?php else: ?>
       <h3>VALUE: <br/>
         <span class="value">
              <?= nl2br(htmlentities($slot['value'])) ?>
         </span>
       </h3>
        <?php endif; ?>
       
       <?php if (isset($result) && $result && isset($slot['nvs'])): ?>
       <h3>Expires in: <br/>
         <span class="value">
                <?=round($slot['nvs']['expires_in']/144)?> days
         </span>
       </h3>
       <?php else: ?>
       <h3>DAYS: <br/>
         <span class="value">
                + <?= nl2br(htmlentities($slot['addr']['EMC']['days'])) ?>
         </span>
       </h3>
        <?php endif; ?>

            <div 
              class="payment-success-msg <?php if (isset($result) && $result): ?>display-payment-conf-nvs-created<?php endif; ?>"
              role="alert">
              Payment confirmed !    
                 <br>
               Name-Value record created or updated !
            </div>
                    
                    
<form method="POST" class="money-send-conf-form <?php if (!isset($result) || (false === $result)): ?>display-money-send-conf-form<?php endif; ?>">
      <input 
         type="hidden" 
         name="check" 
         value="" 
         />
      
   <button 
     type="submit" 
     class="btn btn-primary">
     Confirm money send
   </button>
</form>

    <?php if (isset($result) && (false === $result) && (false !== $error)): ?>
        <code style="color: red;">
        <?=$error?>
        </code>
    <?php endif; ?>
                    
       <div 
        class="tx-not-conf-yet-msg <?php if (isset($result) && (false === $result) && (false === $error)): ?>display-tx-not-conf-yet-msg<?php endif; ?>"
        role="alert">       
        Transaction not confirmed yet !
       </div>
                    

<br>

  <div class="deleting-slot-form <?php if (empty($result) && ('GENERATED' === $slot['status'])): ?>display-deleting-slot-form<?php endif; ?>">
   <form method="POST"
       class="float-right">
       <input 
         type="hidden" 
         name="delete" 
         value="" 
         />
        <button 
         type="submit" 
         class="btn btn-danger">
         Delete slot (!)
        </button>
     </form>
    </div>

    <?php if (isset($result) && $result): ?>
        <a 
            class="btn btn-primary"
            href="/edit.php?slot=<?= $slot['slot_id'] ?>">
            Edit Name:Value record
        </a>
    <?php endif; ?>
        </div>
    </div>
</div>

<script type="text/javascript">
    function copy(text, target) {
        setTimeout(function() {
            $('#copied_tip').remove();
        }, 800);
        $(target).append("<div class='tip' id='copied_tip'>Copied!</div>");
        var input = document.createElement('input');
        input.setAttribute('value', text);
        document.body.appendChild(input);
        input.select();
        var result = document.execCommand('copy');
        document.body.removeChild(input)
        return result;
    }

    function CreateBookmarkLink() {

        title = "NVS exchange"; 
        //or title = document.title

        url = "<?=$actual_link?>";
        //or url = location.href

        if (window.sidebar) { // Mozilla Firefox Bookmark
            window.sidebar.addPanel(title, url,"");
        } else if( window.external && window.external.AddFavorite ) { // IE Favorite
            window.external.AddFavorite( url, title); 
        }

    }
</script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js" crossorigin="anonymous">
</script>

</body>
</html>
