<?php
  session_start();
    include "../config.php";

    $message='';

?>
<?php

    if($_SERVER['REQUEST_METHOD']=='POST'){

        $fname=$_POST['firstname'];
        $lname=$_POST['lastname'];
       

        $dep_id = isset($_POST['dep_id']) ? (int)$_POST['dep_id'] : 0;
         $year=isset($_POST['year-select']?(int)$_POST['year-select']:0);
        $cat_id = isset($_POST['cat_id']) ? (int)$_POST['cat_id'] : 0;
        $event_ids = isset($_POST['event_ids']) ? $_POST['event_ids'] : [];

         $depCheck = $pdo->prepare("SELECT dept_id FROM departments WHERE dept_id = ?");
        $depCheck->execute([$dep_id]);
            if (!$depCheck->fetch()) {
                die("Invalid Department selected.");
            }

            $catCheck = $pdo->prepare("SELECT cat_id FROM categories WHERE cat_id = ?");
            $catCheck->execute([$cat_id]);
            if (!$catCheck->fetch()) {
                die("Invalid Category selected.");
            }

            $validEvents = [];
            foreach($event_ids as $eid) {
                $eid = (int)$eid;
                $eventCheck = $pdo->prepare("SELECT event_id FROM events WHERE event_id = ?");
                $eventCheck->execute([$eid]);
                if ($eventCheck->fetch()) {
                    $validEvents[] = $eid; 
                }
            }

            if (empty($validEvents)) {
                die("No valid events selected.");
            }
            try{
            

            $pdo->beginTransaction();

                $athleteSql=$pdo->prepare("insert into athlete (first_name,last_name,category_id,dept_id,year) 
        values(:fname,:lname,:category,:department,:year)");

        $athleteSql->execute([
            'fname' => $fname,
            'lname' => $lname,
            'category' => $cat_id,
            'department' => $dep_id,
            'year' => $year
        ]);


        $athleteId=$pdo->lastInsertId();

        $participation=$pdo->prepare("insert into participation (athlete_id,event_id) values(:athleteid,:eventid)");

        foreach($validEvents as $event_id)
        $participation->execute([
            'athleteid'=>$athleteId,
            'eventid'=>$event_id
        ]);

         $pdo->commit();
        echo "Athlete and participation successfully registered!";
        }catch(Exception $e){

            $pdo->rollBack();
        echo "Failed: " . $e->getMessage();

        }
        

    }

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body>
    <h1>Add Athletes</h1>
    <div class="insert-container">
         <div class="form-container">
            <form action="" method="post" class="form">
            <div class="username fname">
                Firstname<br>
                <input type="text" placeholder="Firstname" name="firstname" class="username-input fname-input">
            </div>
            <div class="username lname">
                Lastname<br>
                <input type="text" placeholder="Lastname" name="Lastname" class="username-input lname-input">
            </div>
             <div class="cat-id-container">
                Category
                 <?php
                    $category=$pdo->query("select category_id,category_name from categories");
                ?>
                <br><select name="cat-select"  class="cat-insert">
                    <option value="">-- Select Action --</option>
                <?php foreach($category as $cat) :?>
                    
                    <option value="<?=htmlspecialchars($cat['category_id'])?>">
                    <?=htmlspecialchars($cat['category_name'])?>
                <?php endforeach;?> 
                 </select>   
            </div>
             <div class="event-id-container">
                Events(Max 3)
                <br>
              <?php
                $events = $pdo->query("SELECT event_id, event_name FROM events")->fetchAll(PDO::FETCH_ASSOC);
                ?>
             <?php foreach($events as $event) :?>
                 <input type="checkbox" name="events[]" value="<?= htmlspecialchars($event['event_id']) ?>"> 
                    <?= htmlspecialchars($event['event_name']) ?><br>
                <?php endforeach; ?>   
            </div>
            <div class="dep-id-container">
                Department
                <?php
                    $departments=$pdo->query("select dept_id,dept_name from departments");
                ?>
                <br><select name="dep-select"  class="dep-insert">
                <option value="">-- Select Action --</option>
                <?php foreach($departments as $department) :?>
                    
                    <option value="<?=htmlspecialchars($department['dept_id'])?>">
                    <?=htmlspecialchars($department['dept_name'])?>
                <?php endforeach;?>        
                 </select>   
            </div>
            <div class="year-container">
                Year
                <br><select name="year-select"  class="year-insert">
                    <option value="">-- Select Action --</option>
                    <option value="1" class="year-options">1</option>
                    <option value="2" class="year-options">2</option>
                    <option value="3" class="year-options">3</option>
                    <option value="4" class="year-options">4</option>
                 </select>   
            </div>
            <div class="submit-container">
            <input type="submit" value="Add" class="add-btn" name="submit">
           </div>
        </form>
    </div>
</body>
</html>