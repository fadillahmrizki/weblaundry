# 💻 Aplikasi Manajemen Laundry Berbasis Web

Sebuah aplikasi web sederhana yang dibangun dengan PHP native untuk mengelola proses bisnis di sebuah usaha laundry, mulai dari pencatatan transaksi hingga update status pengerjaan.

---

## ✨ Fitur-Fitur Utama

Aplikasi ini memiliki beberapa fitur utama, antara lain:

* **🔐 Sistem Autentikasi:** Login & Logout untuk admin/pengguna.
* **📊 Dashboard Admin:** Menampilkan ringkasan data transaksi dan status laundry.
* **👔 Manajemen Layanan:** Admin dapat menambah, melihat, mengubah, dan menghapus (CRUD) jenis layanan laundry (misal: cuci kering, cuci setrika, dll).
* **👥 Manajemen Pengguna:** Mengelola data pengguna yang terdaftar di sistem.
* **💸 Manajemen Transaksi:** Mencatat transaksi baru, melihat detail, mengupdate status pengerjaan (misal: Diterima, Dicuci, Selesai, Diambil), dan menghapus transaksi.
* **🔑 Lupa Password:** Fitur untuk mereset password pengguna.
* **Email & Password Admin**: Email: admin123@mail.com; Pass: admin123
* **Email & Password User**: Email: user123@mail.com; Pass: user123

---

## 🛠️ Teknologi yang Digunakan

* **Front-End:** HTML, CSS, JavaScript, Boostrap
* **Back-End:** PHP
* **Database:** MySQL
* **Web Server:** Apache (via XAMPP)

---

## 🚀 Cara Instalasi & Setup

Untuk menjalankan proyek ini di komputermu, ikuti langkah-langkah berikut:

1.  **Prasyarat**
    Pastikan kamu sudah menginstall **XAMPP** di komputermu.

2.  **Clone Repository**
    Buka terminal/CMD dan jalankan dibawah:
    git clone [https://github.com/](https://github.com/)[USERNAME_GITHUB_ANDA]/[NAMA_REPOSITORY_ANDA].git

3.  **Pindahkan Folder Proyek**
    Pindahkan folder hasil clone ke dalam direktori `htdocs` di dalam folder instalasi XAMPP-mu.
    (Contoh: `C:\xampp\htdocs\[NAMA_FOLDER_PROYEK]`)

4.  **Setup Database**
    * Nyalakan **Apache** dan **MySQL** dari XAMPP Control Panel.
    * Buka browser dan pergi ke `http://localhost/phpmyadmin`. Atau bisa juga klik button "admin" MySQL di XAMPP Control Panel. 
    * Buat database baru dengan nama `[NAMA_DATABASE_ANDA]`.
    * Pilih database yang baru kamu buat, lalu klik tab **Import**.
    * Klik "Choose File" dan pilih file `db_websiteku.sql` yang ada di dalam folder proyek ini.
    * Scroll ke bawah dan klik **"Import"**.

5.  **Konfigurasi Koneksi Database (PENTING!)**
    Buat file "koneksi.php" secara manual. Untuk menghubungkan semua file ke databasenya.

6.  **Jalankan Aplikasi**
    * Buka browser dan akses aplikasi melalui URL:
        `http://localhost/[NAMA_FOLDER_PROYEK]`

---
