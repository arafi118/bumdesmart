# Buku Panduan Pengguna (User Guide) BUMDesmart

Selamat datang di panduan pengguna BUMDesmart! BUMDesmart adalah sistem Point of Sale (POS) terpadu yang dirancang khusus untuk memenuhi kebutuhan bisnis BUMDes, dilengkapi dengan fitur akuntansi akrual, manajemen inventori berbasis FIFO, dan pelaporan keuangan otomatis.

Panduan ini akan membantu Anda memahami alur kerja dan fitur-fitur utama di dalam aplikasi.

---

## 1. Dashboard
Halaman pertama yang Anda lihat saat login. Menampilkan ringkasan performa bisnis Anda secara real-time, meliputi:
*   **Total Pendapatan & Laba:** Angka penjualan dan keuntungan kotor.
*   **Grafik Penjualan:** Tren penjualan dari waktu ke waktu.
*   **Pengingat Stok:** Notifikasi untuk barang yang stoknya hampir habis (Limit Stok).
*   **Status Hutang & Piutang:** Ringkasan jumlah hutang ke supplier dan piutang pelanggan yang belum tertagih.

---

## 2. Master Data
Menu ini digunakan untuk mengatur seluruh data dasar yang dibutuhkan sebelum melakukan transaksi.

### A. Produk & Inventori Dasar
*   **Produk:** Tempat Anda mendaftarkan barang jualan. Anda bisa mengatur Harga Beli, Harga Jual, Stok Minimal, serta mencetak Label Barcode.
*   **Kategori, Merek, Rak, Satuan:** Gunakan fitur ini untuk mengelompokkan produk agar mudah dicari saat transaksi.

### B. Entitas Bisnis
*   **Supplier:** Daftar pemasok barang (kulakan). Dibutuhkan saat mencatat Pembelian.
*   **Pelanggan / Member:** Daftar pembeli. Membantu melacak riwayat belanja dan Piutang pelanggan.

### C. Pengaturan Akses
*   **Pengguna (User):** Menambahkan akun karyawan (Kasir, Admin, dsb).
*   **Role & Hak Akses:** Menentukan menu apa saja yang boleh diakses oleh karyawan tertentu.

---

## 3. Manajemen Inventori (Gudang)
BUMDesmart menggunakan metode **FIFO (First In First Out)**. Barang yang dibeli lebih dulu akan dikeluarkan lebih dulu oleh sistem untuk menghitung Harga Pokok Penjualan (HPP) yang presisi.

*   **Stok Opname:** Digunakan untuk mencocokkan stok fisik di toko dengan stok di sistem. Jika ada selisih, sistem akan melakukan penyesuaian otomatis.
*   **Penyesuaian Stok (Stock Adjustment):** Digunakan untuk mencatat barang rusak, kadaluarsa, atau hilang agar nilai persediaan di neraca tetap akurat.

---

## 4. Pembelian (Membeli Barang untuk Dijual)
Alur untuk mendata masuknya barang ke toko Anda.

*   **Purchase Order (PO):** Membuat dokumen pesanan ke supplier. Anda juga bisa *Import PO dari CSV* jika memesan dalam jumlah banyak.
*   **Tambah Pembelian:** Mencatat barang masuk. 
    *   *Pembelian Tunai:* Kas akan langsung berkurang dan Stok bertambah.
    *   *Pembelian Tempo (Hutang):* Stok bertambah, dan sistem akan mencatatnya sebagai Hutang.
*   **Daftar Pembelian:** Melihat riwayat belanja. Di sini Anda bisa menekan tombol **Bayar** untuk mencicil atau melunasi Hutang ke supplier.
*   **Retur Pembelian:** Mengembalikan barang rusak ke supplier (akan mengurangi stok dan memotong hutang atau menambah kas).

---

## 5. Penjualan (Menjual Barang ke Pelanggan)
Aplikasi menyediakan dua cara untuk mencatat penjualan:

### A. POS / Kasir (Transaksi Cepat)
*   Didesain untuk kasir minimarket/toko retail.
*   Mendukung pencarian menggunakan Barcode Scanner.
*   Bisa menahan struk sementara (Hold Bill) jika pelanggan masih mengambil barang.
*   Cetak struk menggunakan printer thermal (58mm/80mm).

### B. Tambah Penjualan (Grosir / Invoice)
*   Didesain untuk transaksi grosir atau proyek yang membutuhkan Invoice.
*   Mendukung **Pembayaran Sebagian (Termin / Tempo)**. Sistem otomatis mencatat uang yang dibayar ke **Kas**, dan sisa kekurangannya ke **Piutang**.

### C. Daftar Penjualan & Pelunasan
*   Semua riwayat penjualan terekam di sini.
*   Untuk pelanggan yang berhutang (Piutang), Anda bisa menekan tombol **Bayar** di halaman ini ketika mereka melunasi hutangnya. Sistem otomatis mengurangi Piutang tanpa perlu jurnal manual.

### D. Retur Penjualan
*   Jika pelanggan mengembalikan barang. Akan menambah stok kembali dan mengurangi Piutang/mengeluarkan Kas untuk refund.

---

## 6. Keuangan & Pelaporan
Keunggulan utama BUMDesmart adalah *Double-Entry Accounting* otomatis. Setiap Anda melakukan transaksi jual/beli, sistem menyusun laporan keuangan untuk Anda.

*   **Laporan Penjualan Harian:** Menampilkan rekap penjualan hari ini, dipisah berdasarkan metode pembayaran (Cash, Transfer, QRIS) dan jumlah Piutang yang belum dibayar. Sangat cocok untuk *tutup kasir*.
*   **Daftar Transaksi & Jurnal Umum:** Melihat pergerakan setiap akun akuntansi secara detail (Kas, Persediaan, Hutang, Piutang) beserta mutasi debit/kreditnya.
*   **Laba / Rugi (Profit & Loss):** Melihat keuntungan bersih bisnis setelah dikurangi HPP, diskon, dan beban operasional lainnya. Laba dihitung secara *Akrual* (mencatat hak pendapatan yang sudah terjadi meskipun uang belum diterima sepenuhnya).
*   **Bagan Akun (Chart of Accounts / COA):** Pengaturan kode rekening akuntansi bawaan sistem.

---

## 7. Cetak Dokumen & Pengaturan
Sistem ini mendukung berbagai macam format cetakan dokumen transaksi.
*   **Surat Jalan:** Dicetak dari halaman Daftar Penjualan untuk diberikan ke kurir pengiriman (tanpa menampilkan harga).
*   **Nota Pembelian/Penjualan:** Dokumen ukuran A4/A5 untuk transaksi grosir.
*   **Struk Kasir:** Cetakan kecil untuk printer thermal (58mm atau 80mm).
*   **Pengaturan Toko:** Terdapat di menu *Pengaturan* untuk mengubah Nama Toko, Logo, Alamat, Pesan di bawah struk kasir, serta konfigurasi akun Bank (Transfer/QRIS).

---

## Tips Penggunaan
1. **Pentingnya Data Akurat:** Selalu pastikan Harga Beli dan Jumlah Barang diinput dengan benar saat *Tambah Pembelian*, karena sistem FIFO bergantung pada riwayat pembelian ini untuk menentukan Keuntungan (Laba Kotor) harian Anda.
2. **Manajemen Piutang:** Jangan mencatat penjualan cicilan sebagai 2 nota berbeda. Cukup gunakan **Tambah Penjualan**, isi jumlah yang dibayar, dan biarkan sistem mencatat sisanya sebagai Piutang. Lunasi melalui menu **Daftar Penjualan**.
