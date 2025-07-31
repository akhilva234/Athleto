<?php

       require_once "../session_check.php";
    $user= $_SESSION['user'];
    include "../config.php";

    try{
             $event_cat_status=$pdo->query("SELECT e.event_id, e.event_name, c.category_name,c.category_id
                                        FROM events e
                                        JOIN event_categories ec ON e.event_id = ec.event_id
                                        JOIN categories c ON ec.category_id = c.category_id;
                                        ");

     
         $allowed_categories=[];

         foreach($event_cat_status as $row){

            $event_id=$row['event_id'];
            $category_id=$row['category_id'];

            if(!isset($allowed_categories[$event_id])){
                $allowed_categories[$event_id]=[];
            }
            $allowed_categories[$event_id][]=$category_id;

         }
         header('Content-Type: application/json');
            echo json_encode([
                'allowedCategoriesByEvent' => $allowed_categories
            ]);
            exit;
    }catch(Exception $e){
        echo "Failed:".$e->getMessage();
    }
   
                                   
?>