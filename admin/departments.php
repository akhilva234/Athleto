<?php

    require_once "../session_check.php";
     include_once "../nocache.php";
    include "../config.php";
    if(isset($_POST['deptadd'])){
        require 'add_dept.php';
    }

     $message='';
        if (isset($_SESSION['dept-msg'])) {
            $message = $_SESSION['dept-msg'];
            unset($_SESSION['dept-msg']);
        }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script src="../assets/js/toast.js"></script>
    <link rel="stylesheet" href="../assets/css/common.css">
     <link rel="stylesheet" href="../assets/css/common_css/tables.css">
     <link rel="stylesheet" href="../assets/css/result_form.css">
     <link rel="stylesheet" href="../assets/css/common_css/message.css">
     <link rel="stylesheet" href="../assets/css/add_depts.css">
</head>
<body data-view="departments">
    <h2>Departments</h2>
    
        <?php
        try {

            $sql = "SELECT h.hd_id, h.hd_name, d.dept_id, d.dept_name
        FROM headdepartment h
        LEFT JOIN departments d ON h.hd_id = d.hd_id
        ORDER BY h.hd_id, d.dept_name";

$rows = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);

$departments = [];
foreach ($rows as $row) {
    $hdId = $row['hd_id'];
    if (!isset($departments[$hdId])) {
        $departments[$hdId] = [
            'hd_id' => $hdId,
            'hd_name' => $row['hd_name'],
            'courses' => []
        ];
    }
    if ($row['dept_id'] !== null) {
        $departments[$hdId]['courses'][] = [
            'dept_id' => $row['dept_id'],
            'dept_name' => $row['dept_name']
        ];
    }
}

// Reindex array to numeric keys
$departments = array_values($departments);


        } catch (PDOException $e) {
            echo "Query failed: " . $e->getMessage();
            exit;
        }
        ?>

      <br>
    <button class="add-btn">
        <i class="fas fa-plus-circle"></i>
        Add Department
    </button>
        <div class="participants-table-container table-whole-container">
                <table class="participants-table departments-table">
    <thead>
        <tr>
            <th>SI.NO</th>
            <th>Department Id</th>
            <th>Name</th>
            <!-- <th>Actions</th> -->
        </tr>
    </thead>
    <tbody>
    <?php $count = 1; ?>
    <?php foreach ($departments as $dept): ?>
        <tr id="row-<?= $dept['hd_id'] ?>">
            <td><?= $count++ ?></td>
            <td><?= htmlspecialchars($dept['hd_id']) ?></td>
            <td>
                <strong><?= htmlspecialchars($dept['hd_name']) ?></strong>
                <button class="toggle-courses-btn " data-hd-id="<?= $dept['hd_id'] ?>">▼ Courses</button>

                <!-- Dropdown courses list -->
                <div class="courses-dropdown" id="courses-<?= $dept['hd_id'] ?>" style="display:none; margin-top:5px;">
                    <ul class="course-list">
                        <?php if (!empty($dept['courses'])): ?>
                            <?php foreach ($dept['courses'] as $course): ?>
                                <li>
                                    <?= htmlspecialchars($course['dept_name']) ?>
                                    <button 
                                        class="delete-course-btn " 
                                        data-course-id="<?= $course['dept_id'] ?>" 
                                        data-hd-id="<?= $dept['hd_id'] ?>">
                                        Delete
                                    </button>
                                </li>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <li>No courses added yet.</li>
                        <?php endif; ?>
                    </ul>
                    <button class="add-course-btn " data-hd-id="<?= $dept['hd_id'] ?>">+ Add Course</button>
                </div>
            </td>
            <!-- <td>
                <button class="delete-dept-btn" data-hd-id="<?= $dept['hd_id'] ?>">Delete Department</button>
            </td> -->
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>

    </div>
    <div class="result-form-container modal deptmodal">
        <div class="modal-container">
            <h3 class="result-form-head">Add Department</h3>
        <form action="" class="result-form" method='post'>
            <input type="text" class="dept-name input-style" name="dept_name" required><br> 
            <br>
            <input type="submit" class="submit-btn btns" name="deptadd" value="Add">
            <button type="button" class="cancel-btn btns">Cancel</button>
        </form>
        </div>
        </div>
        <?php if (!empty($message)): ?>
    <script>
    <?php if (strpos($message, 'Failed') !== false || strpos($message, 'Invalid') !== false): ?>
        toastr.error(<?= json_encode($message) ?>);
    <?php else: ?>
        toastr.success(<?= json_encode($message) ?>);
    <?php endif; ?>
    </script>
