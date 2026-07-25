# S-AES Lab - Encrypted Simulator

Simulasi interaktif **Simplified Advanced Encryption Standard** (S-AES) 16-bit dengan pembelajaran step-by-step untuk setiap transformasi kriptografi.

## ✨ Fitur

- 🔐 **Enkripsi & Dekripsi**: Simulasi lengkap algoritma S-AES
- 📊 **Visualisasi State Matrix**: Tampilan grafis transformasi data
- 🔍 **Step-by-Step Calculation**: Lihat setiap langkah perhitungan
- 🎨 **Dark Mode & Light Mode**: Tema gelap dan terang yang dapat dipilih
- 📱 **Responsive Design**: Bekerja sempurna di desktop, tablet, dan mobile
- 🚀 **Teroptimasi Vercel**: Deploy dengan mudah di Vercel

## 🚀 Quick Start

### Local Development

```bash
# Install dependencies
npm install

# Run tests
npm test

# Buka index.html di browser
# Atau gunakan live server
npx serve
```

### Deploy ke Vercel

#### Opsi 1: Via Vercel CLI
```bash
npm install -g vercel
vercel
```

#### Opsi 2: Via GitHub Integration
1. Push repository ke GitHub
2. Kunjungi [Vercel Dashboard](https://vercel.com/dashboard)
3. Klik "Add New Project"
4. Pilih repository ini
5. Klik "Deploy"

## 🌓 Dark Mode / Light Mode

Klik tombol 🌙/☀️ di header untuk mengganti tema. Preferensi Anda disimpan di localStorage.

## 📁 Struktur Project

```
.
├── index.html           # Entry point aplikasi
├── favicon.svg          # Icon aplikasi
├── package.json         # Konfigurasi npm & script
├── vercel.json          # Konfigurasi Vercel
├── src/
│   ├── main.js          # Logic aplikasi & theme toggle
│   ├── styles/
│   │   └── main.css     # Styling dengan dark mode support
│   └── crypto/
│       ├── constants.js # S-Box, Inverse S-Box
│       ├── encrypt.js   # Enkripsi S-AES
│       ├── decrypt.js   # Dekripsi S-AES
│       ├── keyExpansion.js
│       ├── subNibble.js
│       ├── shiftRows.js
│       ├── mixColumns.js
│       ├── gf.js        # GF(2⁴) arithmetic
│       └── logger.js    # Utility functions
└── test/
    └── saes.test.js     # Unit tests
```

## 🔧 Konfigurasi Vercel

File `vercel.json` sudah dikonfigurasi dengan:
- Output directory: `dist/`
- Build command: `npm run build`
- SPA routing (rewrite semua request ke `index.html`)
- Cache headers untuk performa optimal

## 🎓 Tentang S-AES

- **Block Size**: 16 bit
- **Key Size**: 16 bit
- **Rounds**: 2
- **Polynomial**: x⁴ + x + 1 (0x13)
- **Standar**: Educational version of AES

## 📝 License

Educational Project - Semester 6 Ethical Hacking

## 👨‍💻 Development

Dibuat tanpa library kriptografi eksternal untuk pembelajaran mendalam tentang mekanisme AES.

---

**Tombol Toggle Theme**: Klik 🌙 atau ☀️ di header untuk beralih antara dark mode dan light mode!
