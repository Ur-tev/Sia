<?php
session_start();
include 'include/config.php'; // <- make sure this sets $conn (mysqli)

$message = '';
$error = '';
$error_fields = [];

// Helper to safely get POST values
function post($k) {
    return isset($_POST[$k]) ? trim($_POST[$k]) : '';
}

// List of fields to store (must match your DB columns)
$fields = [
    'desired_course',
    // personal
    'last_name','first_name','middle_name','suffix','complete_address','zip_code','region','province','city','barangay',
    'landline','mobile_no','gender','civil_status','nationality','date_of_birth','place_of_birth','email','religion',
    // educational
    'primary_school','primary_year_graduated','secondary_school','secondary_year_graduated',
    'tertiary_school','tertiary_year_graduated','course_graduated','educational_plan','academic_achievement',
    // work / NCST related
    'working','employer','work_in_shifts','work_position','family_connected_ncst','ncst_student','number_of_siblings','ncst_employee','relationship','how_know_ncst',
    // other info
    'transferee','als_graduate','returnee','dts_student','cross_enrollee','foreign_student',
    // father
    'father_family_name','father_given_name','father_middle_name','father_deceased','father_complete_address','father_landline','father_mobile_no','father_occupation',
    // mother
    'mother_family_name','mother_given_name','mother_middle_name','mother_deceased','mother_maiden_family_name','mother_maiden_given_name','mother_maiden_middle_name','mother_complete_address','mother_landline','mother_mobile_no','mother_occupation',
    // guardian
    'guardian_family_name','guardian_given_name','guardian_middle_name','guardian_complete_address','guardian_landline','guardian_mobile_no','guardian_occupation','guardian_relationship',
    // meta
    'status' // we'll insert 'pending' by default if empty
];

$required = [
    'desired_course',
    'last_name','first_name','complete_address','zip_code','region','province','city','barangay',
    'landline','mobile_no','gender','civil_status','nationality','date_of_birth','place_of_birth','email','religion',
    'primary_school','primary_year_graduated','secondary_school','secondary_year_graduated',
    'tertiary_school','tertiary_year_graduated','course_graduated','educational_plan','academic_achievement',
    'working','employer','work_in_shifts','work_position','family_connected_ncst','ncst_student','number_of_siblings','ncst_employee','relationship','how_know_ncst',
    // father
    'father_family_name','father_given_name','father_deceased','father_complete_address','father_landline','father_mobile_no','father_occupation',
    // mother
    'mother_family_name','mother_given_name','mother_deceased','mother_maiden_family_name','mother_maiden_given_name','mother_maiden_middle_name','mother_complete_address','mother_landline','mother_mobile_no','mother_occupation',
    // guardian
    'guardian_family_name','guardian_given_name','guardian_complete_address','guardian_landline','guardian_mobile_no','guardian_occupation','guardian_relationship'
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate required fields
    foreach ($required as $r) {
        if (empty($_POST[$r])) {
            $error_fields[] = $r;
        }
    }
    // Email format validation
    if (!empty($_POST['email']) && !filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
        $error = "Please enter a valid email address.";
        $error_fields[] = 'email';
    }

    if ($error_fields && !$error) {
        $error = "Please fill in all required fields.";
    }

    // Numeric checks
    if (!$error) {
        $mobile = post('mobile_no');
        $landline = post('landline');
        $zip = post('zip_code');

        if (!preg_match('/^\d{11}$/', $mobile)) {
            $error = "Mobile number must be exactly 11 digits.";
            $error_fields[] = 'mobile_no';
        }
        if (!preg_match('/^\d{8}$/', post('landline'))) {
            $error = "Landline must be exactly 8 digits.";
            $error_fields[] = 'landline';
        }
        if (!preg_match('/^\d{4}$/', post('zip_code'))) {
            $error = "Zip code must be 4 digits.";
            $error_fields[] = 'zip_code';
        }
    }

    if (!$error) {
        // build values array from $fields
        $values = [];
        foreach ($fields as $f) {
            // default 'pending' for status if empty
            if ($f === 'status') {
                $values[] = post($f) !== '' ? post($f) : 'pending';
            } else {
                $values[] = post($f);
            }
        }
        // add submitted_at (NOW()) at the end — not a bound param
        // build placeholders and types
        $placeholders = implode(',', array_fill(0, count($values), '?'));
        $types = str_repeat('s', count($values)); // treat all as strings for simplicity

        $sql = "INSERT INTO admission_applications (" . implode(',', $fields) . ", submitted_at) VALUES ($placeholders, NOW())";

        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            $error = "Prepare failed: " . $conn->error;
        } else {
            // mysqli bind_param requires references
            $bind_names[] = $types;
            for ($i = 0; $i < count($values); $i++) {
                $bind_name = 'bind' . $i;
                $$bind_name = $values[$i];
                $bind_names[] = &$$bind_name;
            }
            // call bind_param dynamically
            call_user_func_array([$stmt, 'bind_param'], $bind_names);

            if ($stmt->execute()) {
                // Send email to registrar
                $to = "registrar@yourdomain.com"; // Change to your registrar's email
                $subject = "New College Registration Application";
                $body = "A new application has been submitted.\n\n";
                foreach ($fields as $f) {
                    $body .= ucfirst(str_replace('_', ' ', $f)) . ": " . post($f) . "\n";
                }
                $headers = "From: noreply@yourdomain.com\r\n";
                @mail($to, $subject, $body, $headers);

                // Prevent resubmission on reload
                header("Location: " . $_SERVER['PHP_SELF'] . "?success=1");
                exit;
            } else {
                $error = "Execute failed: " . $stmt->error;
            }
            $stmt->close();
        }
    }
}

