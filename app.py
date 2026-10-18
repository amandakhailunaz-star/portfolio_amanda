from flask import (
    Flask, request, redirect, url_for,
    render_template_string, session, flash,
    send_from_directory
)
from functools import wraps
import pymysql
import os
import secrets
import hmac

app = Flask(__name__)

# Secret key untuk session
app.secret_key = os.environ.get("FLASK_SECRET_KEY")

if not app.secret_key:
    raise RuntimeError(
        "Atur environment variable FLASK_SECRET_KEY sebelum menjalankan aplikasi."
    )

# Konfigurasi login Admin
ADMIN_USERNAME = os.environ.get("ADMIN_USERNAME", "admin")
ADMIN_PASSWORD = os.environ.get("ADMIN_PASSWORD")

if not ADMIN_PASSWORD:
    raise RuntimeError(
        "Atur environment variable ADMIN_PASSWORD sebelum menjalankan aplikasi."
    )


# Koneksi database
def koneksi_database():
    return pymysql.connect(
        host="localhost",
        user="root",
        password="",
        database="portfolio_amanda",
        cursorclass=pymysql.cursors.DictCursor
    )


# Perlindungan CSRF sederhana
def token_csrf():
    if "csrf_token" not in session:
        session["csrf_token"] = secrets.token_hex(32)

    return session["csrf_token"]


def periksa_csrf():
    token_form = request.form.get("csrf_token", "")
    token_session = session.get("csrf_token", "")

    return bool(token_session) and hmac.compare_digest(
        token_form, token_session
    )


# Memeriksa status login
def admin_required(f):
    @wraps(f)
    def decorated_function(*args, **kwargs):
        if not session.get("admin_login"):
            return redirect(url_for("admin_login"))

        return f(*args, **kwargs)

    return decorated_function


# Halaman utama
@app.route("/")
@app.route("/index.html")
def beranda():
    lokasi_file = os.path.join(app.root_path, "index.html")

    with open(lokasi_file, "r", encoding="utf-8") as file:
        html = file.read()

    return html

# Halaman About
@app.route("/about")
@app.route("/about.html")
def about():
    lokasi_file = os.path.join(app.root_path, "about.html")

    with open(lokasi_file, "r", encoding="utf-8") as file:
        html = file.read()

    return html


# Halaman Project
@app.route("/project")
@app.route("/project.html")
def project():
    lokasi_file = os.path.join(app.root_path, "project.html")

    with open(lokasi_file, "r", encoding="utf-8") as file:
        html = file.read()

    return html

# Menampilkan file CSS
@app.route("/style.css")
def serve_css():
    return send_from_directory(app.root_path, "style.css")


# Menampilkan foto profil
@app.route("/amandakhailu.jpeg")
def serve_foto():
    return send_from_directory(app.root_path, "amandakhailu.jpeg")

# Menampilkan foto About
@app.route("/amanda.jpg")
def serve_foto_about():
    return send_from_directory(app.root_path, "amanda.jpg")

@app.route("/web_port.png")
def serve_web_port():
    return send_from_directory(app.root_path, "web_port.png")


@app.route("/web_restaurant.jpg")
def serve_web_restaurant():
    return send_from_directory(app.root_path, "web_restaurant.jpg")


@app.route("/database_minyakjelantah.jpeg")
def serve_database():
    return send_from_directory(app.root_path, "database_minyakjelantah.jpeg")

# Halaman Contact
@app.route("/contact")
def kontak():
    lokasi_file = os.path.join(app.root_path, "contact.html")

    with open(lokasi_file, "r", encoding="utf-8") as file:
        html = file.read()

    return html


