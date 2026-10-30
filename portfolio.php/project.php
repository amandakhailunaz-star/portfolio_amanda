<?php
require "koneksi.php";

// Mengambil data proyek dari database
$query = "SELECT * FROM portfolio";
$result = mysqli_query($koneksi, $query);

// Mengambil seluruh data proyek
$data_proyek = mysqli_fetch_all($result, MYSQLI_ASSOC);
?>

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

      <a class="navbar-brand fw-bold brand" href="index.php">
        Amanda<span>.</span>
      </a>

      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav" aria-controls="mainNav" aria-expanded="false" aria-label="Buka navigasi">
        <span class="navbar-toggler-icon"></span>
      </button>

      <div class="collapse navbar-collapse" id="mainNav">
        <ul class="navbar-nav ms-auto">
          <li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>
          <li class="nav-item"><a class="nav-link" href="about.php">About</a></li>
          <li class="nav-item"><a class="nav-link active" href="project.php">Project</a></li>
          <li class="nav-item"><a class="nav-link" href="contact.php">Contact</a></li>
        </ul>
      </div>

    </div>
  </nav>
</header>

<main>

  <!-- JUDUL -->
  <section class="section-heading container text-center py-5">
    <p class="eyebrow fw-semibold text-uppercase">Project Saya</p>

    <h1 class="display-5 fw-bold">
      Hal yang pernah saya buat
    </h1>

    <p class="section-copy mx-auto mt-3">
      Berikut beberapa project nya :
    </p>
  </section>


  <!-- DAFTAR PROYEK DARI DATABASE -->
  <section class="container pb-5">

    <div class="row g-4">

      <?php
      if (!empty($data_proyek)) {

          foreach ($data_proyek as $proyek) {
      ?>

        <div class="col-12 col-md-6 col-lg-4">

          <article class="card project-card h-100">

            <img
              src="assets/<?php echo htmlspecialchars($proyek['gambar']); ?>"
              class="card-img-top project-img"
              alt="<?php echo htmlspecialchars($proyek['nama_proyek']); ?>"
            >

            <div class="card-body d-flex flex-column">

              <h2 class="h5 card-title">
                <?php echo htmlspecialchars($proyek['nama_proyek']); ?>
              </h2>

              <p class="card-text">
                <?php echo htmlspecialchars($proyek['deskripsi']); ?>
              </p>

              <div class="mt-auto">

                <span class="badge skill-badge">
                  Portfolio
                </span>

                <span class="badge skill-badge">
                  Web Development
                </span>

              </div>

              <?php if (!empty($proyek['link_proyek']) && $proyek['link_proyek'] != '#') { ?>

                <a
                  href="<?php echo htmlspecialchars($proyek['link_proyek']); ?>"
                  class="btn btn-primary mt-3"
                  target="_blank"
                  rel="noopener noreferrer"
                >
                  Lihat Proyek
                </a>

              <?php } ?>

            </div>

          </article>

        </div>

      <?php
          }

      } else {
          echo "<p class='text-center'>Belum ada proyek yang tersedia.</p>";
      }
      ?>

    </div>

  </section>

</main>


<!-- FOOTER -->
<footer class="py-4 mt-5">
  <div class="container text-center">
    <p class="mb-0">
      © 2026 Amanda Nazwa
    </p>
  </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