// Show success alert if redirected after submit
if (isset($_GET['success']) && $_GET['success'] == 1) {
    $message = "Application submitted successfully!";
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>College Registration</title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body { font-family: Arial, sans-serif; background:#f4f6f8; margin:0; padding:20px; }
        .container { background:white; max-width:1100px; margin:auto; padding:25px; border-radius:8px; box-shadow:0 6px 18px rgba(0,0,0,0.08); }
        h1 { margin-top:0; text-align:center; color:#333; }
        .section-title { background:#007BFF; color:white; padding:8px 12px; border-radius:6px; display:inline-block; margin-top:12px; }
        .form-grid { display:grid; grid-template-columns:repeat(2,1fr); gap:12px; margin-top:10px; }
        .form-row { display:flex; gap:12px; }
        .col { display:flex; flex-direction:column; }
        label { font-weight:700; margin-bottom:6px; }
        input[type="text"], input[type="email"], input[type="date"], select, textarea, input[type="number"] {
            padding:8px; border-radius:6px; border:1px solid #cfcfcf; width:100%; box-sizing:border-box;
        }
        textarea { min-height:80px; resize:vertical; }
        .full { grid-column:1/-1; }
        .actions { text-align:right; margin-top:18px; }
        button { background:#007BFF; color:white; border:none; padding:12px 20px; border-radius:6px; cursor:pointer; font-size:16px; }
        button:hover{ background:#005ec2; }
        .small { font-size:13px; color:#666; margin-top:4px; }
        .error-field { border:2px solid #e74c3c !important; background:#fff6f6; }
        @media (max-width:900px) {
            .form-grid { grid-template-columns:1fr; }
        }
    </style>
</head>
<body>
<div class="container">
    <h1>College Registration</h1>

    <form id="regForm" method="POST" novalidate autocomplete="off">
         <div class="section-title">Course Information</div>
        <div class="form-grid">
            <div class="col full">
                <label>Desired Course *</label>
                <select name="desired_course" required class="<?php echo in_array('desired_course', $error_fields) ? 'error-field' : ''; ?>">
                    <option value="">-- Select --</option>
                    <option value="BSIT" <?php if(post('desired_course')=='BSIT') echo 'selected'; ?>>BS in Information Technology</option>
                    <option value="BSHM" <?php if(post('desired_course')=='BSHM') echo 'selected'; ?>>BS in Hospitality Management</option>
                </select>
            </div>
        </div>
        <!-- Personal Information -->
        <div class="section-title">Personal Information</div>
        <div class="form-grid">
            <div class="col">
                <label>Last Name *</label>
                <input type="text" name="last_name" required value="<?php echo htmlspecialchars(post('last_name')); ?>" class="<?php echo in_array('last_name', $error_fields) ? 'error-field' : ''; ?>">
            </div>
            <div class="col">
                <label>First Name *</label>
                <input type="text" name="first_name" required value="<?php echo htmlspecialchars(post('first_name')); ?>" class="<?php echo in_array('first_name', $error_fields) ? 'error-field' : ''; ?>">
            </div>
            <div class="col">
                <label>Middle Name *</label>
                <input type="text" name="middle_name" required value="<?php echo htmlspecialchars(post('middle_name')); ?>" class="<?php echo in_array('middle_name', $error_fields) ? 'error-field' : ''; ?>">
            </div>
            <div class="col">
                <label>Suffix *</label>
                <input type="text" name="suffix" required value="<?php echo htmlspecialchars(post('suffix')); ?>" class="<?php echo in_array('suffix', $error_fields) ? 'error-field' : ''; ?>">
            </div>
            <div class="col full">
                <label>Complete Address *</label>
                <textarea name="complete_address" required class="<?php echo in_array('complete_address', $error_fields) ? 'error-field' : ''; ?>"><?php echo htmlspecialchars(post('complete_address')); ?></textarea>
            </div>
            <div class="col">
                <label>Zip Code *</label>
                <input type="text" name="zip_code" id="zip_code" maxlength="4" pattern="\d{4}" required oninput="this.value=this.value.replace(/[^0-9]/g,'');" value="<?php echo htmlspecialchars(post('zip_code')); ?>" class="<?php echo in_array('zip_code', $error_fields) ? 'error-field' : ''; ?>">
                <div class="small">4 digits only</div>
            </div>
            <div class="col">
                <label>Region *</label>
                <select name="region" id="regionSelect" required class="<?php echo in_array('region', $error_fields) ? 'error-field' : ''; ?>">
                    <option value="Region IV-A" <?php if(post('region')=='Region IV-A' || post('region')=='') echo 'selected'; ?>>Region IV-A (Calabarzon)</option>
                </select>
            </div>
            <div class="col">
                <label>Province *</label>
                <select name="province" id="provinceSelect" required class="<?php echo in_array('province', $error_fields) ? 'error-field' : ''; ?>"><option value="">-- Select --</option></select>
            </div>
            <div class="col">
                <label>Town/Municipality/City *</label>
                <select name="city" id="citySelect" required class="<?php echo in_array('city', $error_fields) ? 'error-field' : ''; ?>"><option value="">-- Select --</option></select>
            </div>
            <div class="col">
                <label>Barangay *</label>
                <select name="barangay" id="barangaySelect" required class="<?php echo in_array('barangay', $error_fields) ? 'error-field' : ''; ?>"><option value="">-- Select --</option></select>
            </div>
            <div class="col">
                <label>Land Line *</label>
                <input type="text" name="landline" id="landline" maxlength="8" required oninput="this.value=this.value.replace(/[^0-9]/g,'');" value="<?php echo htmlspecialchars(post('landline')); ?>" class="<?php echo in_array('landline', $error_fields) ? 'error-field' : ''; ?>">
                <div class="small">8 digits (numbers only)</div>
            </div>
            <div class="col">
                <label>Mobile No *</label>
                <input type="text" name="mobile_no" id="mobile_no" maxlength="11" required oninput="this.value=this.value.replace(/[^0-9]/g,'');" value="<?php echo htmlspecialchars(post('mobile_no')); ?>" class="<?php echo in_array('mobile_no', $error_fields) ? 'error-field' : ''; ?>">
                <div class="small">11 digits (numbers only)</div>
            </div>
            <div class="col">
                <label>Gender *</label>
                <select name="gender" required class="<?php echo in_array('gender', $error_fields) ? 'error-field' : ''; ?>">
                    <option value="">-- Select --</option>
                    <option <?php if(post('gender')=='Male') echo 'selected'; ?>>Male</option>
                    <option <?php if(post('gender')=='Female') echo 'selected'; ?>>Female</option>
                    <option <?php if(post('gender')=='Prefer not to say') echo 'selected'; ?>>Prefer not to say</option>
                </select>
            </div>
            <div class="col">
                <label>Civil Status *</label>
                <select name="civil_status" required class="<?php echo in_array('civil_status', $error_fields) ? 'error-field' : ''; ?>">
                    <option value="">-- Select --</option>
                    <option <?php if(post('civil_status')=='Single') echo 'selected'; ?>>Single</option>
                    <option <?php if(post('civil_status')=='Married') echo 'selected'; ?>>Married</option>
                    <option <?php if(post('civil_status')=='Widowed') echo 'selected'; ?>>Widowed</option>
                    <option <?php if(post('civil_status')=='Separated') echo 'selected'; ?>>Separated</option>
                </select>
            </div>
            <div class="col">
                <label>Nationality *</label>
                <select name="nationality" required class="<?php echo in_array('nationality', $error_fields) ? 'error-field' : ''; ?>">
                    <option value="">-- Select --</option>
                    <option <?php if(post('nationality')=='Filipino') echo 'selected'; ?>>Filipino</option>
                    <option <?php if(post('nationality')=='Other') echo 'selected'; ?>>Other</option>
                </select>
            </div>
            <div class="col">
                <label>Date Of Birth *</label>
                <input type="date" name="date_of_birth" required value="<?php echo htmlspecialchars(post('date_of_birth')); ?>" class="<?php echo in_array('date_of_birth', $error_fields) ? 'error-field' : ''; ?>">
            </div>
            <div class="col">
                <label>Place of Birth *</label>
                <input type="text" name="place_of_birth" required value="<?php echo htmlspecialchars(post('place_of_birth')); ?>" class="<?php echo in_array('place_of_birth', $error_fields) ? 'error-field' : ''; ?>">
            </div>
            <div class="col">
                <label>Email Address *</label>
                <input type="email" name="email" required value="<?php echo htmlspecialchars(post('email')); ?>" class="<?php echo in_array('email', $error_fields) ? 'error-field' : ''; ?>">
            </div>
            <div class="col">
                <label>Religion *</label>
                <select name="religion" required class="<?php echo in_array('religion', $error_fields) ? 'error-field' : ''; ?>">
                    <option value="">-- Select --</option>
                    <option <?php if(post('religion')=='Catholic') echo 'selected'; ?>>Catholic</option>
                    <option <?php if(post('religion')=='Christian') echo 'selected'; ?>>Christian</option>
                    <option <?php if(post('religion')=='Muslim') echo 'selected'; ?>>Muslim</option>
                    <option <?php if(post('religion')=='Other') echo 'selected'; ?>>Other</option>
                </select>
            </div>
        </div>
        <!-- Educational Information -->
        <div class="section-title">Educational Information</div>
        <div class="form-grid">
            <div class="col">
                <label>Primary School *</label>
                <input type="text" name="primary_school" required value="<?php echo htmlspecialchars(post('primary_school')); ?>" class="<?php echo in_array('primary_school', $error_fields) ? 'error-field' : ''; ?>">
            </div>
            <div class="col">
                <label>Year Graduated *</label>
                <input type="text" name="primary_year_graduated" maxlength="4" required oninput="this.value=this.value.replace(/[^0-9]/g,'');" value="<?php echo htmlspecialchars(post('primary_year_graduated')); ?>" class="<?php echo in_array('primary_year_graduated', $error_fields) ? 'error-field' : ''; ?>">
            </div>
            <div class="col">
                <label>Secondary School *</label>
                <input type="text" name="secondary_school" required value="<?php echo htmlspecialchars(post('secondary_school')); ?>" class="<?php echo in_array('secondary_school', $error_fields) ? 'error-field' : ''; ?>">
            </div>
            <div class="col">
                <label>Year Graduated *</label>
                <input type="text" name="secondary_year_graduated" maxlength="4" required oninput="this.value=this.value.replace(/[^0-9]/g,'');" value="<?php echo htmlspecialchars(post('secondary_year_graduated')); ?>" class="<?php echo in_array('secondary_year_graduated', $error_fields) ? 'error-field' : ''; ?>">
            </div>
            <div class="col">
                <label>Tertiary School *</label>
                <input type="text" name="tertiary_school" required value="<?php echo htmlspecialchars(post('tertiary_school')); ?>" class="<?php echo in_array('tertiary_school', $error_fields) ? 'error-field' : ''; ?>">
            </div>
            <div class="col">
                <label>Year Graduated *</label>
                <input type="text" name="tertiary_year_graduated" maxlength="4" required oninput="this.value=this.value.replace(/[^0-9]/g,'');" value="<?php echo htmlspecialchars(post('tertiary_year_graduated')); ?>" class="<?php echo in_array('tertiary_year_graduated', $error_fields) ? 'error-field' : ''; ?>">
            </div>
            <div class="col">
                <label>Course Graduated *</label>
                <input type="text" name="course_graduated" required value="<?php echo htmlspecialchars(post('course_graduated')); ?>" class="<?php echo in_array('course_graduated', $error_fields) ? 'error-field' : ''; ?>">
            </div>
            <div class="col">
                <label>Educational Plan *</label>
                <select name="educational_plan" required class="<?php echo in_array('educational_plan', $error_fields) ? 'error-field' : ''; ?>">
                    <option value="">-- Select --</option>
                    <option <?php if(post('educational_plan')=='Full Scholar') echo 'selected'; ?>>Full Scholar</option>
                    <option <?php if(post('educational_plan')=='Partial Scholar') echo 'selected'; ?>>Partial Scholar</option>
                    <option <?php if(post('educational_plan')=='Self-supporting') echo 'selected'; ?>>Self-supporting</option>
                </select>
            </div>
            <div class="col full">
                <label>Academic Achievement *</label>
                <select name="academic_achievement" required class="<?php echo in_array('academic_achievement', $error_fields) ? 'error-field' : ''; ?>">
                    <option value="">-- Select --</option>
                    <option <?php if(post('academic_achievement')=="Honor Student") echo 'selected'; ?>>Honor Student</option>
                    <option <?php if(post('academic_achievement')=="Dean's Lister") echo 'selected'; ?>>Dean's Lister</option>
                    <option <?php if(post('academic_achievement')=="None") echo 'selected'; ?>>None</option>
                </select>
            </div>
        </div>
        <!-- Work Information -->
        <div class="section-title">Work Information</div>
        <div class="form-grid">
            <div class="col">
                <label>Working? *</label>
                <select name="working" required class="<?php echo in_array('working', $error_fields) ? 'error-field' : ''; ?>">
                    <option value="">-- Select --</option>
                    <option <?php if(post('working')=='Yes') echo 'selected'; ?>>Yes</option>
                    <option <?php if(post('working')=='No') echo 'selected'; ?>>No</option>
                </select>
            </div>
            <div class="col">
                <label>Employer *</label>
                <input type="text" name="employer" required value="<?php echo htmlspecialchars(post('employer')); ?>" class="<?php echo in_array('employer', $error_fields) ? 'error-field' : ''; ?>">
            </div>
            <div class="col">
                <label>Work in Shifts? *</label>
                <select name="work_in_shifts" required class="<?php echo in_array('work_in_shifts', $error_fields) ? 'error-field' : ''; ?>">
                    <option value="">-- Select --</option>
                    <option <?php if(post('work_in_shifts')=='Yes') echo 'selected'; ?>>Yes</option>
                    <option <?php if(post('work_in_shifts')=='No') echo 'selected'; ?>>No</option>
                </select>
            </div>
            <div class="col">
                <label>Work Position *</label>
                <input type="text" name="work_position" required value="<?php echo htmlspecialchars(post('work_position')); ?>" class="<?php echo in_array('work_position', $error_fields) ? 'error-field' : ''; ?>">
            </div>
            <div class="col">
                <label>Family Connected to NCST *</label>
                <input type="text" name="family_connected_ncst" required value="<?php echo htmlspecialchars(post('family_connected_ncst')); ?>" class="<?php echo in_array('family_connected_ncst', $error_fields) ? 'error-field' : ''; ?>">
            </div>
            <div class="col">
                <label>NCST Student *</label>
                <input type="text" name="ncst_student" required value="<?php echo htmlspecialchars(post('ncst_student')); ?>" class="<?php echo in_array('ncst_student', $error_fields) ? 'error-field' : ''; ?>">
            </div>
            <div class="col">
                <label>No of Siblings *</label>
                <input type="number" name="number_of_siblings" required value="<?php echo htmlspecialchars(post('number_of_siblings') !== '' ? post('number_of_siblings') : '0'); ?>" min="0" class="<?php echo in_array('number_of_siblings', $error_fields) ? 'error-field' : ''; ?>">
            </div>
            <div class="col">
                <label>NCST Employee *</label>
                <input type="text" name="ncst_employee" required value="<?php echo htmlspecialchars(post('ncst_employee')); ?>" class="<?php echo in_array('ncst_employee', $error_fields) ? 'error-field' : ''; ?>">
            </div>
            <div class="col">
                <label>Relationship *</label>
                <select name="relationship" required class="<?php echo in_array('relationship', $error_fields) ? 'error-field' : ''; ?>">
                    <option value="">-- Select --</option>
                    <option <?php if(post('relationship')=='Parent') echo 'selected'; ?>>Parent</option>
                    <option <?php if(post('relationship')=='Sibling') echo 'selected'; ?>>Sibling</option>
                    <option <?php if(post('relationship')=='Relative') echo 'selected'; ?>>Relative</option>
                </select>
            </div>
            <div class="col full">
                <label>How did Student come to know about NCST? *</label>
                <select name="how_know_ncst" required class="<?php echo in_array('how_know_ncst', $error_fields) ? 'error-field' : ''; ?>">
                    <option value="">-- Select --</option>
                    <option <?php if(post('how_know_ncst')=='Friends') echo 'selected'; ?>>Friends</option>
                    <option <?php if(post('how_know_ncst')=='Social Media') echo 'selected'; ?>>Social Media</option>
                    <option <?php if(post('how_know_ncst')=='Events') echo 'selected'; ?>>Events</option>
                    <option <?php if(post('how_know_ncst')=='Other') echo 'selected'; ?>>Other</option>
                </select>
            </div>
        </div>
        <!-- Other Information (NOT required) -->
        <div class="section-title">Other Information</div>
        <div class="form-grid">
            <div class="col">
                <label>Transferee?</label>
                <select name="transferee">
                    <option value="">-- Select --</option>
                    <option <?php if(post('transferee')=='Yes') echo 'selected'; ?>>Yes</option>
                    <option <?php if(post('transferee')=='No') echo 'selected'; ?>>No</option>
                </select>
            </div>
            <div class="col">
                <label>ALS Graduate?</label>
                <select name="als_graduate">
                    <option value="">-- Select --</option>
                    <option <?php if(post('als_graduate')=='Yes') echo 'selected'; ?>>Yes</option>
                    <option <?php if(post('als_graduate')=='No') echo 'selected'; ?>>No</option>
                </select>
            </div>
            <div class="col">
                <label>Returnee?</label>
                <select name="returnee">
                    <option value="">-- Select --</option>
                    <option <?php if(post('returnee')=='Yes') echo 'selected'; ?>>Yes</option>
                    <option <?php if(post('returnee')=='No') echo 'selected'; ?>>No</option>
                </select>
            </div>
            <div class="col">
                <label>DTS Student?</label>
                <select name="dts_student">
                    <option value="">-- Select --</option>
                    <option <?php if(post('dts_student')=='Yes') echo 'selected'; ?>>Yes</option>
                    <option <?php if(post('dts_student')=='No') echo 'selected'; ?>>No</option>
                </select>
            </div>
            <div class="col">
                <label>Cross Enrollee?</label>
                <select name="cross_enrollee">
                    <option value="">-- Select --</option>
                    <option <?php if(post('cross_enrollee')=='Yes') echo 'selected'; ?>>Yes</option>
                    <option <?php if(post('cross_enrollee')=='No') echo 'selected'; ?>>No</option>
                </select>
            </div>
            <div class="col">
                <label>Foreign Student?</label>
                <select name="foreign_student">
                    <option value="">-- Select --</option>
                    <option <?php if(post('foreign_student')=='Yes') echo 'selected'; ?>>Yes</option>
                    <option <?php if(post('foreign_student')=='No') echo 'selected'; ?>>No</option>
                </select>
            </div>
        </div>
        <!-- Parent/Guardian Information -->
        <div class="section-title">Parent / Guardian Information</div>
        <div class="form-grid">
            <div class="col full"><strong>Father Information</strong></div>
            <div class="col">
                <label>Family Name *</label><input type="text" name="father_family_name" required value="<?php echo htmlspecialchars(post('father_family_name')); ?>" class="<?php echo in_array('father_family_name', $error_fields) ? 'error-field' : ''; ?>">
            </div>
            <div class="col">
                <label>Given Name *</label><input type="text" name="father_given_name" required value="<?php echo htmlspecialchars(post('father_given_name')); ?>" class="<?php echo in_array('father_given_name', $error_fields) ? 'error-field' : ''; ?>">
            </div>
            <div class="col">
                <label>Middle Name</label><input type="text" name="father_middle_name" value="<?php echo htmlspecialchars(post('father_middle_name')); ?>">
            </div>
            <div class="col">
                <label>Deceased? *</label>
                <select name="father_deceased" required class="<?php echo in_array('father_deceased', $error_fields) ? 'error-field' : ''; ?>">
                    <option value="">-- Select --</option>
                    <option <?php if(post('father_deceased')=='Yes') echo 'selected'; ?>>Yes</option>
                    <option <?php if(post('father_deceased')=='No') echo 'selected'; ?>>No</option>
                </select>
            </div>
            <div class="col full">
                <label>Father's Complete Address *</label><textarea name="father_complete_address" required class="<?php echo in_array('father_complete_address', $error_fields) ? 'error-field' : ''; ?>"><?php echo htmlspecialchars(post('father_complete_address')); ?></textarea>
            </div>
            <div class="col">
                <label>Father's Land Line *</label><input type="text" name="father_landline" maxlength="8" required oninput="this.value=this.value.replace(/[^0-9]/g,'');" value="<?php echo htmlspecialchars(post('father_landline')); ?>" class="<?php echo in_array('father_landline', $error_fields) ? 'error-field' : ''; ?>">
            </div>
            <div class="col">
                <label>Father's Mobile No *</label><input type="text" name="father_mobile_no" maxlength="11" required oninput="this.value=this.value.replace(/[^0-9]/g,'');" value="<?php echo htmlspecialchars(post('father_mobile_no')); ?>" class="<?php echo in_array('father_mobile_no', $error_fields) ? 'error-field' : ''; ?>">
            </div>
            <div class="col">
                <label>Father's Occupation *</label><input type="text" name="father_occupation" required value="<?php echo htmlspecialchars(post('father_occupation')); ?>" class="<?php echo in_array('father_occupation', $error_fields) ? 'error-field' : ''; ?>">
            </div>
            <div class="col full"><strong>Mother Information</strong></div>
            <div class="col"><label>Family Name *</label><input type="text" name="mother_family_name" required value="<?php echo htmlspecialchars(post('mother_family_name')); ?>" class="<?php echo in_array('mother_family_name', $error_fields) ? 'error-field' : ''; ?>"></div>
            <div class="col"><label>Given Name *</label><input type="text" name="mother_given_name" required value="<?php echo htmlspecialchars(post('mother_given_name')); ?>" class="<?php echo in_array('mother_given_name', $error_fields) ? 'error-field' : ''; ?>"></div>
            <div class="col"><label>Middle Name</label><input type="text" name="mother_middle_name" value="<?php echo htmlspecialchars(post('mother_middle_name')); ?>"></div>
            <div class="col">
                <label>Deceased? *</label>
                <select name="mother_deceased" required class="<?php echo in_array('mother_deceased', $error_fields) ? 'error-field' : ''; ?>">
                    <option value="">-- Select --</option>
                    <option <?php if(post('mother_deceased')=='Yes') echo 'selected'; ?>>Yes</option>
                    <option <?php if(post('mother_deceased')=='No') echo 'selected'; ?>>No</option>
                </select>
            </div>
            <div class="col">
                <label>Mother Maiden Family Name *</label><input type="text" name="mother_maiden_family_name" required value="<?php echo htmlspecialchars(post('mother_maiden_family_name')); ?>" class="<?php echo in_array('mother_maiden_family_name', $error_fields) ? 'error-field' : ''; ?>">
            </div>
            <div class="col">
                <label>Mother Maiden Given Name *</label><input type="text" name="mother_maiden_given_name" required value="<?php echo htmlspecialchars(post('mother_maiden_given_name')); ?>" class="<?php echo in_array('mother_maiden_given_name', $error_fields) ? 'error-field' : ''; ?>">
            </div>
            <div class="col">
                <label>Mother Maiden Middle Name *</label><input type="text" name="mother_maiden_middle_name" required value="<?php echo htmlspecialchars(post('mother_maiden_middle_name')); ?>" class="<?php echo in_array('mother_maiden_middle_name', $error_fields) ? 'error-field' : ''; ?>">
            </div>
            <div class="col full">
                <label>Mother's Complete Address *</label><textarea name="mother_complete_address" required class="<?php echo in_array('mother_complete_address', $error_fields) ? 'error-field' : ''; ?>"><?php echo htmlspecialchars(post('mother_complete_address')); ?></textarea>
            </div>
            <div class="col">
                <label>Mother's Land Line *</label><input type="text" name="mother_landline" maxlength="8" required oninput="this.value=this.value.replace(/[^0-9]/g,'');" value="<?php echo htmlspecialchars(post('mother_landline')); ?>" class="<?php echo in_array('mother_landline', $error_fields) ? 'error-field' : ''; ?>">
            </div>
            <div class="col">
                <label>Mother's Mobile No *</label><input type="text" name="mother_mobile_no" maxlength="11" required oninput="this.value=this.value.replace(/[^0-9]/g,'');" value="<?php echo htmlspecialchars(post('mother_mobile_no')); ?>" class="<?php echo in_array('mother_mobile_no', $error_fields) ? 'error-field' : ''; ?>">
            </div>
            <div class="col">
                <label>Mother's Occupation *</label><input type="text" name="mother_occupation" required value="<?php echo htmlspecialchars(post('mother_occupation')); ?>" class="<?php echo in_array('mother_occupation', $error_fields) ? 'error-field' : ''; ?>">
            </div>
            <div class="col full"><strong>Guardian Information</strong></div>
            <div class="col"><label>Family Name *</label><input type="text" name="guardian_family_name" required value="<?php echo htmlspecialchars(post('guardian_family_name')); ?>" class="<?php echo in_array('guardian_family_name', $error_fields) ? 'error-field' : ''; ?>"></div>
            <div class="col"><label>Given Name *</label><input type="text" name="guardian_given_name" required value="<?php echo htmlspecialchars(post('guardian_given_name')); ?>" class="<?php echo in_array('guardian_given_name', $error_fields) ? 'error-field' : ''; ?>"></div>
            <div class="col"><label>Middle Name</label><input type="text" name="guardian_middle_name" value="<?php echo htmlspecialchars(post('guardian_middle_name')); ?>"></div>
            <div class="col full">
                <label>Guardian Complete Address *</label><textarea name="guardian_complete_address" required class="<?php echo in_array('guardian_complete_address', $error_fields) ? 'error-field' : ''; ?>"><?php echo htmlspecialchars(post('guardian_complete_address')); ?></textarea>
            </div>
            <div class="col">
                <label>Guardian Land Line *</label><input type="text" name="guardian_landline" maxlength="8" required oninput="this.value=this.value.replace(/[^0-9]/g,'');" value="<?php echo htmlspecialchars(post('guardian_landline')); ?>" class="<?php echo in_array('guardian_landline', $error_fields) ? 'error-field' : ''; ?>">
            </div>
            <div class="col">
                <label>Guardian Mobile No *</label><input type="text" name="guardian_mobile_no" maxlength="11" required oninput="this.value=this.value.replace(/[^0-9]/g,'');" value="<?php echo htmlspecialchars(post('guardian_mobile_no')); ?>" class="<?php echo in_array('guardian_mobile_no', $error_fields) ? 'error-field' : ''; ?>">
            </div>
            <div class="col">
                <label>Guardian Occupation *</label><input type="text" name="guardian_occupation" required value="<?php echo htmlspecialchars(post('guardian_occupation')); ?>" class="<?php echo in_array('guardian_occupation', $error_fields) ? 'error-field' : ''; ?>">
            </div>
            <div class="col">
                <label>Guardian Relationship *</label><input type="text" name="guardian_relationship" required value="<?php echo htmlspecialchars(post('guardian_relationship')); ?>" class="<?php echo in_array('guardian_relationship', $error_fields) ? 'error-field' : ''; ?>">
            </div>
        </div>
        <div class="actions">
            <button type="submit">Submit Application</button>
        </div>
    </form>
</div>
<script>
const addressData = {
    "Region IV-A": {
        "Cavite": {
            "Imus": ["Anabu 1", "Anabu 2", "Bucandala"],
            "Bacoor": ["San Nicolas I", "San Nicolas II", "Salinas I"]
        },
        "Laguna": {
            "Calamba": ["Canlubang", "Parian", "Lawa"],
            "Santa Rosa": ["Aplaya", "Bagong Kalsada", "Balibago"]
        },
        "Batangas": {
            "Batangas City": ["Poblacion", "Alangilan", "Kapunitan"],
            "Lipa": ["Poblacion East", "Poblacion West"]
        },
        "Rizal": {
            "Antipolo": ["San Roque", "San Jose", "Bagong Nayon"],
            "Tanay": ["Poblacion", "Sala"]
        },
        "Quezon": {
            "Lucena": ["Gulang-Gulang", "Bakunan"],
            "Tayabas": ["Poblacion", "Kinalinan"]
        }
    }
};
const regionSelect = document.getElementById('regionSelect');
const provinceSelect = document.getElementById('provinceSelect');
const citySelect = document.getElementById('citySelect');
const barangaySelect = document.getElementById('barangaySelect');
function populateProvinces() {
    const region = regionSelect.value;
    provinceSelect.innerHTML = '<option value="">-- Select --</option>';
    citySelect.innerHTML = '<option value="">-- Select --</option>';
    barangaySelect.innerHTML = '<option value="">-- Select --</option>';
    if (!addressData[region]) return;
    Object.keys(addressData[region]).forEach(p => {
        const opt = document.createElement('option');
        opt.value = p; opt.textContent = p;
        provinceSelect.appendChild(opt);
    });
}
function populateCities() {
    const region = regionSelect.value;
    const prov = provinceSelect.value;
    citySelect.innerHTML = '<option value="">-- Select --</option>';
    barangaySelect.innerHTML = '<option value="">-- Select --</option>';
    if (!region || !prov || !addressData[region][prov]) return;
    Object.keys(addressData[region][prov]).forEach(c => {
        const opt = document.createElement('option');
        opt.value = c; opt.textContent = c;
        citySelect.appendChild(opt);
    });
}
function populateBarangays() {
    const region = regionSelect.value;
    const prov = provinceSelect.value;
    const city = citySelect.value;
    barangaySelect.innerHTML = '<option value="">-- Select --</option>';
    if (!region || !prov || !city || !addressData[region][prov][city]) return;
    addressData[region][prov][city].forEach(b => {
        const opt = document.createElement('option');
        opt.value = b; opt.textContent = b;
        barangaySelect.appendChild(opt);
    });
}
provinceSelect.addEventListener('change', populateCities);
citySelect.addEventListener('change', populateBarangays);
populateProvinces();
<?php if ($_SERVER['REQUEST_METHOD'] === 'POST'): ?>
    document.getElementById('provinceSelect').value = <?php echo json_encode(post('province')); ?>;
    populateCities();
    document.getElementById('citySelect').value = <?php echo json_encode(post('city')); ?>;
    populateBarangays();
    document.getElementById('barangaySelect').value = <?php echo json_encode(post('barangay')); ?>;
<?php endif; ?>

// Only show success alert if redirected after submit (GET param)
<?php if ($message): ?>
Swal.fire({
    icon:'success',
    title:'Success',
    text: <?php echo json_encode($message); ?>
});
<?php elseif ($error): ?>
Swal.fire({ icon:'error', title:'Error', text: <?php echo json_encode($error); ?> });
<?php endif; ?>

// Highlight missing fields on submit (client-side)
document.getElementById('regForm').addEventListener('submit', function(e){
    let missing = [];
    // Only check visible required fields
    this.querySelectorAll('[required]').forEach(function(input){
        if (!input.value.trim()) {
            input.classList.add('error-field');
            missing.push(input);
        } else {
            input.classList.remove('error-field');
        }
    });
    // Numeric checks
    let zip = document.getElementById('zip_code').value.trim();
    let mobile = document.getElementById('mobile_no').value.trim();
    let landline = document.getElementById('landline').value.trim();
    let email = document.querySelector('input[name="email"]').value.trim();
    let emailValid = /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
    if (!/^\d{4}$/.test(zip)) {
        document.getElementById('zip_code').classList.add('error-field');
        missing.push(document.getElementById('zip_code'));
    }
    if (!/^\d{11}$/.test(mobile)) {
        document.getElementById('mobile_no').classList.add('error-field');
        missing.push(document.getElementById('mobile_no'));
    }
    if (!/^\d{8}$/.test(landline)) {
        document.getElementById('landline').classList.add('error-field');
        missing.push(document.getElementById('landline'));
    }
    if (!emailValid) {
        document.querySelector('input[name="email"]').classList.add('error-field');
        missing.push(document.querySelector('input[name="email"]'));
    } else {
        document.querySelector('input[name="email"]').classList.remove('error-field');
    }
    if (missing.length > 0) {
        e.preventDefault();
        if (missing[0]) missing[0].focus();
        Swal.fire({ icon:'error', title:'Error', text:'Please fill in all required fields correctly.' });
    }
});
</script>
</body>
</html>