# Menerima dan menyimpan pesan
@app.route("/kirim-pesan", methods=["POST"])
def kirim_pesan():
    nama = request.form.get("nama", "").strip()
    email = request.form.get("email", "").strip()
    isi = request.form.get("isi", "").strip()

    if not nama or not email or not isi:
        return """
        <script>
            alert("Semua kolom wajib diisi!");
            window.location.href = "/contact";
        </script>
        """, 400

    koneksi = None

    try:
        koneksi = koneksi_database()

        with koneksi.cursor() as cursor:
            sql = """
                INSERT INTO pesan (nama, email, isi)
                VALUES (%s, %s, %s)
            """
            cursor.execute(sql, (nama, email, isi))

        koneksi.commit()

        return """
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Pesan Berhasil</title>

    <style>
        body {
            margin: 0;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            background: #f9f3ef;
            font-family: Arial, sans-serif;
            color: #5b4038;
        }

        .card {
            background: white;
            padding: 40px 30px;
            border-radius: 20px;
            text-align: center;
            max-width: 400px;
            margin: 20px;
            box-shadow: 0 8px 25px #704b3b18;
        }

        .icon {
            font-size: 60px;
            margin-bottom: 15px;
        }

        h1 {
            color: #b66f7f;
            font-size: 25px;
        }

        p {
            line-height: 1.7;
        }

        a {
            display: inline-block;
            margin-top: 15px;
            padding: 12px 24px;
            background: #b66f7f;
            color: white;
            text-decoration: none;
            border-radius: 10px;
        }

        a:hover {
            background: #965467;
        }
    </style>
</head>

<body>
    <div class="card">
        <div class="icon">&#10004;</div>

        <h1>Pesan Berhasil Dikirim!</h1>

        <p>
            Terima kasih sudah menghubungi saya.
            Pesan kamu berhasil disimpan.
        </p>

        <a href="/contact">Kembali ke Portfolio</a>
    </div>
</body>
</html>
"""

    except pymysql.MySQLError:
        app.logger.exception("Gagal menyimpan pesan")

        return """
        <script>
            alert("Pesan gagal dikirim. Silakan coba lagi.");
            window.location.href = "/contact";
        </script>
        """, 500

    finally:
        if koneksi:
            koneksi.close()


# Halaman Login Admin
@app.route("/admin", methods=["GET", "POST"])
def admin_login():
    if session.get("admin_login"):
        return redirect(url_for("dashboard"))

    pesan_error = ""

    if request.method == "POST":
        if not periksa_csrf():
            return "Permintaan tidak valid. Muat ulang halaman login.", 400

        username = request.form.get("username", "")
        password = request.form.get("password", "")

        if (
            hmac.compare_digest(username, ADMIN_USERNAME)
            and hmac.compare_digest(password, ADMIN_PASSWORD)
        ):
            session.clear()
            session["admin_login"] = True
            token_csrf()

            return redirect(url_for("dashboard"))

        pesan_error = "Username atau password salah."

    token = token_csrf()

    return render_template_string("""
    <!DOCTYPE html>
    <html lang="id">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Login Admin</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
        <style>
            body {
                background: #f8eef2;
                min-height: 100vh;
                display: flex;
                align-items: center;
                justify-content: center;
            }
            .login-card {
                background: white;
                width: 100%;
                max-width: 400px;
                padding: 30px;
                border-radius: 18px;
                box-shadow: 0 5px 25px #00000012;
            }
            .btn-pink {
                background: #d87599;
                color: white;
            }
            .btn-pink:hover {
                background: #c55e83;
                color: white;
            }
        </style>
    </head>
    <body>
        <div class="login-card">
            <h2 class="text-center mb-4">Admin Login</h2>

            {% if pesan_error %}
                <div class="alert alert-danger">{{ pesan_error }}</div>
            {% endif %}

            <form method="POST">
                <input type="hidden" name="csrf_token" value="{{ token }}">

                <div class="mb-3">
                    <label class="form-label">Username</label>
                    <input type="text" name="username"
                        class="form-control" required autocomplete="username">
                </div>

                <div class="mb-3">
                    <label class="form-label">Password</label>
                    <input type="password" name="password"
                        class="form-control" required autocomplete="current-password">
                </div>

                <button type="submit" class="btn btn-pink w-100">
                    Login
                </button>
            </form>

            <p class="text-center mt-3 mb-0">
                <a href="/contact">Kembali ke Contact</a>
            </p>
        </div>
    </body>
    </html>
    """, pesan_error=pesan_error, token=token)