<?php endif; ?>


<!-- Add Course Modal -->
<div id="addCourseModal" class="modal">
  <div class="modal-content">
    <span class="close-btn">&times;</span>
    <h3>Add New Course</h3>
    <form id="addCourseForm">
      <input type="hidden" id="hd_id" name="hd_id">
      
      <label for="dept_name">Course Name</label>
      <input type="text" id="dept_name" name="dept_name" placeholder="Enter course name" required>
       <label for="degree_id">Degree</label><br>
            <select name="degree_id" id="degree_id" class="input-style" required>
                <option value="">-- Select Degree --</option>
                <?php
                $degrees = $pdo->query("SELECT degree_id, degree_name FROM degree")->fetchAll(PDO::FETCH_ASSOC);
                foreach ($degrees as $deg):
                ?>
                    <option value="<?= htmlspecialchars($deg['degree_id']) ?>">
                        <?= htmlspecialchars($deg['degree_name']) ?>
                    </option>
                <?php endforeach; ?>
            </select><br><br>

      <button type="submit" class="submit-btn">Add Course</button>
    </form>
  </div>
</div>


<script>
// ======= Common Click Handler =======
document.addEventListener('click', function(e) {

    // ===== Toggle Courses Dropdown =====
    if (e.target.classList.contains('toggle-courses-btn')) {
        const hdId = e.target.dataset.hdId;
        const dropdown = document.getElementById('courses-' + hdId);
        dropdown.style.display = dropdown.style.display === 'none' ? 'block' : 'none';
    }

    // ===== Delete Course =====
    if (e.target.classList.contains('delete-course-btn')) {
        const courseId = e.target.dataset.courseId;

        if (confirm('Are you sure you want to delete this course?')) {
            fetch('delete_course.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'dept_id=' + encodeURIComponent(courseId)
            })
            .then(res => res.json())
            .then(data => {
                if (data.status === 'success') {
                    toastr.success(data.message);
                    setTimeout(() => location.reload(), 1000);
                } else {
                    toastr.error(data.message);
                }
            })
            .catch(err => {
                console.error('Fetch error:', err);
                toastr.error('An unexpected error occurred while deleting the course.');
            });
        }
    }

    // ===== Show Add Course Modal =====
    if (e.target.classList.contains('add-course-btn')) {
        const hdId = e.target.dataset.hdId;
        document.getElementById('hd_id').value = hdId;
        document.getElementById('dept_name').value = '';
        document.getElementById('addCourseModal').style.display = 'block';
    }
});

// ===== Modal Controls =====
const modal = document.getElementById('addCourseModal');
const closeBtn = document.querySelector('.close-btn');
const addCourseForm = document.getElementById('addCourseForm');

// Close modal on X or outside click
closeBtn.addEventListener('click', () => modal.style.display = 'none');
window.addEventListener('click', (e) => {
    if (e.target === modal) modal.style.display = 'none';
});

// ===== Handle Add Course Form Submission =====
addCourseForm.addEventListener('submit', function(e) {
    e.preventDefault();

    const formData = new URLSearchParams(new FormData(addCourseForm));

    fetch('add_course.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: formData.toString()
    })
    .then(res => res.json())
    .then(data => {
        if (data.status === 'success') {
            toastr.success(data.message);
            modal.style.display = 'none';
            setTimeout(() => location.reload(), 1000);
        } else {
            toastr.error(data.message);
        }
    })
    .catch(err => {
        console.error('Fetch error:', err);
        toastr.error('An unexpected error occurred while adding the course.');
    });
});
</script>

</body>
<script src="../assets/js/addCommon.js"></script>
<!-- <script src="../assets/js/deleteCommon.js"></script> -->
</html>