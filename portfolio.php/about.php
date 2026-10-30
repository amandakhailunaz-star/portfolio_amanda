<?php
require "koneksi.php";

// Mengambil data pribadi
$query_profil = "SELECT * FROM data_pribadi LIMIT 1";
$result_profil = mysqli_query($koneksi, $query_profil);
$profil = mysqli_fetch_assoc($result_profil);

// Mengambil data pendidikan
$query_pendidikan = "SELECT * FROM pendidikan";
$result_pendidikan = mysqli_query($koneksi, $query_pendidikan);
$data_pendidikan = mysqli_fetch_all($result_pendidikan, MYSQLI_ASSOC);

// Mengambil data skill
$query_skill = "SELECT * FROM skill";
$result_skill = mysqli_query($koneksi, $query_skill);
$data_skill = mysqli_fetch_all($result_skill, MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <meta name="description" content="Website portofolio Amanda Nazwa, mahasiswi Teknik Informatika.">

  <title>About Me | Amanda Nazwa</title>

  <link
    href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
    rel="stylesheet"
  >

  <link rel="stylesheet" href="style.css">
</head>

<body>

  <!-- NAVBAR -->
  <header>
    <nav class="navbar navbar-expand-lg navbar-light bg-white sticky-top border-bottom">
      <div class="container">

        <a class="navbar-brand fw-bold brand" href="index.php">
          Amanda<span>.</span>
        </a>

        <button
          class="navbar-toggler"
          type="button"
          data-bs-toggle="collapse"
          data-bs-target="#mainNav"
          aria-controls="mainNav"
          aria-expanded="false"
          aria-label="Buka navigasi"
        >
          <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="mainNav">
          <ul class="navbar-nav ms-auto">

            <li class="nav-item">
              <a class="nav-link" href="index.php">Home</a>
            </li>

            <li class="nav-item">
              <a class="nav-link active" href="about.php">About</a>
            </li>

            <li class="nav-item">
              <a class="nav-link" href="project.php">Project</a>
            </li>

            <li class="nav-item">
              <a class="nav-link" href="contact.php">Contact</a>
            </li>

          </ul>
        </div>

      </div>
    </nav>
  </header>


  <!-- KONTEN ABOUT -->
  <main>

    <!-- JUDUL -->
    <section class="section-heading container text-center py-5">

      <p class="eyebrow fw-semibold text-uppercase">
        About Me
      </p>

      <h1 class="display-5 fw-bold">
        Let's Get to Know Me.
      </h1>

      <p class="mt-3">
        Kenali saya lebih dekat, mulai dari latar belakang,
        kemampuan, hingga hal-hal yang saya sukai.
      </p>

    </section>


    <!-- PROFIL -->
    <section class="container pb-4">

      <div class="row align-items-center g-4">

        <!-- FOTO -->
        <div class="col-12 col-md-4 text-center">

          <img
            src="<?php
              echo !empty($profil['foto'])
                ? htmlspecialchars($profil['foto'])
                : 'amanda.jpg';
            ?>"
            class="img-fluid about-img"
            alt="Foto Amanda Nazwa"
          >

        </div>


        <!-- INFORMASI DIRI -->
        <div class="col-12 col-md-8">

          <div class="content-panel p-4 p-lg-5">

            <h2 class="h3">
              Hi, I'm <?php
                echo htmlspecialchars($profil['nama'] ?? 'Amanda');
              ?>!
            </h2>

            <p>
              <?php
                echo htmlspecialchars(
                  $profil['deskripsi'] ?? 'Informasi belum tersedia.'
                );
              ?>
            </p>

            <p>
              Saya berusaha terus belajar melalui tugas perkuliahan
              dan proyek kecil. Saya percaya bahwa proses,
              memperbaiki kesalahan, dan bekerja sama dapat
              membantu saya untuk terus berkembang.
            </p>


            <!-- INFORMASI SINGKAT -->
            <h3 class="h5 mt-4">
              Informasi Singkat
            </h3>

            <ul class="info-list">

              <li>
                <strong>Status:</strong> Mahasiswa aktif
              </li>

              <li>
                <strong>Bidang:</strong>
                <?php
                  echo htmlspecialchars($profil['profesi'] ?? 'Teknik Informatika');
                ?>
              </li>

              <li>
                <strong>Minat:</strong> Database, MySQL, dan Internet of Things
              </li>

            </ul>


            <!-- PENDIDIKAN -->
            <h3 class="h5 mt-4">
              Riwayat Pendidikan
            </h3>

            <ul class="info-list">

              <?php
              if (!empty($data_pendidikan)) {
                  foreach ($data_pendidikan as $pendidikan) {
                      echo "<li>";

                      echo "<strong>" .
                        htmlspecialchars($pendidikan['nama_sekolah']) .
                        "</strong><br>";

                      echo htmlspecialchars($pendidikan['jurusan'] ?? '');

                      echo " (" .
                        htmlspecialchars($pendidikan['tahun_mulai'] ?? '') .
                        " - " .
                        htmlspecialchars($pendidikan['tahun_selesai'] ?? '') .
                        ")";

                      echo "</li>";
                  }
              } else {
                  echo "<li>Data pendidikan belum tersedia.</li>";
              }
              ?>

            </ul>


            <!-- SKILLS -->
            <h3 class="h5 mt-4">
              My Skills
            </h3>

            <div class="d-flex flex-wrap gap-2">

              <?php
              if (!empty($data_skill)) {
                  foreach ($data_skill as $skill) {
                      echo '<span class="badge skill-badge">';
                      echo htmlspecialchars($skill['nama_skill']);
                      echo '</span>';
                  }
              } else {
                  echo "Data skill belum tersedia.";
              }
              ?>

            </div>


            <!-- HOBI -->
            <h3 class="h5 mt-4">
              My Hobbies
            </h3>

            <ul class="info-list">

              <li>Menyanyi</li>

              <li>Mengikuti kegiatan sosial dan menjadi relawan</li>

              <li>
                Berkomunikasi dengan banyak orang melalui kegiatan
                sebagai MC (Master of Ceremony).
              </li>

            </ul>


            <!-- TUJUAN -->
            <h3 class="h5 mt-4">
              Tujuan Saya
            </h3>

            <p>
              Saya ingin mengasah kemampuan dalam bidang teknologi
              dan mendapatkan pengalaman dari berbagai proyek.
              Saya juga ingin terus belajar, mengembangkan kreativitas,
              dan menciptakan karya yang bermanfaat bagi orang lain.
            </p>


            <!-- TOMBOL -->
            <div class="mt-4 d-flex flex-wrap align-items-center gap-3">

              <a
                class="text-link"
                href="https://github.com/amandakhailunaz-star"
                target="_blank"
                rel="noopener noreferrer"
              >
                Kunjungi GitHub ↗
              </a>

              <a
                class="btn btn-primary"
                href="contact.php"
              >
                Hubungi Saya
              </a>

            </div>

          </div>

        </div>

      </div>

    </section>

  </main>


  <!-- FOOTER -->
  <footer class="py-4 mt-5">

    <div class="container text-center">

      <p class="mb-0">
        © 2026 Amanda Nazwa. All rights reserved.
      </p>

    </div>

  </footer>

  <script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
  ></script>

</body>
</html>