# Dashboard Admin: melihat pesan
@app.route("/admin/dashboard")
@admin_required
def dashboard():
    koneksi = None

    try:
        koneksi = koneksi_database()

        with koneksi.cursor() as cursor:
            cursor.execute("""
                SELECT id, nama, email, isi, tanggal_pengiriman_pesan
                FROM pesan
                ORDER BY id DESC
            """)
            daftar_pesan = cursor.fetchall()

        token = token_csrf()

        return render_template_string("""
        <!DOCTYPE html>
        <html lang="id">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1">
            <title>Dashboard Admin</title>
            <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
            
        </head>
        <body>
            <nav class="navbar border-bottom p-3">
                <div class="container">
                    <span class="navbar-brand fw-bold">Portfolio Admin</span>

                    <form action="{{ url_for('admin_logout') }}" method="POST">
                        <input type="hidden" name="csrf_token" value="{{ token }}">
                        <button class="btn btn-outline-danger btn-sm" type="submit">
                            Logout
                        </button>
                    </form>
                </div>
            </nav>

            <main class="container py-5">
                <h1 class="h3 mb-4">Pesan dari Pengunjung</h1>

                <p>Total pesan: <strong>{{ daftar_pesan|length }}</strong></p>

                {% if daftar_pesan %}
                    {% for pesan in daftar_pesan %}
                        <div class="message-card">
                            <div class="d-flex justify-content-between flex-wrap gap-2">
                                <h2 class="h5">{{ pesan.nama }}</h2>
                                <small class="text-muted">
                                    {{ pesan.tanggal_pengiriman_pesan }}
                                </small>
                            </div>

                            <p class="mb-2">
                                <strong>Email:</strong>
                                {{ pesan.email }}
                            </p>

                            <p class="mb-3" style="white-space: pre-wrap;">{{ pesan.isi }}</p>

                            <form action="{{ url_for('hapus_pesan', pesan_id=pesan.id) }}"
                                  method="POST"
                                  onsubmit="return confirm('Yakin ingin menghapus pesan ini?')">
                                <input type="hidden" name="csrf_token" value="{{ token }}">
                                <button type="submit" class="btn btn-outline-danger btn-sm">
                                    Hapus Pesan
                                </button>
                            </form>
                        </div>
                    {% endfor %}
                {% else %}
                    <div class="alert alert-info">
                        Belum ada pesan dari pengunjung.
                    </div>
                {% endif %}

                <a href="/contact" class="btn btn-secondary mt-3">
                    Lihat Halaman Contact
                </a>
            </main>
        </body>
        </html>
        """, daftar_pesan=daftar_pesan, token=token)

    except pymysql.MySQLError:
        app.logger.exception("Gagal mengambil data pesan")
        return "Gagal mengambil data pesan dari database.", 500

    finally:
        if koneksi:
            koneksi.close()


# Menghapus pesan
@app.route("/admin/hapus/<int:pesan_id>", methods=["POST"])
@admin_required
def hapus_pesan(pesan_id):
    if not periksa_csrf():
        return "Permintaan tidak valid. Muat ulang halaman.", 400

    koneksi = None

    try:
        koneksi = koneksi_database()

        with koneksi.cursor() as cursor:
            cursor.execute("DELETE FROM pesan WHERE id = %s", (pesan_id,))

        koneksi.commit()

        return redirect(url_for("dashboard"))

    except pymysql.MySQLError:
        app.logger.exception("Gagal menghapus pesan")
        return "Pesan gagal dihapus.", 500

    finally:
        if koneksi:
            koneksi.close()


# Logout Admin
@app.route("/admin/logout", methods=["POST"])
@admin_required
def admin_logout():
    if not periksa_csrf():
        return "Permintaan tidak valid. Muat ulang halaman.", 400

    session.clear()

    return redirect(url_for("admin_login"))


# Menjalankan aplikasi
if __name__ == "__main__":
    app.run(debug=True)
