<?php

      require_once "../session_check.php";
    include "../config.php";
    $user= $_SESSION['user'];

    $athlete_id = !empty($_GET['athleteid']) ? $_GET['athleteid'] : 0;


     try{
    
         $currentYear=date('Y');

        $sql="SELECT 
        a.athlete_id,
        a.first_name,
        a.last_name,
        a.year,
        c.category_id,
        c.category_name,
        d.dept_id,
        d.dept_name,
        p.meet_year,
        dg.degree_id,
        dg.degree_name,
        GROUP_CONCAT(p.event_id) as registered_event_ids,
         GROUP_CONCAT(e.event_name) AS registered_event_names

        FROM athletes a

        JOIN   departments d ON a.dept_id = d.dept_id

        JOIN degree dg ON dg.degree_id= d.degree_id
        
         JOIN categories c ON a.category_id = c.category_id

        LEFT JOIN 
            participation p ON a.athlete_id= p.athlete_id

         LEFT JOIN 
             events e ON p.event_id = e.event_id    
        WHERE 
            a.athlete_id = ? AND
            p.meet_year=?
        GROUP BY 
            a.athlete_id";

         $stmnt=$pdo->prepare($sql);
         $stmnt->execute([$athlete_id,$currentYear]);
         
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

        $degrees = $pdo->query("SELECT degree_id, degree_name FROM degree")->fetchAll(PDO::FETCH_ASSOC);

            // Fetch all head departments
            $headDepartments = $pdo->query("SELECT * FROM headdepartment")->fetchAll(PDO::FETCH_ASSOC);

            // Get athlete's current head department (based on their department)
            $headDeptQuery = $pdo->prepare("SELECT hd_id FROM departments WHERE dept_id = ?");
            $headDeptQuery->execute([$athlete['dept_id']]);
            $athleteHdId = $headDeptQuery->fetchColumn();

            // Fetch departments under that head department
            $departmentsStmt = $pdo->prepare("SELECT * FROM departments WHERE hd_id = ?");
            $departmentsStmt->execute([$athleteHdId]);
            $departments = $departmentsStmt->fetchAll(PDO::FETCH_ASSOC);

            
        $categories=$pdo->query("SELECT * FROM categories");
         $relay_events=$pdo->query("SELECT * FROM events WHERE is_relay=1");

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

    }catch(Exception $e){

        echo "Failed:".$e->getMessage();
    }
    ob_start();
?>

<span style="float:right; cursor:pointer; font-size:22px; font-weight:bold;"
     onclick="document.getElementById('editAthleteModal').style.display='none'">
        &times;</span>
