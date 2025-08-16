<?php
session_start();
include 'include/config.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'student') {
    header("Location: login.php");
    exit;
}

$student_id = $_SESSION['user_id'];

// Check if student already has an approved enrollment
$approvedSection = '';
$sqlApproved = "SELECT section FROM enrollments WHERE student_id = ? AND status = 'approved' LIMIT 1";
$stmt = $conn->prepare($sqlApproved);
$stmt->bind_param('i', $student_id);
$stmt->execute();
$res = $stmt->get_result();
if ($res->num_rows > 0) {
    $row = $res->fetch_assoc();
    $approvedSection = $row['section'];
}

// Subjects data
$subjects = [
    ['code' => 'MATH101', 'name' => 'Math 101', 'units' => 3, 'section' => 'A1', 'teacher' => 'Mr. Smith', 'schedule' => 'Mon 8:00-10:00'],
    ['code' => 'PHYS201', 'name' => 'Physics 201', 'units' => 4, 'section' => 'A1', 'teacher' => 'Dr. Taylor', 'schedule' => 'Tue 9:00-11:00'],
    ['code' => 'ENG102', 'name' => 'English 102', 'units' => 2, 'section' => 'A1', 'teacher' => 'Ms. Johnson', 'schedule' => 'Wed 10:00-12:00'],
    ['code' => 'HIST202', 'name' => 'History 202', 'units' => 3, 'section' => 'A1', 'teacher' => 'Mr. Lee', 'schedule' => 'Thu 13:00-15:00'],
    ['code' => 'CS301', 'name' => 'Computer Sci 301', 'units' => 4, 'section' => 'A1', 'teacher' => 'Dr. Brown', 'schedule' => 'Fri 15:00-17:00'],

    ['code' => 'MATH101', 'name' => 'Math 101', 'units' => 3, 'section' => 'A2', 'teacher' => 'Mr. Smith', 'schedule' => 'Mon 8:00-10:00'],
    ['code' => 'PHYS201', 'name' => 'Physics 201', 'units' => 4, 'section' => 'A2', 'teacher' => 'Dr. Taylor', 'schedule' => 'Tue 9:00-11:00'],
    ['code' => 'ENG102', 'name' => 'English 102', 'units' => 2, 'section' => 'A2', 'teacher' => 'Ms. Johnson', 'schedule' => 'Wed 10:00-12:00'],
    ['code' => 'HIST202', 'name' => 'History 202', 'units' => 3, 'section' => 'A2', 'teacher' => 'Mr. Lee', 'schedule' => 'Thu 13:00-15:00'],
    ['code' => 'CS301', 'name' => 'Computer Sci 301', 'units' => 4, 'section' => 'A2', 'teacher' => 'Dr. Brown', 'schedule' => 'Fri 15:00-17:00'],

    ['code' => 'MATH101', 'name' => 'Math 101', 'units' => 3, 'section' => 'A3', 'teacher' => 'Mr. Smith', 'schedule' => 'Mon 8:00-10:00'],
    ['code' => 'PHYS201', 'name' => 'Physics 201', 'units' => 4, 'section' => 'A3', 'teacher' => 'Dr. Taylor', 'schedule' => 'Tue 9:00-11:00'],
    ['code' => 'ENG102', 'name' => 'English 102', 'units' => 2, 'section' => 'A3', 'teacher' => 'Ms. Johnson', 'schedule' => 'Wed 10:00-12:00'],
    ['code' => 'HIST202', 'name' => 'History 202', 'units' => 3, 'section' => 'A3', 'teacher' => 'Mr. Lee', 'schedule' => 'Thu 13:00-15:00'],
    ['code' => 'CS301', 'name' => 'Computer Sci 301', 'units' => 4, 'section' => 'A3', 'teacher' => 'Dr. Brown', 'schedule' => 'Fri 15:00-17:00'],

    // M sections
    ['code' => 'MATH101', 'name' => 'Math 101', 'units' => 3, 'section' => 'M1', 'teacher' => 'Mr. Smith', 'schedule' => 'Mon 8:00-10:00'],
    ['code' => 'PHYS201', 'name' => 'Physics 201', 'units' => 4, 'section' => 'M1', 'teacher' => 'Dr. Taylor', 'schedule' => 'Tue 9:00-11:00'],
    ['code' => 'ENG102', 'name' => 'English 102', 'units' => 2, 'section' => 'M1', 'teacher' => 'Ms. Johnson', 'schedule' => 'Wed 10:00-12:00'],
    ['code' => 'HIST202', 'name' => 'History 202', 'units' => 3, 'section' => 'M1', 'teacher' => 'Mr. Lee', 'schedule' => 'Thu 13:00-15:00'],
    ['code' => 'CS301', 'name' => 'Computer Sci 301', 'units' => 4, 'section' => 'M1', 'teacher' => 'Dr. Brown', 'schedule' => 'Fri 15:00-17:00'],

    ['code' => 'MATH101', 'name' => 'Math 101', 'units' => 3, 'section' => 'M2', 'teacher' => 'Mr. Smith', 'schedule' => 'Mon 8:00-10:00'],
    ['code' => 'PHYS201', 'name' => 'Physics 201', 'units' => 4, 'section' => 'M2', 'teacher' => 'Dr. Taylor', 'schedule' => 'Tue 9:00-11:00'],
    ['code' => 'ENG102', 'name' => 'English 102', 'units' => 2, 'section' => 'M2', 'teacher' => 'Ms. Johnson', 'schedule' => 'Wed 10:00-12:00'],
    ['code' => 'HIST202', 'name' => 'History 202', 'units' => 3, 'section' => 'M2', 'teacher' => 'Mr. Lee', 'schedule' => 'Thu 13:00-15:00'],
    ['code' => 'CS301', 'name' => 'Computer Sci 301', 'units' => 4, 'section' => 'M2', 'teacher' => 'Dr. Brown', 'schedule' => 'Fri 15:00-17:00'],

    ['code' => 'MATH101', 'name' => 'Math 101', 'units' => 3, 'section' => 'M3', 'teacher' => 'Mr. Smith', 'schedule' => 'Mon 8:00-10:00'],
    ['code' => 'PHYS201', 'name' => 'Physics 201', 'units' => 4, 'section' => 'M3', 'teacher' => 'Dr. Taylor', 'schedule' => 'Tue 9:00-11:00'],
    ['code' => 'ENG102', 'name' => 'English 102', 'units' => 2, 'section' => 'M3', 'teacher' => 'Ms. Johnson', 'schedule' => 'Wed 10:00-12:00'],
    ['code' => 'HIST202', 'name' => 'History 202', 'units' => 3, 'section' => 'M3', 'teacher' => 'Mr. Lee', 'schedule' => 'Thu 13:00-15:00'],
    ['code' => 'CS301', 'name' => 'Computer Sci 301', 'units' => 4, 'section' => 'M3', 'teacher' => 'Dr. Brown', 'schedule' => 'Fri 15:00-17:00'],

    // E sections
    ['code' => 'MATH101', 'name' => 'Math 101', 'units' => 3, 'section' => 'E1', 'teacher' => 'Mr. Smith', 'schedule' => 'Mon 8:00-10:00'],
    ['code' => 'PHYS201', 'name' => 'Physics 201', 'units' => 4, 'section' => 'E1', 'teacher' => 'Dr. Taylor', 'schedule' => 'Tue 9:00-11:00'],
    ['code' => 'ENG102', 'name' => 'English 102', 'units' => 2, 'section' => 'E1', 'teacher' => 'Ms. Johnson', 'schedule' => 'Wed 10:00-12:00'],
    ['code' => 'HIST202', 'name' => 'History 202', 'units' => 3, 'section' => 'E1', 'teacher' => 'Mr. Lee', 'schedule' => 'Thu 13:00-15:00'],
    ['code' => 'CS301', 'name' => 'Computer Sci 301', 'units' => 4, 'section' => 'E1', 'teacher' => 'Dr. Brown', 'schedule' => 'Fri 15:00-17:00'],

    ['code' => 'MATH101', 'name' => 'Math 101', 'units' => 3, 'section' => 'E2', 'teacher' => 'Mr. Smith', 'schedule' => 'Mon 8:00-10:00'],
    ['code' => 'PHYS201', 'name' => 'Physics 201', 'units' => 4, 'section' => 'E2', 'teacher' => 'Dr. Taylor', 'schedule' => 'Tue 9:00-11:00'],
    ['code' => 'ENG102', 'name' => 'English 102', 'units' => 2, 'section' => 'E2', 'teacher' => 'Ms. Johnson', 'schedule' => 'Wed 10:00-12:00'],
    ['code' => 'HIST202', 'name' => 'History 202', 'units' => 3, 'section' => 'E2', 'teacher' => 'Mr. Lee', 'schedule' => 'Thu 13:00-15:00'],
    ['code' => 'CS301', 'name' => 'Computer Sci 301', 'units' => 4, 'section' => 'E2', 'teacher' => 'Dr. Brown', 'schedule' => 'Fri 15:00-17:00'],

    ['code' => 'MATH101', 'name' => 'Math 101', 'units' => 3, 'section' => 'E3', 'teacher' => 'Mr. Smith', 'schedule' => 'Mon 8:00-10:00'],
    ['code' => 'PHYS201', 'name' => 'Physics 201', 'units' => 4, 'section' => 'E3', 'teacher' => 'Dr. Taylor', 'schedule' => 'Tue 9:00-11:00'],
    ['code' => 'ENG102', 'name' => 'English 102', 'units' => 2, 'section' => 'E3', 'teacher' => 'Ms. Johnson', 'schedule' => 'Wed 10:00-12:00'],
    ['code' => 'HIST202', 'name' => 'History 202', 'units' => 3, 'section' => 'E3', 'teacher' => 'Mr. Lee', 'schedule' => 'Thu 13:00-15:00'],
    ['code' => 'CS301', 'name' => 'Computer Sci 301', 'units' => 4, 'section' => 'E3', 'teacher' => 'Dr. Brown', 'schedule' => 'Fri 15:00-17:00'],
];

