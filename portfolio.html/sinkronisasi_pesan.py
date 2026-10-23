import os
import time
import pymysql
from dotenv import load_dotenv

# Membaca konfigurasi dari file .env
load_dotenv()

# Koneksi ke database Aiven (online)
def koneksi_aiven():
    return pymysql.connect(
        host=os.environ["DB_HOST"],
        port=int(os.environ.get("DB_PORT", "20055")),
        user=os.environ["DB_USER"],
        password=os.environ["DB_PASSWORD"],
        database=os.environ.get("DB_NAME", "defaultdb"),
        ssl={"check_hostname": True},
        cursorclass=pymysql.cursors.DictCursor
    )


# Koneksi ke database XAMPP (lokal)
def koneksi_xampp():
    return pymysql.connect(
        host="localhost",
        port=3306,
        user="root",
        password="",
        database="portfolio_amanda",
        cursorclass=pymysql.cursors.DictCursor
    )


# Sinkronisasi pesan dari Aiven ke XAMPP
def sinkronisasi():
    online = koneksi_aiven()

    try:
        lokal = koneksi_xampp()

        try:
            with online.cursor() as cursor_online:
                cursor_online.execute("""
                    SELECT id, nama, gmail, isi, tanggal_pengiriman_pesan
                    FROM pesan
                    ORDER BY id ASC
                """)
                semua_pesan = cursor_online.fetchall()

            with lokal.cursor() as cursor_lokal:
                cursor_lokal.execute("""
                    CREATE TABLE IF NOT EXISTS sinkronisasi_pesan (
                        aiven_id INT PRIMARY KEY,
                        nama VARCHAR(255),
                        gmail VARCHAR(255),
                        isi TEXT,
                        tanggal_pengiriman_pesan DATETIME
                    )
                """)

                for pesan in semua_pesan:
                    cursor_lokal.execute("""
                        INSERT IGNORE INTO sinkronisasi_pesan
                        (aiven_id, nama, gmail, isi, tanggal_pengiriman_pesan)
                        VALUES (%s, %s, %s, %s, %s)
                    """, (
                        pesan["id"],
                        pesan["nama"],
                        pesan["gmail"],
                        pesan["isi"],
                        pesan["tanggal_pengiriman_pesan"]
                    ))

            lokal.commit()
            print(
                f"Sinkronisasi selesai. "
                f"{len(semua_pesan)} pesan diperiksa."
            )

        finally:
            lokal.close()

    finally:
        online.close()


if __name__ == "__main__":
    while True:
        try:
            sinkronisasi()
        except Exception as error:
            print("Sinkronisasi gagal:", error)

        time.sleep(60)