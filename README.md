# PJBL Seblak Management System

Sistem manajemen ini digunakan untuk pengelolaan data pelanggan, produk, dan transaksi Seblak Mama Rizki.  
Proyek dikembangkan menggunakan PHP Native, MySQL, dan CSS manual.

---

# Persiapan Lingkungan

## Windows (XAMPP)

1. Install XAMPP dengan PHP 8.4.
2. Letakkan folder proyek di:

```txt
C:\xampp\htdocs\PJBL-Seblak
```

3. Pastikan Apache dan MySQL pada XAMPP Control Panel dalam status **Running**.

---

# Instalasi Database

1. Buka phpMyAdmin:

```txt
http://localhost/phpmyadmin
```

2. Buat database baru dengan nama:

```sql
seblak
```

3. Import file:

```txt
seblak.sql
```

4. Jalankan query berikut agar koneksi database di `db.php` tidak error:

```sql
CREATE USER 'seblak_user'@'localhost' IDENTIFIED BY '1234';

GRANT ALL PRIVILEGES ON seblak.* TO 'seblak_user'@'localhost';

FLUSH PRIVILEGES;
```

---

# Aturan Penggunaan Git

Seluruh anggota tim wajib mengikuti prosedur berikut untuk menghindari konflik dan kehilangan kode.

## Sebelum Mulai Coding

Selalu jalankan:

```bash
git pull origin main
```

Lakukan ini sebelum membuka VS Code atau mengubah file apa pun.

---

## Setelah Selesai Coding

```bash
git add .

git commit -m "pesan commit"

git push origin main
```

---

# Struktur Proyek

```txt
PJBL-Seblak/
│
├── auth/
│   └── check.php
│
├── customer/
│   ├── create.php
│   └── index.php
│
├── produk/
│   ├── create.php
│   └── index.php
│
├── pesanan/
│
├── transaksi/
│
├── layout/
│   ├── header.php
│   └── footer.php
│
├── db.php
├── index.php
├── style.css
└── seblak.sql
```

---

# Penjelasan File dan Folder

## `db.php`

Mengatur koneksi database menggunakan MySQLi.

---

## `auth/check.php`

Memastikan pengguna sudah login sebelum mengakses halaman tertentu.

---

## `layout/`

Berisi:

- `header.php`
- `footer.php`

Navbar dan layout global cukup diubah di folder ini agar seluruh halaman ikut berubah.

---

## `style.css`

Tempat seluruh CSS manual proyek.

Gunakan class:

```css
.icon-base
```

untuk memanggil ikon dari folder `/icons`.

---

## `index.php`

Halaman utama aplikasi yang menampilkan daftar menu.

---

## `customer/`, `produk/`, `pesanan/`, `transaksi/`

Berisi fitur CRUD:

- Create
- Read
- Update
- Delete

untuk masing-masing modul.

---

# Catatan Teknis

## Penanganan Stok

Jika stok produk bernilai:

```txt
0
```

maka sistem otomatis:

- menampilkan status `HABIS`
- menonaktifkan tombol tambah

---

## Keamanan Query SQL

Setiap input yang masuk ke query SQL wajib menggunakan:

```php
mysqli_real_escape_string()
```

untuk mengurangi risiko SQL Injection.

---

## Sistem Ikon

Ikon menggunakan teknik:

```txt
CSS Mask
```

Jika menambah ikon baru:

1. Tambahkan file ikon ke folder `/icons`
2. Daftarkan class ikonnya di `style.css`

---

# Peringatan Penting

```txt
JANGAN PERNAH mulai bekerja tanpa menjalankan:
git pull origin main
```

Karena dapat menyebabkan konflik dan kehilangan perubahan anggota tim lain.
