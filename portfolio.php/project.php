<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="description" content="Website portofolio Amanda Nazwa, mahasiswa Teknik Informatika.">
  <title>Project | Amanda Nazwa</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="style.css">
</head>
<body>

<header>
  <nav class="navbar navbar-expand-lg navbar-light bg-white sticky-top border-bottom">
    <div class="container">
      <a class="navbar-brand fw-bold brand" href="index.php">Amanda<span>.</span></a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav" aria-controls="mainNav" aria-expanded="false" aria-label="Buka navigasi">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="mainNav">
        <ul class="navbar-nav ms-auto">
          <li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>
          <li class="nav-item"><a class="nav-link" href="about.php">About</a></li>
          <li class="nav-item"><a class="nav-link" href="project.php">Project</a></li>
          <li class="nav-item"><a class="nav-link" href="contact.php">Contact</a></li>
        </ul>
      </div>
    </div>
  </nav>
</header>

<main>
  <section class="section-heading container text-center py-5">
    <p class="eyebrow fw-semibold text-uppercase">Project Saya</p>
    <h1 class="display-5 fw-bold">Hal yang pernah saya buat</h1>
    <p class="section-copy mx-auto mt-3">Berikut beberapa project nya : </p>
  </section>
  <section class="container pb-5">
    <div class="row g-4">
      <div class="col-12 col-md-6 col-lg-4">
        <article class="card project-card h-100">
          <img src="web_port.png" class="card-img-top project-img" alt="Tampilan website portofolio">
          <div class="card-body d-flex flex-column">
            <h2 class="h5 card-title">Portfolio Website</h2>
            <p class="card-text">Website pribadi untuk memperkenalkan profil, keterampilan, hobi, dan karya saya.</p>
            <div class="mt-auto"><span class="badge skill-badge">HTML</span> <span class="badge skill-badge">CSS</span> <span class="badge skill-badge">Bootstrap 5</span></div>
          </div>
        </article>
      </div>
      <div class="col-12 col-md-6 col-lg-4">
        <article class="card project-card h-100">
          <img src="web_restaurant.jpg" class="card-img-top project-img" alt="Tampilan website restoran">
          <div class="card-body d-flex flex-column">
            <h2 class="h5 card-title">Restaurant Website</h2>
            <p class="card-text">Website restoran dengan fitur informasi menu, reservasi, dan pengelolaan data.</p>
            <div class="mt-auto"><span class="badge skill-badge">Python</span> <span class="badge skill-badge">Flask</span> <span class="badge skill-badge">MySQL</span></div>
          </div>
        </article>
      </div>
      <div class="col-12 col-md-6 col-lg-4">
        <article class="card project-card h-100">
          <img src="database_minyakjelantah.jpeg" class="card-img-top project-img" alt="Tampilan Database">
          <div class="card-body d-flex flex-column">
            <h2 class="h5 card-title">Database/Mysql</h2>
            <p class="card-text">Menyimpan data aplikasi web, sistem manajemen konten (yang pernah saya buat adalah database aplikasi penyetoran minyak jelantah).</p>
            <div class="mt-auto"><span class="badge skill-badge">Mysql</span> <span class="badge skill-badge">Database</span> <span class="badge skill-badge">Networking</span></div>
          </div>
        </article>
      </div>
    </div>
  </section>
</main>

<footer class="py-4 mt-5">
  <div class="container text-center">
    <p class="mb-0">© 2026 Amanda Nazwa </p>
  </div>
</footer>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
