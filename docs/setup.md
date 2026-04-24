# Panduan Setup Perangkat POS (Point of Sale)

Dokumen ini berisi panduan untuk melakukan konfigurasi hardware (Scanner dan Printer) agar berjalan optimal dengan aplikasi BUMDESMART.

---

## 1. Konfigurasi Barcode Scanner

Sistem mendukung dua jenis pemindaian:
1.  **Scanner Kamera:** Menggunakan kamera laptop/HP (khusus di halaman POS).
2.  **Scanner Hardware (USB/Bluetooth):** Menggunakan alat scanner fisik.

### Setup Scanner Hardware:
*   **Mode Keyboard Wedge:** Pastikan scanner Anda diatur dalam mode "Keyboard Wedge" (default untuk sebagian besar scanner).
*   **Sinyal Enter (Suffix):** Pastikan scanner dikonfigurasi untuk mengirim sinyal `ENTER` (Line Feed/Carriage Return) setelah setiap pemindaian. Ini diperlukan agar sistem otomatis memproses produk yang ditemukan.
*   **Cara Penggunaan:**
    *   Di halaman **Sale POS**, tekan tombol `F1` pada keyboard untuk memfokuskan kursor ke kolom pencarian sebelum melakukan pemindaian.
    *   Di halaman **Tambah Penjualan**, klik atau arahkan kursor ke kolom "Cari Produk" sebelum melakukan pemindaian.

---

## 2. Konfigurasi Printer Thermal (Struk)

Aplikasi menggunakan pencetakan berbasis web (`window.print()`). Agar tinggi kertas mengikuti jumlah item (variable height), ikuti langkah berikut:

### Pengaturan Browser (Google Chrome / Microsoft Edge):
Saat jendela print muncul, lakukan pengaturan berikut (hanya sekali, browser akan mengingatnya):
1.  **Destination:** Pilih printer thermal Anda.
2.  **Margins:** Ubah menjadi **None** (Sangat Penting).
3.  **Headers and Footers:** Pastikan **Dihilangkan centangnya** (Uncheck).
4.  **Scale:** Pilih **Default** atau **100%**.

### Pengaturan Driver Printer (Windows):
Jika printer memotong kertas terlalu cepat atau ada jeda kosong yang panjang:
1.  Buka **Control Panel** > **Devices and Printers**.
2.  Klik kanan printer thermal Anda > **Printer Properties**.
3.  Pilih tab **Advanced** atau **Preferences**.
4.  Cari pengaturan **Paper Size** atau **Form Name**.
5.  Pilih opsi yang mendukung gulungan terus menerus, biasanya bernama **Roll Paper** atau **Continuous**. Hindari memilih ukuran tetap seperti *Short Slip*.
6.  Pada menu **Paper Cut**, pilih **Partial Cut** atau **Full Cut** di akhir dokumen (*End of Doc*).

### Menyesuaikan Ukuran Kertas (58mm vs 80mm):
Secara default, aplikasi disetel untuk kertas **58mm**. Jika Anda menggunakan printer **80mm**, Anda perlu mengubah CSS di file berikut:
*   `resources/views/livewire/penjualan/cetak-struk.blade.php`
*   `resources/views/livewire/penjualan/cetak-struk-kasir.blade.php`

**Bagian yang perlu diubah:**
```css
/* Ganti 58mm menjadi 80mm pada bagian berikut */
@page { size: 80mm auto; }
body { width: 80mm; }
```

---

## 3. Konfigurasi Printer Label & Laser (Barcode Produk)

Untuk mencetak label menggunakan printer Laser (kertas stiker lembaran) atau printer khusus label:

1.  Buka menu **Master Produk** > **Produk** > **Cetak Label**.
2.  Pilih produk dan tentukan jumlah label yang ingin dicetak.
3.  Pilih format kertas (Contoh: TJ 103, TJ 108, atau A4).
4.  **Penting untuk Printer Laser (Kertas Stiker):**
    *   **Scale:** Harus setel ke **100%** (Bukan "Fit to Page"). Jika tidak 100%, posisi barcode tidak akan pas di tengah kotak stiker.
    *   **Margins:** Setel ke **None**.
    *   **Paper Size:** 
        *   Jika menggunakan kertas stiker A4 penuh: Pilih **A4**.
        *   Jika menggunakan kertas label kecil (TJ/Kojiko): Pilih **User Defined / Custom Size** pada driver printer dan masukkan ukuran lembar label tersebut (Contoh TJ 103/121 biasanya memiliki lebar sekitar 135mm - 155mm).
    *   **Kesesuaian Baris & Kolom:** Sistem sudah diatur mengikuti standar jumlah kolom dan baris pada label T&J (Contoh: No. 107 memiliki 3 kolom, No. 121 memiliki 2 kolom).

5.  **Tips Kalibrasi Presisi:**
    *   Cetaklah 1 lembar percobaan menggunakan kertas HVS biasa terlebih dahulu.
    *   Tumpuk hasil cetakan HVS di atas kertas label kuning asli, lalu terawang di bawah cahaya.
    *   Jika posisi sudah pas di tengah kotak, silakan cetak di kertas label asli.
    *   Jika posisi agak meleset, pastikan pengaturan **Scale** benar-benar 100% dan **Margins** adalah None.

---

## 4. Troubleshooting
*   **Scanner tidak masuk ke keranjang:** Pastikan kursor sudah berada di kolom pencarian. Jika sudah tapi tidak masuk, cek apakah scanner mengirim sinyal "Enter" di akhir kode.
*   **Struk terpotong di tengah:** Pastikan pengaturan Margin di browser adalah **None**.
*   **Teks struk terlalu kecil/besar:** Sesuaikan pengaturan **Scale** pada jendela print browser.
