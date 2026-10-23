import os
import pymysql
from dotenv import load_dotenv

load_dotenv()

koneksi = pymysql.connect(
    host=os.environ["DB_HOST"],
    port=int(os.environ.get("DB_PORT", "20055")),
    user=os.environ["DB_USER"],
    password=os.environ["DB_PASSWORD"],
    database=os.environ.get("DB_NAME", "defaultdb"),
    ssl={"check_hostname": True},
    cursorclass=pymysql.cursors.DictCursor
)

try:
    with koneksi.cursor() as cursor:
        cursor.execute("SELECT * FROM pesan ORDER BY id DESC")
        pesan = cursor.fetchall()

        print("\n=== PESAN YANG TERSIMPAN DI AIVEN ===")
        print("Jumlah pesan:", len(pesan))

        for item in pesan:
            print("------------------------------")
            print("ID:", item["id"])
            print("Nama:", item["nama"])
            print("Gmail:", item["gmail"])
            print("Isi:", item["isi"])
            print("Tanggal:", item["tanggal_pengiriman_pesan"])

            cursor.execute("SELECT NOW() AS waktu_db, UTC_TIMESTAMP() AS waktu_utc")
            waktu = cursor.fetchone()

            print("Waktu database:", waktu["waktu_db"])
            print("Waktu UTC:", waktu["waktu_utc"])

finally:
    koneksi.close()