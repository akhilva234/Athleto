<?php

      require_once "../session_check.php";
    include "../config.php";
    $user= $_SESSION['user'];

    $athlete_id = !empty($_GET['athleteid']) ? $_GET['athleteid'] : 0;


     try{
    
        $sql="SELECT 
        a.athlete_id,
        a.first_name,
        a.last_name,
        a.year,
        c.category_id,
        c.category_name,
        d.dept_id,
        d.dept_name,
        GROUP_CONCAT(p.event_id) as registered_event_ids,
         GROUP_CONCAT(e.event_name) AS registered_event_names

        FROM athletes a

        JOIN   departments d ON a.dept_id = d.dept_id
        
         JOIN categories c ON a.category_id = c.category_id

        LEFT JOIN 
            participation p ON a.athlete_id= p.athlete_id

         LEFT JOIN 
             events e ON p.event_id = e.event_id    
        WHERE 
            a.athlete_id = ?
        GROUP BY 
            a.athlete_id";

         $stmnt=$pdo->prepare($sql);
         $stmnt->execute([$athlete_id]);
         
         $athlete=$stmnt->fetch(PDO::FETCH_ASSOC);

          if(!$athlete){
            throw new Exception("Athlete not found!.");
         }

         $registered_event_ids=!empty($athlete['registered_event_ids'])
         ? explode(',',$athlete['registered_event_ids']) : [];

         $registered_event_names=!empty($athlete['registered_event_names'])
         ? explode(',',$athlete['registered_event_names']) : [];

        $isFull=count($registered_event_ids)>=3;

        $events=$pdo->query("SELECT * FROM events WHERE is_relay=0");
        $departments=$pdo->query("SELECT * FROM departments");
        $categories=$pdo->query("SELECT * FROM categories");
         $relay_events=$pdo->query("SELECT * FROM events WHERE is_relay=1");

    }catch(Exception $e){

        echo "Failed:".$e->getMessage();
    }
?>

<span style="float:right; cursor:pointer; font-size:22px; font-weight:bold;"
     onclick="document.getElementById('editAthleteModal').style.display='none'">
        &times;</span>
<h3>Update Athlete</h3>
<form action="update_athletes.php" method="post">
    <input type="hidden" name="athlete_id" value="<?= $athlete['athlete_id'] ?>">

    <!-- Name -->
    <label>Fist Name:</label>
    <input type="text" name="first_name" value="<?= htmlspecialchars($athlete['first_name']) ?>"><br>

       <label>Last Name:</label>
    <input type="text" name="last_name" value="<?= htmlspecialchars($athlete['last_name']) ?>"><br>

    <!-- Year -->
    <label>Year:</label>
    <select name="year">
        <?php for ($i = 1; $i <= 4; $i++): ?>
            <option value="<?= $i ?>" <?= $athlete['year'] == $i ? 'selected' : '' ?>>Year <?= $i ?></option>
        <?php endfor; ?>
    </select><br>

    <!-- Department -->
    <label>Department:</label>
    <select name="dept_id">
        <?php foreach ($departments as $dept): ?>
            <option value="<?= $dept['dept_id'] ?>" <?= $athlete['dept_id'] == $dept['dept_id'] ? 'selected' : '' ?>>
                <?= htmlspecialchars($dept['dept_name']) ?>
            </option>
        <?php endforeach; ?>
    </select><br>

    <!-- Category -->
    <label>Category:</label>
    <select name="category_id">
        <?php foreach ($categories as $cat): ?>
            <option value="<?= $cat['category_id'] ?>" <?= $athlete['category_id'] == $cat['category_id'] ? 'selected' : '' ?>>
                <?= htmlspecialchars($cat['category_name']) ?>
            </option>
        <?php endforeach; ?>
    </select><br>

    <!-- Events -->
     <div class="event-container">
            <div class="individual-events">
                 <label>Individual Events:</label><br>
                <?php foreach ($events as $event): ?>
                    <label>
                        <input 
                            class="individual-event"
                            type="checkbox" 
                            name="event_ids[]" 
                            value="<?= $event['event_id'] ?>"
                            <?= in_array($event['event_id'], $registered_event_ids) ? 'checked' : '' ?>
                            <?= !$isFull || in_array($event['event_id'], $registered_event_ids) ? '' : 'disabled' ?>
                        >
                        <?= htmlspecialchars($event['event_name']) ?>
                    </label><br>
                <?php endforeach; ?>
            </div>
            <div class="relay-events">
             <label>Relay Events:</label><br>
            <?php foreach ($relay_events as $relay): ?>
             <label>
            <input 
                type="checkbox" 
                name="event_ids[]" 
                value="<?= $relay['event_id'] ?>"
                <?= in_array($relay['event_id'], $registered_event_ids) ? 'checked' : '' ?>
                <?= !$isFull || in_array($relay['event_id'], $registered_event_ids) ? '' : 'disabled' ?>
            >
            <?= htmlspecialchars($relay['event_name']) ?>
        </label><br>
        <?php endforeach; ?>
            </div>
        </div>
    <button type="submit">Update</button>
</form>
