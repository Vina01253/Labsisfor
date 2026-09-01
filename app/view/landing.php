<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="description" content="Landing Page E-Laboratorium Sistem Informasi">
  <meta name="author" content="">
  <link href="<?php echo isset($__asset) ? $__asset : 'asset/'; ?>images/logo_lab.png" rel="icon">
  <title><?php echo isset($title) ? $title : 'Selamat Datang - E-Laboratorium'; ?></title>
  
  <!-- Vendor and Custom CSS -->
  <link href="<?php echo isset($__asset) ? $__asset : 'asset/'; ?>vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
  <link href="<?php echo isset($__asset) ? $__asset : 'asset/'; ?>vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css">
  <link href="<?php echo isset($__asset) ? $__asset : 'asset/'; ?>css/ruang-admin.min.css" rel="stylesheet">
  <link href="<?php echo isset($__asset) ? $__asset : 'asset/'; ?>css/login-form.css" rel="stylesheet">
  <link href="<?php echo isset($__asset) ? $__asset : 'asset/'; ?>css/background-gradient.css" rel="stylesheet">
  <link href="<?php echo isset($__asset) ? $__asset : 'asset/'; ?>css/loader.css" rel="stylesheet">

  <style>
    body {
      background-color: #f8f9fc;
      min-height: 100vh;
      display: flex;
      flex-direction: column;
    }

    .hero-section {
      background: linear-gradient(135deg, #4e73df 0%, #224abe 50%, #0082c8 100%);
      color: #ffffff;
      padding: 60px 0 70px;
      position: relative;
      overflow: hidden;
    }

    .hero-section::before {
      content: "";
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background: radial-gradient(circle at 20% 50%, rgba(255, 255, 255, 0.12) 0%, transparent 60%);
      pointer-events: none;
    }

    .logo-container {
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 24px;
      margin-bottom: 24px;
      flex-wrap: wrap;
    }

    .logo-badge {
      background: rgba(255, 255, 255, 0.95);
      padding: 12px 18px;
      border-radius: 16px;
      box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15);
      display: inline-flex;
      align-items: center;
      justify-content: center;
      transition: transform 0.3s ease;
    }

    .logo-badge:hover {
      transform: translateY(-4px);
    }

    .logo-img {
      max-height: 70px;
      width: auto;
      object-fit: contain;
    }

    .hero-title {
      font-size: 2.3rem;
      font-weight: 800;
      letter-spacing: 0.5px;
      text-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
      margin-bottom: 12px;
    }

    .hero-subtitle {
      font-size: 1.15rem;
      color: rgba(255, 255, 255, 0.9);
      max-width: 650px;
      margin: 0 auto;
      font-weight: 300;
    }

    .card-custom {
      border: none;
      border-radius: 16px;
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
      transition: transform 0.3s ease, box-shadow 0.3s ease;
      background: #ffffff;
      height: 100%;
    }

    .card-custom:hover {
      transform: translateY(-5px);
      box-shadow: 0 15px 35px rgba(0, 0, 0, 0.12);
    }

    .section-icon-box {
      width: 54px;
      height: 54px;
      border-radius: 12px;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      font-size: 1.4rem;
      margin-bottom: 18px;
    }

    .vision-icon {
      background: rgba(78, 115, 223, 0.12);
      color: #4e73df;
    }

    .mission-icon {
      background: rgba(28, 200, 138, 0.12);
      color: #1cc88a;
    }

    .card-title-custom {
      font-size: 1.5rem;
      font-weight: 700;
      color: #2e384d;
      margin-bottom: 16px;
    }

    .vision-text {
      color: #5a5c69;
      font-size: 1.05rem;
      line-height: 1.8;
    }

    .mission-list {
      padding-left: 20px;
      margin-bottom: 0;
      color: #5a5c69;
    }

    .mission-list li {
      font-size: 1.02rem;
      line-height: 1.75;
      margin-bottom: 10px;
    }

    .mission-list li:last-child {
      margin-bottom: 0;
    }

    .cta-container {
      margin-top: -30px;
      position: relative;
      z-index: 10;
    }

    .btn-cta {
      background: linear-gradient(135deg, #0082c8 0%, #667db6 100%);
      border: none;
      color: #ffffff !important;
      font-size: 1.15rem;
      font-weight: 700;
      padding: 14px 42px;
      border-radius: 50px;
      box-shadow: 0 8px 25px rgba(0, 130, 200, 0.35);
      transition: all 0.3s ease;
      display: inline-flex;
      align-items: center;
      gap: 12px;
    }

    .btn-cta:hover {
      background: linear-gradient(135deg, #006da8 0%, #52679c 100%);
      transform: translateY(-3px) scale(1.02);
      box-shadow: 0 12px 30px rgba(0, 130, 200, 0.45);
      text-decoration: none;
    }

    .footer-landing {
      margin-top: auto;
      background-color: #ffffff;
      border-top: 1px solid #e3e6f0;
      padding: 24px 0;
      color: #858796;
      font-size: 0.9rem;
    }
  </style>
</head>

<body id="page-landing">
  <!-- Top Navigation Bar -->
  <header class="header-login">
    <div class="container">
      <nav class="navbar navbar-expand navbar-dark bg-transparent py-3">
        <a class="navbar-brand d-flex align-items-center" href="index.php">
          <img class="img-profile rounded-circle mr-2" src="<?php echo isset($__asset) ? $__asset : 'asset/'; ?>images/logo_lab.png" style="max-width: 42px" alt="Logo Lab">
          <span class="font-weight-bold text-white">E-Laboratorium</span>
        </a>
        <div class="ml-auto">
          <!-- CTA BUTTON: Quick access to login -->
          <a href="login" class="btn btn-outline-light btn-sm font-weight-bold px-3 py-2 rounded-pill shadow-sm">
            <i class="fas fa-sign-in-alt mr-1"></i> Masuk ke Sistem
          </a>
        </div>
      </nav>
    </div>
  </header>

  <!-- Hero Header Section -->
  <section class="hero-section text-center">
    <div class="container">
      <!-- HEADER LOGOS -->
      <div class="logo-container">
        <div class="logo-badge">
          <!-- Logo Jurusan -->
          <img src="asset/images/logo-jurusan.png" alt="Logo Jurusan" class="logo-img">
        </div>
        <div class="logo-badge">
          <!-- Logo Universitas -->
          <img src="asset/images/logo-universitas.png" alt="Logo Universitas" class="logo-img">
        </div>
      </div>

      <!-- HEADER TITLE -->
      <h1 class="hero-title text-white">E-Laboratorium Sistem Informasi</h1>
      <p class="hero-subtitle">
        Selamat Datang di Portal Resmi Sistem Informasi Praktikum dan Laboratorium SISFOR Institut Teknologi Adhi Tama Surabaya.
      </p>
    </div>
  </section>

  <!-- CTA Banner Section -->
  <div class="container cta-container text-center mb-5">
    <!-- CTA BUTTON -->
    <a href="login" class="btn btn-cta">
      <span>Masuk ke Sistem</span>
      <i class="fas fa-arrow-right"></i>
    </a>
  </div>

  <!-- Content Section: Visi & Misi -->
  <main class="container mb-5">
    <div class="row">
      <!-- VISION SECTION -->
      <div class="col-lg-6 mb-4">
        <div class="card card-custom p-4 p-md-5">
          <div class="card-body p-0">
            <div>
              <h2 style="color: #c88c00ff; font-weight: bold;">Visi Prodi</h2>
            </div>
            <h2 class="card-title-custom">Pusat Unggulan Pengelolaan Data dan Sistem Bisnis</h2>
            <p class="vision-text">Menjadi Program Studi yang <b>unggul dan inovatif</b> pada analisis proses bisnis untuk mengaplikasikan bidang pengelolaan data dan manajemen sistem yang berkelanjutan di sektor Enterprise.</p> <!-- TODO: replace with actual vision -->
          </div>
        </div>
      </div>

      <!-- MISSION SECTION -->
      <div class="col-lg-6 mb-4">
        <div class="card card-custom p-4 p-md-5">
          <div class="card-body p-0">
            <div>
              <h2 style="color: #c88c00ff; font-weight: bold;">Misi Prodi</h2>
            </div>
            <ol class="mission-list">
              <li>Menyelenggarakan sistem pendidikan berbasis <b>Outcome-Based Education (OBE)</b> yang akuntabel dan berkualitas dalam rangka menghasilkan sarjana Sistem Informasi pada bidang analisis proses bisnis untuk mengaplikasikan pengelolaan data dan manajemen sistem yang berkelanjutan di sektor Enterprise.</li> 
              <li>Menyelenggarakan penelitian yang berkualitas berbasis <b>riset, sains, dan teknologi</b> pada bidang analisis proses bisnis untuk mengaplikasikan pengelolaan data dan manajemen sistem yang berkelanjutan di sektor Enterprise.</li>
              <li>Melaksanakan <b>pengabdian kepada masyarakat</b> yang bermanfaat dan berkelanjutan di bidang sistem informasi.</li>
              <li>Menjalin <b>kerjasama</b> bidang sistem informasi yang berkelanjutan dengan institusi atau lembaga di dalam dan di luar negeri.</li>
            </ol>
          </div>
        </div>
      </div>
    </div>
  </main>

  <!-- Footer -->
  <footer class="footer-landing text-center">
    <div class="container">
      <span>Copyright &copy; <?php echo date("Y"); ?> - E-Laboratorium. All rights reserved.</span>
    </div>
  </footer>

  <!-- Scripts -->
  <script src="<?php echo isset($__asset) ? $__asset : 'asset/'; ?>vendor/jquery/jquery.min.js"></script>
  <script src="<?php echo isset($__asset) ? $__asset : 'asset/'; ?>vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>

</html>
