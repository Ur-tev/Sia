<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Online Enrollment Schedule</title>
<style>
  /* Reset & base */
  * {
    box-sizing: border-box;
  }
  body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    margin: 0;
    padding: 40px 20px;
    background: #f5f7fa;
    color: #333;
  }

  h2 {
    text-align: center;
    color: #222;
    margin-bottom: 30px;
    font-weight: 700;
  }

  form {
    max-width: 700px;
    margin: 0 auto;
    background: #fff;
    padding: 30px 40px;
    border-radius: 8px;
    box-shadow: 0 6px 20px rgba(0,0,0,0.1);
  }

  label {
    display: block;
    margin-bottom: 12px;
    font-weight: 600;
    font-size: 1rem;
    cursor: pointer;
  }

  select, input[type="checkbox"], input[type="radio"] {
    cursor: pointer;
  }

  select {
    width: 100%;
    padding: 10px 12px;
    font-size: 1rem;
    border-radius: 5px;
    border: 1.8px solid #ccc;
    transition: border-color 0.3s ease;
    margin-top: 5px;
  }

  select:focus {
    border-color: #007BFF;
    outline: none;
  }

  .section {
    margin-top: 30px;
  }

  /* Tables */
  table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 12px;
    border-radius: 6px;
    overflow: hidden;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
  }

  th, td {
    padding: 12px 15px;
    text-align: left;
  }

  th {
    background-color: #007BFF;
    color: white;
    font-weight: 700;
    font-size: 1rem;
  }

  tbody tr {
    background-color: #fafafa;
    transition: background-color 0.3s ease;
  }

  tbody tr:nth-child(even) {
    background-color: #f0f4f9;
  }

  tbody tr:hover {
    background-color: #dbe7ff;
  }

  input[type="checkbox"], input[type="radio"] {
    transform: scale(1.2);
    margin-right: 10px;
  }

  /* Radio & checkbox label inline */
  .radio-group label,
  .checkbox-group label {
    display: inline-flex;
    align-items: center;
    margin-right: 25px;
    font-weight: 600;
    font-size: 1rem;
  }

  /* Custom Schedule multi-select */
  select[multiple] {
    height: 130px;
  }

  /* Button */
  button {
    margin-top: 40px;
    width: 100%;
    background-color: #007BFF;
    border: none;
    padding: 15px 0;
    color: white;
    font-size: 1.2rem;
    font-weight: 700;
    border-radius: 7px;
    cursor: pointer;
    transition: background-color 0.3s ease;
  }

  button:hover {
    background-color: #0056b3;
  }

  /* Hidden */
  .hidden {
    display: none;
  }

  /* Nested table inside Section B schedule */
  .nested-table {
    margin-top: 8px;
    width: 100%;
    border-collapse: collapse;
    font-size: 0.9rem;
  }
  .nested-table th,
  .nested-table td {
    padding: 4px;
    border: 1px solid #ccc;
  }
  .nested-table thead tr {
    background-color: #e0e7ff;
  }
</style>
</head>
<body>

<h2>Online Enrollment - Schedule Selection</h2>

