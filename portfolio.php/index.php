<?php
require "koneksi.php";

// Mengambil data profil dari database
$query_profil = "SELECT * FROM data_pribadi LIMIT 1";
$result_profil = mysqli_query($koneksi, $query_profil);
$profil = mysqli_fetch_assoc($result_profil);
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="description" content="Website portofolio Amanda Nazwa, mahasiswa Teknik Informatika.">
  <title>Beranda | Amanda Nazwa</title>
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
  <section class="hero py-5">
    <div class="container py-lg-5">
      <div class="row align-items-center g-5">

        <div class="col-12 col-md-7">
          <p class="eyebrow fw-semibold text-uppercase mb-2">Halo, I'm</p>

          <h1 class="display-3 fw-bold mb-3">
            <?php echo htmlspecialchars($profil['nama']); ?> <span>.</span>
          </h1>

          <h2 class="h4 mb-3">
            <?php echo htmlspecialchars($profil['profesi']); ?>
          </h2>

          <p class="lead mb-4">
            <?php echo htmlspecialchars($profil['deskripsi']); ?>
          </p>

          <a href="about.php" class="btn btn-brown btn-lg me-2 mb-2">About me</a>
          <a href="project.php" class="btn btn-outline-brown btn-lg mb-2">Lihat Project</a>
        </div>

        <div class="col-12 col-md-5 text-center">
          <img
            src="<?php echo htmlspecialchars($profil['foto']); ?>"
            class="img-fluid profile-img"
            alt="Foto profil Amanda Nazwa">
        </div>

      </div>
    </div>
  </section>

  <section class="welcome py-5">
    <div class="container text-center py-3">
      <p class="eyebrow fw-semibold text-uppercase">Welcome to my portfolio</p>
      <h2 class="display-6 fw-semibold">Belajar dan terus berkembang.</h2>
      <p class="mx-auto mt-3 section-copy">
        Website ini berisi informasi singkat tentang saya, skills, hobi, dan beberapa proyek yang pernah saya kerjakan.
      </p>
    </div>
  </section>
</main>

<footer class="py-4 mt-5">
  <div class="container text-center">
    <p class="mb-0">© 2026 Amanda Nazwa</p>
  </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
