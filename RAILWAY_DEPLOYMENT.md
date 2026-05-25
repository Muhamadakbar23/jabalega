# 🚀 Panduan Deploy Jabalega Admin ke Railway

Aplikasi Anda sudah siap untuk di-deploy ke Railway! Ikuti langkah-langkah di bawah.

---

## 📋 Prasyarat

1. **Akun Railway** → Daftar gratis di https://railway.app
2. **Git** → Sudah terinstall di komputer Anda
3. **GitHub Account** → Untuk menyimpan kode (opsional, bisa pakai GitLab atau Gitea)

---

## ✅ Langkah 1: Siapkan GitHub Repository

### A. Inisialisasi Git (Jika belum)
```bash
cd d:\jabalega\jabalega-admin
git init
git add .
git commit -m "Initial commit - Jabalega Admin Panel"
```

### B. Buat Repository di GitHub
1. Buka https://github.com/new
2. Nama repo: `jabalega-admin`
3. Jangan initialize dengan README (karena sudah ada)
4. Klik **Create Repository**

### C. Push ke GitHub
```bash
git remote add origin https://github.com/YOUR_USERNAME/jabalega-admin.git
git branch -M main
git push -u origin main
```

> 💡 Ganti `YOUR_USERNAME` dengan username GitHub Anda

---

## 🏗️ Langkah 2: Setup Database di Railway

### A. Login ke Railway
1. Buka https://railway.app
2. Klik **Start New Project**
3. Pilih **Provision PostgreSQL** atau **Provision MySQL**
   - **Rekomendasi: MySQL 8.0** (sama seperti di localhost)

### B. Ambil Credentials
Setelah database dibuat:
1. Klik database → Tab **Connect**
2. Salin kredensial untuk environment variables:
   - `DB_HOST` (contoh: `roundhouse.proxy.rlway.app`)
   - `DB_PORT` (contoh: `12345`)
   - `DB_USER` (contoh: `root`)
   - `DB_PASS` (password yang di-generate)
   - `DB_NAME` (nama database)

---

## 🔧 Langkah 3: Deploy PHP App ke Railway

### A. Buat Dockerfile (Opsional tapi Direkomendasikan)

**Buat file `Dockerfile` di root project:**

```dockerfile
FROM php:8.2-apache

# Install MySQL extension
RUN docker-php-ext-install mysqli

# Enable Apache rewrite module
RUN a2enmod rewrite

# Set working directory
WORKDIR /var/www/html

# Copy aplikasi
COPY . .

# Set permissions
RUN chown -R www-data:www-data /var/www/html

# Expose port
EXPOSE 80

# Start Apache
CMD ["apache2-foreground"]
```

**Atau jika tanpa Apache, pakai built-in server:**

Buat file `Procfile` di root project:
```
web: php -S 0.0.0.0:$PORT
```

### B. Deploy via Railway CLI

1. **Install Railway CLI** (https://docs.railway.app/guides/cli)
   ```bash
   npm install -g @railway/cli
   ```

2. **Login ke Railway**
   ```bash
   railway login
   ```

3. **Deploy aplikasi**
   ```bash
   cd d:\jabalega\jabalega-admin
   railway up
   ```

### C. Atau Deploy via GitHub (Lebih Mudah)

1. Di Railway Dashboard, klik **+ New Project**
2. Pilih **Deploy from GitHub**
3. Sambungkan akun GitHub Anda
4. Pilih repository `jabalega-admin`
5. Railway otomatis akan deploy

---

## 🌍 Langkah 4: Set Environment Variables

Di Railway Dashboard:

1. Klik **Project Settings** → **Variables**
2. Tambahkan semua ini:

```
DB_HOST=<dari step 2B>
DB_PORT=<dari step 2B>
DB_USER=<dari step 2B>
DB_PASS=<dari step 2B>
DB_NAME=<dari step 2B>
ENVIRONMENT=PRODUCTION
```

---

## 🗄️ Langkah 5: Import Database Schema

Setelah deployment berhasil:

1. **Dapatkan MySQL Tools** (MySQL Workbench, DBeaver, atau phpMyAdmin)
2. **Connect ke Railway MySQL** menggunakan credentials dari step 2B
3. **Import file `jabalega.sql`:**
   - Buka file jabalega.sql di text editor
   - Copy semua SQL
   - Paste ke Database Manager → Run

Atau via command line (jika MySQL client terinstall):
```bash
mysql -h <DB_HOST> -u <DB_USER> -p<DB_PASS> -P <DB_PORT> <DB_NAME> < jabalega.sql
```

---

## 🎯 Langkah 6: Testing

1. **Dapatkan URL dari Railway:**
   - Di Dashboard → Klik aplikasi Anda
   - Tab **Deployments** → Copy **Railway Domain**
   - Contoh: `jabalega-admin-production.up.railway.app`

2. **Buka di Browser:**
   ```
   https://jabalega-admin-production.up.railway.app
   ```

3. **Login dengan credentials default:**
   - Username: `admin`
   - Password: `admin` (dari jabalega.sql)

---

## 🔐 Langkah 7: Update Password Admin

Setelah login pertama kali, **langsung ubah password**:

1. Cari halaman profile/settings
2. Update password dengan sesuatu yang aman
3. Jalankan file `fix_passwords.php` jika diperlukan untuk update hash password

---

## 🛠️ Troubleshooting

### ❌ "Database connection failed"

**Solusi:**
1. Pastikan credentials di Railway Variables sudah benar
2. Check firewall - Railway MySQL perlu whitelist:
   - Buka Railway → Database → Settings
   - Pastikan "Public Networking" aktif
3. Test koneksi manual via MySQL Workbench

### ❌ "502 Bad Gateway"

**Solusi:**
1. Lihat logs di Railway → Deployments → View Logs
2. Pastikan PHP sudah terinstall dengan ekstensi `mysqli`
3. Check file permissions

### ❌ Blank Page / White Screen

**Solusi:**
1. Enable error logging di `config.php`
2. Akses `/debug.php` untuk melihat diagnostic
3. Check Rails logs untuk PHP errors

---

## 📊 Custom Domain (Opsional)

Untuk menggunakan domain sendiri:

1. Railway → Project Settings → **Domain**
2. Klik **+ Add Domain**
3. Masukkan domain Anda (contoh: `admin.jabalega.com`)
4. Ikuti instruksi DNS pointing
5. Tunggu SSL certificate tergenerate (~5 menit)

---

## 💰 Pricing

Railway menggunakan **pay-as-you-go pricing**:
- **MySQL Database:** ~$0.40/hari
- **PHP App:** ~$0.50/hari (tergantung traffic)
- **Free tier:** $5/bulan gratis

Jabalega Admin Panel biasanya akan kena biaya ~$20-30/bulan.

---

## 📚 Resources Tambahan

- Railway Docs: https://docs.railway.app
- PHP on Railway: https://docs.railway.app/databases/mysql
- MySQL Connection Strings: https://docs.railway.app/plugins/mysql

---

## ✨ Sudah Selesai!

Aplikasi Anda sekarang live di Railway dengan:
- ✅ Auto-scaling
- ✅ SSL Certificate (gratis)
- ✅ Database backup otomatis
- ✅ Monitoring & Logs
- ✅ Git integration untuk auto-deploy

Setiap push ke GitHub akan otomatis di-deploy ke Railway! 🚀

---

**Butuh bantuan?** Buat issue di GitHub atau hubungi Railway support.