// Group subjects by section for easier display
$sections = [];
foreach ($subjects as $sub) {
    $sections[$sub['section']][] = $sub;
}

function convertTo12Hour($time24) {
    $dateObj = DateTime::createFromFormat('H:i', $time24);
    return $dateObj ? $dateObj->format('g:i A') : $time24;
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Subjects and Enrollment</title>
    <style>
        table { border-collapse: collapse; width: 100%; max-width: 700px; margin: 20px auto; }
        th, td { border: 1px solid #333; padding: 8px; text-align: left; }
        th { background: #555; color: white; }
        h2 { text-align: center; margin-top: 40px; }
        .enroll-btn { padding: 8px 12px; font-size: 1rem; cursor: pointer; }
        .enroll-btn:disabled { background: #ccc; cursor: not-allowed; }
        .message { text-align: center; margin: 20px; font-weight: bold; font-size: 1.2rem; }
        .enroll-container { text-align: center; margin: 10px 0 40px; }
    </style>
</head>
<body>

<h1 style="text-align:center;">Welcome, Student #<?= htmlspecialchars($student_id) ?></h1>

<?php if ($approvedSection): ?>
    <div class="message" style="color:green;">
        You are enrolled and approved in Section <?= htmlspecialchars($approvedSection) ?>.
    </div>

    <div class="container" aria-label="Section <?= htmlspecialchars($approvedSection) ?>">
        <h2>Subjects for Section <?= htmlspecialchars($approvedSection) ?></h2>
        <table>
            <thead>
                <tr>
                    <th>Code</th>
                    <th>Name</th>
                    <th>Units</th>
                    <th>Teacher</th>
                    <th>Schedule</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                foreach ($sections[$approvedSection] as $sub):
                    preg_match('/^(\w+)\s(\d{1,2}:\d{2})-(\d{1,2}:\d{2})$/', $sub['schedule'], $matches);
                    if ($matches) {
                        $day = $matches[1];
                        $start24 = $matches[2];
                        $end24 = $matches[3];
                        $start12 = convertTo12Hour($start24);
                        $end12 = convertTo12Hour($end24);
                        $schedule_display = "$day $start12 - $end12";
                    } else {
                        $schedule_display = htmlspecialchars($sub['schedule']);
                    }
                ?>
                    <tr>
                        <td><?= htmlspecialchars($sub['code']) ?></td>
                        <td><?= htmlspecialchars($sub['name']) ?></td>
                        <td><?= htmlspecialchars($sub['units']) ?></td>
                        <td><?= htmlspecialchars($sub['teacher']) ?></td>
                        <td><?= $schedule_display ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

<?php else: ?>

    <div class="message" style="color:blue;">
        Please select a section to enroll:
    </div>

    <?php foreach ($sections as $section => $subs): ?>
        <div class="container" aria-label="Section <?= htmlspecialchars($section) ?>">
            <h2>Section <?= htmlspecialchars($section) ?></h2>
            <table>
                <thead>
                    <tr>
                        <th>Code</th>
                        <th>Name</th>
                        <th>Units</th>
                        <th>Teacher</th>
                        <th>Schedule</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($subs as $sub): 
                        preg_match('/^(\w+)\s(\d{1,2}:\d{2})-(\d{1,2}:\d{2})$/', $sub['schedule'], $matches);
                        if ($matches) {
                            $day = $matches[1];
                            $start24 = $matches[2];
                            $end24 = $matches[3];
                            $start12 = convertTo12Hour($start24);
                            $end12 = convertTo12Hour($end24);
                            $schedule_display = "$day $start12 - $end12";
                        } else {
                            $schedule_display = htmlspecialchars($sub['schedule']);
                        }
                    ?>
                        <tr>
                            <td><?= htmlspecialchars($sub['code']) ?></td>
                            <td><?= htmlspecialchars($sub['name']) ?></td>
                            <td><?= htmlspecialchars($sub['units']) ?></td>
                            <td><?= htmlspecialchars($sub['teacher']) ?></td>
                            <td><?= $schedule_display ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <div class="enroll-container">
                <button 
                    class="enroll-btn" 
                    data-section="<?= htmlspecialchars($section) ?>"
                >
                    Enroll
                </button>
                <div class="pending-message" style="display:none; color: orange; font-weight:bold;">
                    Enrollment Pending for Section <?= htmlspecialchars($section) ?>
                </div>
            </div>
        </div>
    <?php endforeach; ?>

<?php endif; ?>

<script>
document.querySelectorAll('.enroll-btn').forEach(btn => {
    btn.addEventListener('click', () => {
        const section = btn.getAttribute('data-section');
        if (!section) return;

        btn.disabled = true;
        btn.textContent = 'Enrolling...';

        fetch('enroll.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: 'section=' + encodeURIComponent(section)
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                btn.style.display = 'none';
                btn.nextElementSibling.style.display = 'block'; // show pending message
            } else {
                alert('Error: ' + data.message);
                btn.disabled = false;
                btn.textContent = 'Enroll';
            }
        })
        .catch(() => {
            alert('Network error');
            btn.disabled = false;
            btn.textContent = 'Enroll';
        });
    });
});
</script>

</body>
</html>
