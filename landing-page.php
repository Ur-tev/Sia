<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>College Landing Page</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    html, body {
      margin: 0;
      padding: 0;
      overflow-x: hidden;
      font-family: 'Segoe UI', sans-serif;
    }

    .parallax-section {
      background-attachment: fixed;
      background-position: center;
      background-repeat: no-repeat;
      background-size: cover;
      min-height: 100vh;
      display: flex;
      justify-content: center;
      align-items: center;
      position: relative;
      color: white;
    }

    .parallax-section::before {
      content: '';
      position: absolute;
      inset: 0;
      background-color: rgba(0, 0, 0, 0.5);
      z-index: 1;
    }

    .top-content, .content {
      position: relative;
      z-index: 2;
    }

    .box {
      display: flex;
      gap: 20px;
      background: rgba(255, 255, 255, 0.95);
      padding: 40px;
      border-radius: 15px;
      box-shadow: 0 10px 25px rgba(0,0,0,0.2);
      flex-wrap: wrap;
      justify-content: center;
    }

    .btn-option {
      min-width: 150px;
    }

    .hero-card {
      border-radius: 10px;
      padding: 30px 20px;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
      transition: transform 0.3s;
      height: 100%;
      text-align: center;
    }

    .hero-card:hover {
      transform: translateY(-5px);
    }

    .hero-title {
      font-weight: bold;
      font-size: 1.5rem;
      margin-bottom: 10px;
    }

    .symbolism-section {
      background: linear-gradient(to bottom right, #fdfdfd, #e9ecef);
      padding: 80px 20px;
    }

    .symbolism-card {
      background-color: rgba(255, 255, 255, 0.2); 
      border-radius: 15px;
      backdrop-filter: blur(12px); 
      padding: 40px;
      box-shadow: 0 8px 24px rgba(0, 0, 0, 0.2);
    }

    .ncst-logo-section {
      text-align: center;
      padding: 40px 0;
      background-color: #fff;
    }

    .ncst-logo-section img {
      width: 150px;
      height: auto;
    }


    .makatao {
      background-color: #ff4d4d;
      color: white;
    }

    .makabayan {
      background-color: #007bff;
      color: white;
    }

    .makakalikasan {
      background-color: #ffcc00;
      color: #333;
    }

    .makadiyos {
      background-color: #ffffff;
      color: #333;
      border: 1px solid #ccc;
    }
  </style>
</head>
<body>


<div class="parallax-section" style="background-image: url('image.png');">
  <div class="top-content">
    <div class="box">
      <a href="admission.php" class="btn btn-primary btn-option fw-semibold fs-5">Admission Portal</a>
      <a href="login.php" class="btn btn-success btn-option fw-semibold fs-5">Student Login</a>
      <a href="treasury.php" class="btn btn-warning btn-option text-white fw-semibold fs-5">Treasury</a>
    </div>
  </div>
</div>


<div class="symbolism-section">
  <div class="container">
    <div class="symbolism-card mx-auto text-center">
      <h2 class="text-primary fw-bold mb-4">NCST Symbolism</h2>
      <p class="fs-5 text-dark lh-lg">
        The NCST logo symbolizes excellence, commitment to industry-driven education, and the institution's dedication to molding individuals who embody discipline, innovation, and Filipino values. It reflects our mission to build future professionals with strong character and technical expertise.
      </p>
    </div>
  </div>
</div>


<div class="parallax-section" style="background-image: url('476631147_596711336519691_7775981671927857968_n.jpg');">
  <div class="content">
    <h4 class="mb-5 text-white text-center fw-bold fs-3">Core Values of the Filipino</h4>
    <div class="container">
      <div class="row g-4">
        <div class="col-md-6 col-lg-3">
          <div class="hero-card makatao">
            <div class="hero-title">Makatao</div>
            <p>Respect and compassion for others.</p>
          </div>
        </div>
        <div class="col-md-6 col-lg-3">
          <div class="hero-card makabayan">
            <div class="hero-title">Makabayan</div>
            <p>Love for country and civic responsibility.</p>
          </div>
        </div>
        <div class="col-md-6 col-lg-3">
          <div class="hero-card makakalikasan">
            <div class="hero-title">Makakalikasan</div>
            <p>Care for the environment.</p>
          </div>
        </div>
        <div class="col-md-6 col-lg-3">
          <div class="hero-card makadiyos">
            <div class="hero-title">Makadiyos</div>
            <p>Faith and spiritual devotion.</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

</body>
</html>
