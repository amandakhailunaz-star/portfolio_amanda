from flask import (
    Flask, request, redirect, url_for,
    render_template_string, session, send_from_directory
)
from functools import wraps
from datetime import timedelta
from email.message import EmailMessage
import pymysql
import os
import secrets
import hmac
import smtplib

app = Flask(__name__)

# Konfigurasi keamanan
app.secret_key = os.environ.get("FLASK_SECRET_KEY")

if not app.secret_key:
    raise RuntimeError("FLASK_SECRET_KEY belum diatur.")

ADMIN_USERNAME = os.environ.get("ADMIN_USERNAME", "admin")
ADMIN_PASSWORD = os.environ.get("ADMIN_PASSWORD")

if not ADMIN_PASSWORD:
    raise RuntimeError("ADMIN_PASSWORD belum diatur.")


# Koneksi database Aiven
def koneksi_database():
    return pymysql.connect(
        host=os.environ["DB_HOST"],
        port=int(os.environ.get("DB_PORT", "20055")),
        user=os.environ["DB_USER"],
        password=os.environ["DB_PASSWORD"],
        database=os.environ.get("DB_NAME", "defaultdb"),
        ssl={"check_hostname": True},
        cursorclass=pymysql.cursors.DictCursor,
        connect_timeout=15
    )


# Notifikasi Gmail
def kirim_notifikasi_email(nama, gmail, isi):
    email = EmailMessage()
    email["Subject"] = "Pesan Baru dari Website Portfolio"
    email["From"] = os.environ["SMTP_USER"]
    email["To"] = os.environ["MAIL_TO"]

    email.set_content(f"""
Ada pesan baru dari website portfolio Amanda.

Nama: {nama}
Email pengunjung: {gmail}

Isi pesan:
{isi}
""")

    with smtplib.SMTP_SSL("smtp.gmail.com", 465) as server:
        server.login(
            os.environ["SMTP_USER"],
            os.environ["SMTP_APP_PASSWORD"]
        )
        server.send_message(email)


# CSRF untuk halaman admin
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


# Proteksi halaman admin
def admin_required(f):
    @wraps(f)
    def decorated_function(*args, **kwargs):
        if not session.get("admin_login"):
            return redirect(url_for("admin_login"))
        return f(*args, **kwargs)
    return decorated_function


# Halaman Home
@app.route("/")
@app.route("/index.html")
def beranda():
    return send_from_directory(app.root_path, "index.html")


# Halaman About
@app.route("/about")
@app.route("/about.html")
def about():
    return send_from_directory(app.root_path, "about.html")


# Halaman Project
@app.route("/project")
@app.route("/project.html")
def project():
    return send_from_directory(app.root_path, "project.html")


# Halaman Contact
@app.route("/contact")
@app.route("/contact.html")
def kontak():
    return send_from_directory(app.root_path, "contact.html")


# File CSS dan gambar
@app.route("/style.css")
def serve_css():
    return send_from_directory(app.root_path, "style.css")


@app.route("/<path:nama_file>")
def serve_asset(nama_file):
    file_diizinkan = {
        "amanda.jpg",
        "amandakhailu.jpeg",
        "web_port.png",
        "web_restaurant.jpg",
        "database_minyakjelantah.jpeg"
    }

    if nama_file in file_diizinkan:
        return send_from_directory(app.root_path, nama_file)

    return "Halaman tidak ditemukan.", 404


# Halaman berhasil
HALAMAN_BERHASIL = """
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
    </style>
</head>
<body>
    <div class="card">
        <div class="icon">&#10004;</div>
        <h1>Pesan Berhasil Disimpan!</h1>
        <p>
            Terima kasih sudah menghubungi saya.
            Pesan kamu berhasil disimpan ke database.
        </p>
        <a href="/contact.html">Kembali ke Portfolio</a>
    </div>
</body>
</html>
"""


# Menerima dan menyimpan pesan
@app.route("/kirim-pesan", methods=["POST"])
def kirim_pesan():
    nama = request.form.get("nama", "").strip()
    gmail = request.form.get("gmail", "").strip()
    isi = request.form.get("isi", "").strip()

    if not nama or not gmail or not isi:
        return "Semua kolom wajib diisi.", 400

    if len(nama) > 100 or len(gmail) > 150:
        return "Nama atau email terlalu panjang.", 400

    koneksi = None

    try:
        koneksi = koneksi_database()

        with koneksi.cursor() as cursor:
            sql = """
                INSERT INTO pesan (nama, gmail, isi)
                VALUES (%s, %s, %s)
            """
            cursor.execute(sql, (nama, gmail, isi))

        koneksi.commit()

    except pymysql.MySQLError:
        app.logger.exception("Gagal menyimpan pesan ke Aiven")
        return "Pesan gagal disimpan. Silakan coba lagi.", 500

    finally:
        if koneksi:
            koneksi.close()

    # Email dikirim setelah pesan berhasil disimpan.
    # Jika email gagal, data database tetap tersimpan.
    try:
        kirim_notifikasi_email(nama, gmail, isi)
    except Exception:
        app.logger.exception("Pesan tersimpan, tetapi notifikasi email gagal.")

    return HALAMAN_BERHASIL


# Login Admin
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
            }
            .btn-pink {
                background: #d87599;
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
                        class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Password</label>
                    <input type="password" name="password"
                        class="form-control" required>
                </div>

                <button type="submit" class="btn btn-pink w-100">
                    Login
                </button>
            </form>

            <p class="text-center mt-3">
                <a href="/contact.html">Kembali ke Contact</a>
            </p>
        </div>
    </body>
    </html>
    """, pesan_error=pesan_error, token=token)


# Dashboard Admin
@app.route("/admin/dashboard")
@admin_required
def dashboard():
    koneksi = None

    try:
        koneksi = koneksi_database()

        with koneksi.cursor() as cursor:
            cursor.execute("""
                SELECT id, nama, gmail, isi, tanggal_pengiriman_pesan
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
                        <div class="card p-3 mb-3">
                            <div class="d-flex justify-content-between flex-wrap">
                                <h2 class="h5">{{ pesan.nama }}</h2>
                                <small>{{ pesan.tanggal_pengiriman_pesan }}</small>
                            </div>

                            <p><strong>Gmail:</strong> {{ pesan.gmail }}</p>
                            <p style="white-space: pre-wrap;">{{ pesan.isi }}</p>

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

                <a href="/contact.html" class="btn btn-secondary mt-3">
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


# Hapus pesan
@app.route("/admin/hapus/<int:pesan_id>", methods=["POST"])
@admin_required
def hapus_pesan(pesan_id):
    if not periksa_csrf():
        return "Permintaan tidak valid. Muat ulang halaman.", 400

    koneksi = None

    try:
        koneksi = koneksi_database()

        with koneksi.cursor() as cursor:
            cursor.execute(
                "DELETE FROM pesan WHERE id = %s",
                (pesan_id,)
            )

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


# Menjalankan aplikasi lokal
if __name__ == "__main__":
    app.run(debug=True)