<form id="enrollForm">

  <label for="scheduleType">
    Select Schedule Type:
    <select id="scheduleType" name="schedule_type" required>
      <option value="" disabled selected>-- Select Schedule Type --</option>
      <option value="regular">Regular</option>
      <option value="irregular">Irregular</option>
    </select>
  </label>

  <!-- Regular Schedule Table -->
  <div id="regularChoices" class="section hidden">
    <h3>Regular Schedule - Sections and Schedules</h3>
    <table>
      <thead>
        <tr>
          <th>Select</th>
          <th>Section</th>
          <th>Schedule</th>
          <th>Room</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td><input type="checkbox" name="regular_sections[]" value="Sec A" /></td>
          <td>Section A</td>
          <td>Mon, Wed, Fri 8:00 AM - 10:00 AM</td>
          <td>Room 101</td>
        </tr>
        <tr>
          <td><input type="checkbox" name="regular_sections[]" value="Sec B" /></td>
          <td>Section B</td>
          <td>
            Tue, Thu 10:00 AM - 12:00 PM<br />
            <small><strong>Total Units:</strong> 21 Units</small>
            <table class="nested-table">
              <thead>
                <tr>
                  <th>Subject</th>
                  <th>Units</th>
                  <th>Schedule</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td>Math 101</td>
                  <td>3</td>
                  <td>Tue 10:00 AM - 11:00 AM</td>
                </tr>
                <tr>
                  <td>English 102</td>
                  <td>3</td>
                  <td>Tue 11:00 AM - 12:00 PM</td>
                </tr>
                <tr>
                  <td>Science 103</td>
                  <td>4</td>
                  <td>Thu 10:00 AM - 12:00 PM</td>
                </tr>
                <tr>
                  <td>History 104</td>
                  <td>3</td>
                  <td>Thu 1:00 PM - 2:00 PM</td>
                </tr>
                <tr>
                  <td>PE 105</td>
                  <td>2</td>
                  <td>Fri 8:00 AM - 9:00 AM</td>
                </tr>
                <tr>
                  <td>Elective 106</td>
                  <td>6</td>
                  <td>Fri 9:00 AM - 12:00 PM</td>
                </tr>
              </tbody>
            </table>
          </td>
          <td>Room 102</td>
        </tr>
        <tr>
          <td><input type="checkbox" name="regular_sections[]" value="Sec C" /></td>
          <td>Section C</td>
          <td>Mon, Wed 1:00 PM - 3:00 PM</td>
          <td>Room 103</td>
        </tr>
      </tbody>
    </table>
  </div>

  <!-- Irregular Schedule Options -->
  <div id="irregularOptions" class="section hidden">
    <h3>Irregular Schedule - Choose Section or Custom Schedule</h3>

    <div class="radio-group">
      <label>
        <input type="radio" name="irregular_option" value="choices" /> Choose from Sections
      </label>
      <label>
        <input type="radio" name="irregular_option" value="custom_schedule" /> Custom Schedule
      </label>
    </div>

    <!-- Irregular Choices Section as Table -->
    <div id="irregularChoicesSection" class="hidden section">
      <h4>Choices Section - Sections and Schedules</h4>
      <table>
        <thead>
          <tr>
            <th>Select</th>
            <th>Section</th>
            <th>Schedule</th>
            <th>Room</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td><input type="checkbox" name="irregular_sections[]" value="Sec D" /></td>
            <td>Section D</td>
            <td>Mon, Wed, Fri 8:00 AM - 10:00 AM</td>
            <td>Room 201</td>
          </tr>
          <tr>
            <td><input type="checkbox" name="irregular_sections[]" value="Sec E" /></td>
            <td>Section E</td>
            <td>Tue, Thu 10:00 AM - 12:00 PM</td>
            <td>Room 202</td>
          </tr>
          <tr>
            <td><input type="checkbox" name="irregular_sections[]" value="Sec F" /></td>
            <td>Section F</td>
            <td>Fri 1:00 PM - 5:00 PM</td>
            <td>Room 203</td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Custom Schedule Section -->
    <div id="customScheduleSection" class="hidden section">
      <h4>Custom Schedule</h4>
      <label for="customDays">Pick Day(s):</label>
      <select id="customDays" name="custom_days[]" multiple size="5">
        <option value="mon">Monday</option>
        <option value="tue">Tuesday</option>
        <option value="wed">Wednesday</option>
        <option value="thu">Thursday</option>
        <option value="fri">Friday</option>
      </select><br /><br />
      
      <label>Pick Time Slots:</label><br />
      <label><input type="checkbox" name="custom_times[]" value="8am-10am" /> 8 AM - 10 AM</label><br />
      <label><input type="checkbox" name="custom_times[]" value="10am-12pm" /> 10 AM - 12 PM</label><br />
      <label><input type="checkbox" name="custom_times[]" value="1pm-3pm" /> 1 PM - 3 PM</label><br />
      <label><input type="checkbox" name="custom_times[]" value="3pm-5pm" /> 3 PM - 5 PM</label><br />
    </div>
  </div>

  <button type="submit">Submit Enrollment</button>
</form>

<script>
  const scheduleType = document.getElementById('scheduleType');
  const regularChoices = document.getElementById('regularChoices');
  const irregularOptions = document.getElementById('irregularOptions');
  const irregularChoicesSection = document.getElementById('irregularChoicesSection');
  const customScheduleSection = document.getElementById('customScheduleSection');
  const form = document.getElementById('enrollForm');

  scheduleType.addEventListener('change', function() {
    if (this.value === 'regular') {
      regularChoices.classList.remove('hidden');
      irregularOptions.classList.add('hidden');
      irregularChoicesSection.classList.add('hidden');
      customScheduleSection.classList.add('hidden');
      // Clear irregular option radios
      document.querySelectorAll('input[name="irregular_option"]').forEach(r => r.checked = false);
    } else if (this.value === 'irregular') {
      regularChoices.classList.add('hidden');
      irregularOptions.classList.remove('hidden');
    } else {
      regularChoices.classList.add('hidden');
      irregularOptions.classList.add('hidden');
      irregularChoicesSection.classList.add('hidden');
      customScheduleSection.classList.add('hidden');
    }
  });

  document.querySelectorAll('input[name="irregular_option"]').forEach(el => {
    el.addEventListener('change', function() {
      if (this.value === 'choices') {
        irregularChoicesSection.classList.remove('hidden');
        customScheduleSection.classList.add('hidden');
      } else if (this.value === 'custom_schedule') {
        irregularChoicesSection.classList.add('hidden');
        customScheduleSection.classList.remove('hidden');
      }
    });
  });

  form.addEventListener('submit', function(e) {
    if (scheduleType.value === 'irregular') {
      const irregularOptionChecked = document.querySelector('input[name="irregular_option"]:checked');
      if (!irregularOptionChecked) {
        alert('Please select an option for Irregular schedule: Choose from Sections or Custom Schedule.');
        e.preventDefault();
        return;
      }
      if (irregularOptionChecked.value === 'custom_schedule') {
        // Validate at least one day selected
        const daysSelected = Array.from(document.querySelectorAll('#customDays option:checked')).length;
        // Validate at least one time slot selected
        const timesSelected = Array.from(document.querySelectorAll('input[name="custom_times[]"]:checked')).length;
        if (daysSelected === 0 || timesSelected === 0) {
          alert('Please select at least one day and one time slot for your Custom Schedule.');
          e.preventDefault();
          return;
        }
      }
    }
  });
</script>

</body>
</html>
