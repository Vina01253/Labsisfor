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

    /* Hero Section with Auto-sliding Background Slideshow */
    .hero-section {
      position: relative;
      background-color: #1a233a; /* Fallback background */
      color: #ffffff;
      padding: 65px 0 75px;
      overflow: hidden;
    }

    /* Slideshow Container */
    .hero-slideshow {
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      z-index: 1;
      overflow: hidden;
    }

    /* Individual Slide */
    .hero-slide {
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      opacity: 0;
      transition: opacity 1.2s ease-in-out;
      pointer-events: none;
    }

    .hero-slide.active {
      opacity: 1;
      pointer-events: auto;
    }

    .hero-slide-img {
      width: 100%;
      height: 100%;
      object-fit: cover;
      object-position: center;
      display: block;
      transform: scale(1.04);
      transition: transform 6s ease-out;
    }

    .hero-slide.active .hero-slide-img {
      transform: scale(1);
    }

    /* Dark Overlay with Theme Blue Gradient Blend */
    .hero-overlay {
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background: rgba(0, 0, 0, 0.5); /* Required 50% dark overlay layer */
      z-index: 2;
    }

    .hero-overlay::after {
      content: "";
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background: linear-gradient(135deg, rgba(78, 115, 223, 0.5) 0%, rgba(34, 74, 190, 0.55) 50%, rgba(0, 130, 200, 0.5) 100%);
      pointer-events: none;
    }

    /* Hero Content Layer */
    .hero-section .container {
      position: relative;
      z-index: 3;
    }

    /* Slide Navigation Indicators */
    .slideshow-indicators {
      display: flex;
      justify-content: center;
      gap: 8px;
      margin-top: 24px;
    }

    .indicator-dot {
      width: 10px;
      height: 10px;
      border-radius: 50%;
      background: rgba(255, 255, 255, 0.45);
      border: 1px solid rgba(255, 255, 255, 0.75);
      cursor: pointer;
      transition: all 0.3s ease;
      display: inline-block;
    }

    .indicator-dot.active,
    .indicator-dot:hover {
      background: #ffffff;
      width: 26px;
      border-radius: 8px;
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

  <!-- Hero Header Section with Slideshow Background -->
  <section class="hero-section text-center">
    <!-- Background Slideshow -->
    <div class="hero-slideshow" aria-hidden="true">
      <div class="hero-slide active">
        <img src="<?php echo isset($__asset) ? $__asset : 'asset/'; ?>images/slideshow/slide1.jpg" alt="Lab Activity 1" class="hero-slide-img"> <!-- slide1.jpg --> <!-- TODO: replace with lab activity photo -->
      </div>
      <div class="hero-slide">
        <img src="<?php echo isset($__asset) ? $__asset : 'asset/'; ?>images/slideshow/slide2.jpg" alt="Lab Activity 2" class="hero-slide-img"> <!-- slide2.jpg --> <!-- TODO: replace with lab activity photo -->
      </div>
      <div class="hero-slide">
        <img src="<?php echo isset($__asset) ? $__asset : 'asset/'; ?>images/slideshow/slide3.jpg" alt="Lab Activity 3" class="hero-slide-img"> <!-- slide3.jpg --> <!-- TODO: replace with lab activity photo -->
      </div>
    </div>

    <!-- Dark Overlay with Subtle Blue Gradient Blend -->
    <div class="hero-overlay" aria-hidden="true"></div>

    <div class="container">
      <!-- HEADER LOGOS -->
      <div class="logo-container">
        <div class="logo-badge">
          <img src="asset/images/logo-universitas.png" alt="Logo Universitas" class="logo-img">
        </div>
        <div class="logo-badge">
          <img src="asset/images/logo-jurusan.png" alt="Logo Jurusan" class="logo-img">
        </div>
      </div>

      <!-- HEADER TITLE -->
      <h1 class="hero-title text-white">E-Laboratorium Sistem Informasi</h1>
      <p class="hero-subtitle">
        Selamat Datang di Portal Praktikum dan Laboratorium <br/> Program Studi Sistem Informasi ITATS.
      </p>

      <!-- Slideshow Navigation Indicators -->
      <div class="slideshow-indicators" aria-label="Slideshow Indicators">
        <span class="indicator-dot active" data-slide="0" title="Slide 1"></span>
        <span class="indicator-dot" data-slide="1" title="Slide 2"></span>
        <span class="indicator-dot" data-slide="2" title="Slide 3"></span>
        <span class="indicator-dot" data-slide="3" title="Slide 4"></span>
      </div>
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

  <!-- Vanilla JS Auto-sliding Background Slideshow -->
  <script>
    document.addEventListener('DOMContentLoaded', function () {
      var slides = document.querySelectorAll('.hero-slideshow .hero-slide');
      var dots = document.querySelectorAll('.slideshow-indicators .indicator-dot');
      if (slides.length <= 1) return;

      var currentSlide = 0;
      var slideInterval = 5000; // Transitions automatically every 5 seconds
      var timer = null;

      function goToSlide(index) {
        slides[currentSlide].classList.remove('active');
        if (dots[currentSlide]) {
          dots[currentSlide].classList.remove('active');
        }

        currentSlide = index;

        slides[currentSlide].classList.add('active');
        if (dots[currentSlide]) {
          dots[currentSlide].classList.add('active');
        }
      }

      function nextSlide() {
        var next = (currentSlide + 1) % slides.length;
        goToSlide(next);
      }

      function startSlideshow() {
        stopSlideshow();
        timer = setInterval(nextSlide, slideInterval);
      }

      function stopSlideshow() {
        if (timer) {
          clearInterval(timer);
          timer = null;
        }
      }

      // Allow clicking dots to switch slides manually
      dots.forEach(function (dot) {
        dot.addEventListener('click', function () {
          var targetIndex = parseInt(this.getAttribute('data-slide'), 10);
          goToSlide(targetIndex);
          startSlideshow(); // Reset 5-second interval on interaction
        });
      });

      startSlideshow();
    });
  </script>
</body>

</html>
