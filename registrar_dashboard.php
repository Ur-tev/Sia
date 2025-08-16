<?php
session_start();

// Logout handler
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Registrar Dashboard</title>
  <style>
    body {
      margin: 0;
      font-family: Arial, sans-serif;
      display: flex;
      height: 100vh;
      background-color: #f4f4f4;
    }
    .sidebar {
      width: 220px;
      background-color: #2c3e50;
      color: white;
      padding: 20px;
      box-sizing: border-box;
      display: flex;
      flex-direction: column;
      gap: 15px;
    }
    .sidebar h2 {
      margin-top: 0;
      margin-bottom: 20px;
      font-weight: 700;
      border-bottom: 1px solid #34495e;
      padding-bottom: 10px;
    }
    .sidebar a {
      color: white;
      text-decoration: none;
      padding: 10px 8px;
      border-radius: 4px;
      transition: background-color 0.3s;
      font-weight: 600;
    }
    .sidebar a:hover {
      background-color: #34495e;
    }

    .main-content {
      flex: 1;
      padding: 30px;
      background: white;
      overflow-y: auto;
    }

    h1 {
      margin-top: 0;
      margin-bottom: 20px;
    }

    /* Responsive */
    @media (max-width: 600px) {
      body {
        flex-direction: column;
      }
      .sidebar {
        width: 100%;
        flex-direction: row;
        overflow-x: auto;
      }
      .sidebar a {
        flex: 1;
        text-align: center;
      }
    }
  </style>
</head>
<body>

  <div class="sidebar">
    <h2>Menu</h2>\
    <a href="section_assignment.php">Section Assignment</a>
    <a href="registrar_dashboard.php">Registrar Dashboard</a>
    <a href="admission_list.php">Admission Details</a>
    <a href="adding_documents.php">Adding Documents</a>
    <a href="registrar_grades.php">Finalize of Grades</a>
    <a href="registrar_regular.php">Registrar of Regular</a>
    <a href="?logout=1">Logout</a>
  </div>

  <div class="main-content">
    <h1>Registrar Dashboard</h1>
    <canvas id="registrarChart" width="600" height="400"></canvas>
  </div>

  <!-- Chart.js CDN -->
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script>
    const ctx = document.getElementById('registrarChart').getContext('2d');

    const data = {
      labels: ['March', 'April', 'May', 'June', 'July', 'August'],
      datasets: [{
        label: 'Number of Admissions',
        data: [120, 135, 150, 170, 160, 180],
        backgroundColor: 'rgba(46, 204, 113, 0.5)',
        borderColor: 'rgba(46, 204, 113, 1)',
        borderWidth: 2,
        fill: true,
        tension: 0.3
      }]
    };

    const config = {
      type: 'line',
      data: data,
      options: {
        responsive: true,
        scales: {
          y: {
            beginAtZero: true,
            ticks: {
              stepSize: 20
            }
          }
        },
        plugins: {
          legend: {
            display: true,
            position: 'top'
          },
          title: {
            display: true,
            text: 'Admissions Over the Last 6 Months',
            font: {
              size: 18,
              weight: 'bold'
            }
          }
        }
      }
    };

    new Chart(ctx, config);
  </script>
</body>
</html>
