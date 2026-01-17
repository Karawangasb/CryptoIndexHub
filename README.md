# CryptoIndexHub – Database & Admin Panel (PHP + MySQL)

Dokumen ini melanjutkan desain **CryptoIndexHub** (website listing coin/token dengan voting publik), fokus pada **struktur database** dan **admin panel**. Disusun agar mudah diimplementasikan dengan PHP (PDO) + MySQL.

---

## 1. Struktur Database (MySQL)

### 1.1 Tabel `users`
Digunakan untuk admin dan (opsional) user terdaftar.

```sql
CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(50) UNIQUE NOT NULL,
  email VARCHAR(100) UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  role ENUM('admin','moderator','user') DEFAULT 'user',
  status ENUM('active','banned') DEFAULT 'active',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

---

### 1.2 Tabel `coins`
Data utama listing coin/token.

```sql
CREATE TABLE coins (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  symbol VARCHAR(20) NOT NULL,
  slug VARCHAR(120) UNIQUE NOT NULL,
  network VARCHAR(100),
  contract_address VARCHAR(255),
  website VARCHAR(255),
  explorer VARCHAR(255),
  whitepaper VARCHAR(255),
  github VARCHAR(255),
  twitter VARCHAR(255),
  telegram VARCHAR(255),
  description TEXT,
  logo VARCHAR(255),
  status ENUM('pending','approved','rejected') DEFAULT 'pending',
  is_featured TINYINT(1) DEFAULT 0,
  total_votes INT DEFAULT 0,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

---

### 1.3 Tabel `votes`
Voting publik (1 IP = 1 vote / 24 jam).

```sql
CREATE TABLE votes (
  id INT AUTO_INCREMENT PRIMARY KEY,
  coin_id INT NOT NULL,
  ip_address VARCHAR(45) NOT NULL,
  user_id INT NULL,
  vote_date DATE NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

  UNIQUE KEY uniq_vote (coin_id, ip_address, vote_date),
  FOREIGN KEY (coin_id) REFERENCES coins(id) ON DELETE CASCADE
);
```

---

### 1.4 Tabel `submissions`
Form submit coin dari publik.

```sql
CREATE TABLE submissions (
  id INT AUTO_INCREMENT PRIMARY KEY,
  coin_name VARCHAR(100),
  symbol VARCHAR(20),
  network VARCHAR(100),
  contract_address VARCHAR(255),
  website VARCHAR(255),
  contact_email VARCHAR(100),
  note TEXT,
  status ENUM('pending','approved','rejected') DEFAULT 'pending',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

---

### 1.5 Tabel `reports`
Laporan spam / scam dari user.

```sql
CREATE TABLE reports (
  id INT AUTO_INCREMENT PRIMARY KEY,
  coin_id INT,
  reason VARCHAR(255),
  ip_address VARCHAR(45),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

---

## 2. Relasi Singkat

- **coins ↔ votes** → 1 : N
- **coins ↔ reports** → 1 : N
- **submissions → coins** (manual approval admin)

---

## 3. Admin Panel – Struktur Menu

### 3.1 Authentication
- Login admin (session-based)
- Password hash `password_hash()`
- Middleware `is_admin()`

---

### 3.2 Dashboard
Menampilkan:
- Total coins
- Pending submissions
- Total votes (24 jam / all time)
- Top 10 coins by vote

---

### 3.3 Manage Coins
Fitur:
- List coin (search + pagination)
- Approve / Reject
- Edit detail coin
- Upload logo
- Set **Featured Coin**
- Reset votes (opsional)

---

### 3.4 Submissions
- Lihat submit publik
- Approve → insert ke `coins`
- Reject + catatan

---

### 3.5 Voting Monitor
- Lihat vote per coin
- Deteksi IP spam
- Manual delete vote

---

### 3.6 Reports / Abuse
- Lihat laporan scam
- Tandai coin (warning / delist)

---

### 3.7 User Management (Opsional)
- Tambah admin / moderator
- Ban user

---

## 4. Struktur Folder (Sederhana)

```text
/cryptohub
 ├── admin/
 │   ├── login.php
 │   ├── dashboard.php
 │   ├── coins.php
 │   ├── submissions.php
 │   ├── votes.php
 │   └── reports.php
 ├── api/
 │   ├── vote.php
 │   └── submit.php
 ├── assets/
 ├── config/
 │   └── db.php
 ├── index.php
 └── coin.php
```

---

## 5. Security Minimal Wajib

- Prepared Statements (PDO)
- CSRF token (form submit & vote)
- Rate limit voting (IP + cookie)
- Validasi contract address
- reCAPTCHA (submit + vote)

---

## 6. Tahap Selanjutnya (Rekomendasi)

1. API public (`/api/coins`, `/api/top-voted`)
2. Sorting: newest, top today, top all-time
3. SEO (slug, schema.org)
4. Monetisasi: promoted listing

---
