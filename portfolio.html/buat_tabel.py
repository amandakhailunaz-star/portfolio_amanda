import pymysql

HOST = input("Masukkan Host Aiven: ").strip()
PORT = int(input("Masukkan Port Aiven: "))
USER = input("Masukkan Username Aiven: ").strip()
PASSWORD = input("Masukkan Password Aiven: ")
DATABASE = input("Masukkan nama database: ").strip()

koneksi = None

try:
    koneksi = pymysql.connect(
        host=HOST,
        port=PORT,
        user=USER,
        password=PASSWORD,
        database=DATABASE,
        ssl={"check_hostname": True},
        connect_timeout=15
    )

    with koneksi.cursor() as cursor:

        # 1. Membuat tabel pesan jika belum ada
        cursor.execute("""
            CREATE TABLE IF NOT EXISTS pesan (
                id INT AUTO_INCREMENT PRIMARY KEY,
                nama VARCHAR(100) NOT NULL,
                gmail VARCHAR(150) NOT NULL,
                isi TEXT NOT NULL,
                tanggal_pengiriman_pesan TIMESTAMP
                    DEFAULT CURRENT_TIMESTAMP
            )
        """)

        # 2. Memeriksa kolom gmail
        cursor.execute("SHOW COLUMNS FROM pesan LIKE 'gmail'")
        kolom_gmail = cursor.fetchone()

        if not kolom_gmail:
            cursor.execute("""
                ALTER TABLE pesan
                ADD COLUMN gmail VARCHAR(150) NULL
            """)
            print("Kolom gmail berhasil ditambahkan.")
        else:
            print("Kolom gmail sudah tersedia.")

        # 3. Memeriksa kolom email lama
        cursor.execute("SHOW COLUMNS FROM pesan LIKE 'email'")
        kolom_email = cursor.fetchone()

        if kolom_email:
            cursor.execute("""
                ALTER TABLE pesan
                MODIFY COLUMN email VARCHAR(150) NULL
            """)
            print("Kolom email sekarang boleh kosong.")
        else:
            print("Kolom email tidak ditemukan.")

    # 4. Menyimpan perubahan database
    koneksi.commit()

    print("BERHASIL! Struktur tabel pesan sudah diperiksa.")

except Exception as error:
    if koneksi:
        koneksi.rollback()

    print("Koneksi atau perbaikan tabel gagal:", error)

finally:
    if koneksi:
        koneksi.close()
        print("Koneksi database ditutup.")