<h3>Update Athlete</h3>
<form action="" method="post">
    <input type="hidden" name="athlete_id" value="<?= $athlete['athlete_id'] ?>">

    <!-- Name -->
    <label>Fist Name:</label>
    <input type="text" name="first_name" value="<?= htmlspecialchars($athlete['first_name']) ?>"><br>

       <label>Last Name:</label>
    <input type="text" name="last_name" value="<?= htmlspecialchars($athlete['last_name']) ?>"><br>

    <!-- Year -->
    <label>Year:</label>
    <select name="year" id="year-select">
        <?php for ($i = 1; $i <= 4; $i++): ?>
            <option value="<?= $i ?>" <?= $athlete['year'] == $i ? 'selected' : '' ?>><?= $i ?></option>
        <?php endfor; ?>
    </select><br>

        <!-- Degree -->
    <label>Degree:</label>
    <select name="degree_id" id="degreeSelect">
        <option value="">-- Select Degree --</option>
        <?php foreach ($degrees as $deg): ?>
            <option value="<?= $deg['degree_id'] ?>" <?= $deg['degree_id'] == $athlete['degree_id'] ? 'selected' : '' ?>
                data-name="<?= htmlspecialchars(strtolower($deg['degree_name'])) ?>">
                <?= htmlspecialchars($deg['degree_name']) ?>
            </option>
        <?php endforeach; ?>
    </select><br>


        <!-- Head Department -->
    <label>Department:</label>
    <select name="hd_id" id="headDeptSelect">
        <option value="">-- Select Head Department --</option>
        <?php foreach ($headDepartments as $hd): ?>
            <option value="<?= $hd['hd_id'] ?>" <?= $hd['hd_id'] == $athleteHdId ? 'selected' : '' ?>>
                <?= htmlspecialchars($hd['hd_name']) ?>
            </option>
        <?php endforeach; ?>
    </select><br>


    <!-- Department -->
    <label>Course:</label>
    <select name="dept_id" id="deptSelect">
        <?php foreach ($departments as $dept): ?>
            <option value="<?= $dept['dept_id'] ?>" <?= $athlete['dept_id'] == $dept['dept_id'] ? 'selected' : '' ?>>
                <?= htmlspecialchars($dept['dept_name']) ?>
            </option>
        <?php endforeach; ?>
    </select><br>

    <!-- Category -->
    <label>Category:</label>
    <select name="category_id" id="category-check">
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
        <div class="relay-events-group">
    <label>Relay Events:</label><br>
    <?php foreach ($relay_events as $relay): 
        $event_id = $relay['event_id'];
        $event_name = $relay['event_name'];

        $allowed_cats = $allowed_categories[$event_id] ?? [];
        $is_checked = in_array($event_id, $registered_event_ids);
        $is_allowed = in_array($athlete['category_id'], $allowed_cats);
    ?>
        <label>
            <input 
                type="checkbox" 
                class="relay-events category-checker"
                name="relay_event_ids[]" 
                 data-event-id="<?= $event_id ?>"
                value="<?= $event_id ?>"
                <?= $is_checked ? 'checked' : '' ?>
                <?= (!$is_allowed && !$is_checked) ? 'disabled' : '' ?>
            >
            <?= htmlspecialchars($event_name) ?>
        </label><br>
    <?php endforeach; ?>
</div>

        </div>
    <button type="submit" name="update">Update</button>
</form>

 <script>
        function loadDepartments() {

        const degreeId = document.getElementById('degreeSelect').value;
        const hdId = document.getElementById('headDeptSelect').value;
        const depSelect = document.getElementById('deptSelect');

        depSelect.innerHTML = '<option value="">-- Loading --</option>';

        if (!degreeId || !hdId) {
            depSelect.innerHTML = '<option value="">-- Select Department --</option>';
            return;
        }

        fetch(`../common_pages/get_departments.php?degree_id=${degreeId}&hd_id=${hdId}`)
            .then(res => res.json())
            .then(data => {
                depSelect.innerHTML = '<option value="">-- Select Department --</option>';
                if (data.length === 0) {
                    depSelect.innerHTML = '<option value="">-- No Courses found --</option>';
                } else {
                    data.forEach(dep => {
                        const option = document.createElement('option');
                        option.value = dep.dept_id;
                        option.textContent = dep.dept_name;
                        depSelect.appendChild(option);
                    });
                }
            })
            .catch(err => {
                depSelect.innerHTML = '<option value="">-- Error loading courses --</option>';
                console.error(err);
            });
    }
    document.getElementById('degreeSelect').addEventListener('change', function() {
    const selectedOption = this.options[this.selectedIndex];
    const degreeName = selectedOption.getAttribute('data-name');
    const yearSelect = document.getElementById('year-select');

    // Reset year options
    yearSelect.innerHTML = '<option value="">-- Select Year --</option>';

    // UG → 4 years, PG → 2 years
    if (degreeName === 'undergraduate') {
        for (let i = 1; i <= 4; i++) {
            const option = document.createElement('option');
            option.value = i;
            option.textContent = i;
            yearSelect.appendChild(option);
        }
    } else if (degreeName === 'postgraduate') {
        for (let i = 1; i <= 2; i++) {
            const option = document.createElement('option');
            option.value = i;
            option.textContent = i;
            yearSelect.appendChild(option);
        }
    } else {
        yearSelect.innerHTML = '<option value="">-- Select Year --</option>';
    }
});

          document.getElementById('headDeptSelect').addEventListener('change', loadDepartments);
        document.getElementById('degreeSelect').addEventListener('change', loadDepartments);
      
    </script>
<?php
// Capture the output into a variable
$htmlForm = ob_get_clean();

// Now output JSON
header('Content-Type: application/json');
echo json_encode([
    'html' => $htmlForm,
    'allowedCategoriesByEvent' => $allowed_categories
]);
exit;
?>

