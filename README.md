# Penjelasan Lengkap Database Aplikasi Toko Minimarket

## 📋 DAFTAR ISI

1. [Tabel Master Data](#master-data)
2. [Tabel Transaksi Penjualan](#transaksi-penjualan)
3. [Tabel Transaksi Pembelian](#transaksi-pembelian)
4. [Tabel Pembayaran](#pembayaran)
5. [Tabel Inventory & Stok](#inventory-stok)
6. [Tabel Costing (FIFO/Average)](#costing)
7. [Tabel Pengaturan](#pengaturan)

---

## <a id="master-data"></a>📊 1. TABEL MASTER DATA

### **USERS**

**Fungsi:** Menyimpan data pengguna/karyawan sistem

**Field Penting:**

-   `user_id` (PK): ID unik pengguna
-   `username`: Untuk login
-   `password`: Password terenkripsi
-   `full_name`: Nama lengkap
-   `role`: Hak akses ('admin', 'kasir', 'gudang', 'manager')
-   `is_active`: Status aktif/nonaktif

**Kegunaan:**

-   Login dan autentikasi sistem
-   Tracking siapa yang melakukan transaksi
-   Pembagian hak akses berdasarkan role
-   Audit trail untuk setiap aktivitas

**Contoh Data:**

```
1 | admin001 | ****** | Budi Santoso | admin | true
2 | kasir01  | ****** | Siti Aminah | kasir | true
3 | gudang01 | ****** | Ahmad Yani  | gudang | true
```

---

### **CUSTOMER_GROUPS**

**Fungsi:** Mengelompokkan pelanggan berdasarkan kategori/tier

**Field Penting:**

-   `group_id` (PK): ID unik grup
-   `group_name`: Nama grup (Member Biasa, Silver, Gold, Platinum)
-   `description`: Deskripsi grup
-   `discount_percentage`: Diskon otomatis untuk grup ini

**Kegunaan:**

-   Membedakan tier pelanggan (reguler, member, VIP)
-   Diskon otomatis per group (member gold = diskon 10%)
-   Harga khusus untuk grup tertentu (grosir, reseller)
-   Program loyalty dan membership

**Contoh Data:**

```
1 | Pelanggan Umum | Pembeli tanpa member        | 0%
2 | Member Silver  | Member dengan belanja >1jt  | 5%
3 | Member Gold    | Member dengan belanja >5jt  | 10%
4 | Reseller       | Pembeli grosir              | 15%
```

---

### **CUSTOMERS**

**Fungsi:** Menyimpan data pelanggan/pembeli

**Field Penting:**

-   `customer_id` (PK): ID unik pelanggan
-   `customer_code`: Kode pelanggan (mis: CUST-001)
-   `customer_name`: Nama pelanggan
-   `phone`: Nomor telepon
-   `address`: Alamat
-   `group_id` (FK): Grup pelanggan
-   `credit_limit`: Batas kredit/hutang

**Kegunaan:**

-   Identifikasi pembeli untuk transaksi kredit/tempo
-   Tracking hutang pelanggan
-   Penerapan harga khusus sesuai group
-   Database untuk program member/loyalty
-   Riwayat pembelian pelanggan
-   Reminder jatuh tempo pembayaran

**Contoh Data:**

```
1 | CUST-001 | Toko Maju Jaya   | 081234567890 | Jl. Sudirman 10 | 4 | 50000000
2 | CUST-002 | Ibu Ratna        | 081298765432 | Jl. Gatsu 25    | 3 | 5000000
3 | WALK-IN  | Pelanggan Umum   | -            | -               | 1 | 0
```

---

### **SUPPLIERS**

**Fungsi:** Menyimpan data pemasok/supplier barang

**Field Penting:**

-   `supplier_id` (PK): ID unik supplier
-   `supplier_code`: Kode supplier (SUP-001)
-   `supplier_name`: Nama supplier
-   `phone`: Nomor telepon
-   `address`: Alamat
-   `email`: Email kontak

**Kegunaan:**

-   Identifikasi sumber barang
-   Kontak untuk pemesanan ulang
-   Tracking hutang ke supplier
-   Evaluasi performa supplier
-   Negosiasi harga dan termin pembayaran

**Contoh Data:**

```
1 | SUP-001 | PT Indofood Sukses Makmur | 021-5555-1234 | Jakarta | sales@indofood.com
2 | SUP-002 | CV Berkah Jaya            | 031-7777-5678 | Surabaya | info@berkahjaya.com
3 | SUP-003 | UD Sumber Rejeki          | 0274-888-9999 | Yogya    | sumberrejeki@mail.com
```

---

### **CATEGORIES**

**Fungsi:** Mengelompokkan produk berdasarkan kategori

**Field Penting:**

-   `category_id` (PK): ID unik kategori
-   `category_name`: Nama kategori
-   `description`: Deskripsi kategori

**Kegunaan:**

-   Klasifikasi produk (Makanan, Minuman, ATK, Elektronik, dll)
-   Memudahkan pencarian produk
-   Laporan penjualan per kategori
-   Pengaturan layout/rak toko
-   Analisis kategori terlaris

**Contoh Data:**

```
1 | Makanan & Snack    | Makanan ringan, biskuit, keripik
2 | Minuman            | Air mineral, soft drink, juice
3 | Alat Tulis Kantor  | Pulpen, buku, kertas
4 | Peralatan Rumah    | Sabun, detergen, pengharum
5 | Elektronik         | Baterai, charger, kabel
```

---

### **PRODUCTS**

**Fungsi:** Menyimpan data produk/barang dagangan (Master Data Produk)

**Field Penting:**

-   `product_id` (PK): ID unik produk
-   `product_code`: Kode produk internal (PRD-001)
-   `barcode`: Barcode untuk scanning kasir
-   `product_name`: Nama produk
-   `category_id` (FK): Kategori produk
-   `unit`: Satuan (pcs, box, lusin, kg)
-   `base_price`: Harga dasar (untuk referensi)
-   `selling_price`: Harga jual standar
-   `min_stock`: Stok minimum (alert reorder)
-   `current_stock`: Stok saat ini
-   `costing_method`: Metode HPP ('FIFO', 'AVERAGE', 'SYSTEM')
-   `average_cost`: HPP rata-rata (untuk metode Average)
-   `is_active`: Status produk aktif/nonaktif

**Kegunaan:**

-   Master data barang yang dijual
-   Harga jual dan HPP dasar
-   **Tracking stok real-time** (current_stock)
-   **Alert stok minimum** untuk reorder
-   Barcode untuk scanning kasir
-   Status aktif/nonaktif produk
-   **Pengaturan metode costing per produk**

**Contoh Data:**

```
1 | PRD-001 | 8992761111111 | Indomie Goreng         | 1 | pcs | 2500 | 3000 | 50  | 250 | FIFO    | 2600 | true
2 | PRD-002 | 8992761222222 | Aqua 600ml             | 2 | pcs | 2800 | 3500 | 100 | 450 | AVERAGE | 2900 | true
3 | PRD-003 | 8992761333333 | Pulpen Standard AE-7   | 3 | pcs | 1500 | 2000 | 20  | 75  | AVERAGE | 1600 | true
```

---

### **PRODUCT_PRICES**

**Fungsi:** Menyimpan harga khusus produk untuk grup pelanggan tertentu

**Field Penting:**

-   `price_id` (PK): ID unik harga khusus
-   `product_id` (FK): Produk yang diberi harga khusus
-   `group_id` (FK): Grup pelanggan yang dapat harga khusus
-   `special_price`: Harga khusus
-   `valid_from`: Tanggal mulai berlaku
-   `valid_until`: Tanggal berakhir (NULL = selamanya)

**Kegunaan:**

-   Harga member berbeda dengan harga umum
-   Harga grosir vs eceran
-   Harga promosi dengan periode tertentu
-   Diskon khusus untuk pelanggan setia
-   Program bundling atau paket

**Contoh Data:**

```
1 | 1 (Indomie)  | 3 (Gold)     | 2700 | 2024-01-01 | NULL       → Member Gold: Rp 2.700
2 | 1 (Indomie)  | 4 (Reseller) | 2500 | 2024-01-01 | NULL       → Reseller: Rp 2.500
3 | 2 (Aqua)     | 3 (Gold)     | 3200 | 2024-12-01 | 2024-12-31 → Promo Desember
```

**Logika Harga:**

```
Jika customer group = Gold DAN product = Indomie
  Harga = 2700 (bukan 3000)
Jika customer group = Reseller DAN product = Indomie
  Harga = 2500
Jika tidak ada harga khusus
  Harga = selling_price dari tabel PRODUCTS
```

---

## <a id="transaksi-penjualan"></a>💰 2. TABEL TRANSAKSI PENJUALAN

### **SALES**

**Fungsi:** Menyimpan header/data utama transaksi penjualan (Nota/Struk)

**Field Penting:**

-   `sale_id` (PK): ID unik transaksi
-   `invoice_number`: Nomor nota (INV-2024-001)
-   `sale_date`: Tanggal & waktu transaksi
-   `customer_id` (FK): Pelanggan yang membeli
-   `user_id` (FK): Kasir yang melayani
-   `payment_type`: Jenis pembayaran ('cash', 'credit', 'installment', 'preorder')
-   `subtotal`: Total sebelum diskon
-   `discount_percentage`: Diskon persen per nota
-   `discount_amount`: Diskon nominal per nota
-   `cashback_amount`: Cashback per nota
-   `tax_amount`: Pajak (PPN)
-   `total_amount`: Total yang harus dibayar
-   `paid_amount`: Jumlah yang dibayar
-   `change_amount`: Kembalian
-   `debt_amount`: Sisa hutang (jika kredit/tempo)
-   `status`: Status pembayaran ('pending', 'partial', 'paid', 'completed')
-   `notes`: Catatan tambahan

**Kegunaan:**

-   Nota/struk penjualan
-   Total pembayaran dan kembalian
-   Diskon & cashback per nota
-   Status pembayaran (tunai/kredit/tempo)
-   Tracking hutang pelanggan
-   Informasi tanggal & kasir
-   Basis laporan penjualan harian

**Contoh Data:**

```
SALE_ID: 1
Invoice: INV-2024-12-001
Date: 2024-12-29 14:30:00
Customer: Ibu Ratna (CUST-002)
Kasir: Siti Aminah (kasir01)
Payment: credit (tempo 30 hari)
Subtotal: Rp 500.000
Diskon Nota (10%): Rp 50.000
Cashback: Rp 10.000
Tax (11%): Rp 49.500
Total: Rp 489.500
Paid: Rp 100.000 (DP)
Debt: Rp 389.500
Status: partial
```

---

### **SALES_DETAILS**

**Fungsi:** Menyimpan detail produk yang dibeli dalam satu nota

**Field Penting:**

-   `sale_detail_id` (PK): ID unik detail
-   `sale_id` (FK): Nota penjualan
-   `product_id` (FK): Produk yang dibeli
-   `quantity`: Jumlah yang dibeli
-   `unit_price`: Harga satuan saat transaksi
-   `discount_percentage`: Diskon persen per item
-   `discount_amount`: Diskon nominal per item
-   `cashback_amount`: Cashback per item
-   `subtotal`: Total harga item (qty × price - diskon)
-   `hpp`: Harga Pokok Penjualan per item
-   `profit`: Laba per item (subtotal - (hpp × qty))

**Kegunaan:**

-   Daftar barang yang dibeli pelanggan
-   Quantity dan harga per item
-   Diskon & cashback per produk
-   **Perhitungan HPP per item** (untuk analisa margin)
-   **Perhitungan profit per item** (laba = harga jual - HPP)
-   Basis laporan produk terlaris
-   Analisis margin produk

**Contoh Data (dari Nota INV-2024-12-001):**

```
Detail 1:
- Product: Indomie Goreng (10 pcs)
- Unit Price: Rp 3.000
- Diskon Item (5%): Rp 150/pcs
- Subtotal: Rp 28.500
- HPP: Rp 2.600/pcs
- Profit: Rp 28.500 - (2.600 × 10) = Rp 2.500

Detail 2:
- Product: Aqua 600ml (20 pcs)
- Unit Price: Rp 3.500
- Diskon Item: Rp 0
- Subtotal: Rp 70.000
- HPP: Rp 2.900/pcs
- Profit: Rp 70.000 - (2.900 × 20) = Rp 12.000
```

**Perhitungan Profit Total Nota:**

```
Total Profit = Σ profit semua detail
            = Rp 2.500 + Rp 12.000
            = Rp 14.500
```

---

### **SALES_RETURNS**

**Fungsi:** Menyimpan header transaksi retur penjualan

**Field Penting:**

-   `return_id` (PK): ID unik retur
-   `return_number`: Nomor retur (RET-001)
-   `return_date`: Tanggal retur
-   `sale_id` (FK): Link ke nota penjualan asli
-   `user_id` (FK): Petugas yang proses retur
-   `total_return`: Total nilai pengembalian
-   `reason`: Alasan retur (rusak, salah beli, kadaluarsa)
-   `status`: Status retur ('pending', 'approved', 'rejected')

**Kegunaan:**

-   Pengembalian barang dari pelanggan
-   Nomor retur untuk tracking
-   Alasan retur (rusak, salah beli, kadaluarsa, dll)
-   Total nilai barang yang dikembalikan
-   Link ke transaksi penjualan asli
-   Approval manajemen untuk retur
-   Laporan retur bulanan

**Contoh Data:**

```
RET-001
Date: 2024-12-30
Sale: INV-2024-12-001
Total: Rp 28.500
Reason: "Produk rusak/penyok"
Status: approved
```

---

### **SALES_RETURN_DETAILS**

**Fungsi:** Menyimpan detail produk yang diretur

**Field Penting:**

-   `return_detail_id` (PK): ID unik detail retur
-   `return_id` (FK): Header retur
-   `sale_detail_id` (FK): Detail penjualan yang diretur
-   `product_id` (FK): Produk yang diretur
-   `quantity`: Jumlah yang diretur
-   `unit_price`: Harga satuan (dari transaksi asli)
-   `subtotal`: Total pengembalian

**Kegunaan:**

-   Daftar barang yang dikembalikan
-   Quantity yang diretur (bisa sebagian dari pembelian)
-   Nilai pengembalian uang
-   Link ke item penjualan asli
-   Update stok masuk kembali
-   Tracking produk bermasalah

**Contoh Data:**

```
Detail Retur:
- Sale Detail: #1 (Indomie 10 pcs)
- Qty Diretur: 10 pcs (semua)
- Unit Price: Rp 2.850 (harga sudah diskon)
- Subtotal: Rp 28.500
- Alasan: Kemasan penyok/rusak
```

**Proses Retur:**

1. Customer datang bawa nota + barang
2. Kasir input retur dengan referensi nota asli
3. Manager approve
4. Uang dikembalikan ke customer
5. Stok produk kembali bertambah (atau dipisah jika rusak)

---

## <a id="transaksi-pembelian"></a>🛒 3. TABEL TRANSAKSI PEMBELIAN

### **PURCHASES**

**Fungsi:** Menyimpan header/data utama transaksi pembelian dari supplier

**Field Penting:**

-   `purchase_id` (PK): ID unik pembelian
-   `purchase_number`: Nomor PO (PO-2024-001)
-   `purchase_date`: Tanggal pembelian
-   `supplier_id` (FK): Supplier yang memasok
-   `user_id` (FK): Staff gudang yang input
-   `payment_type`: Jenis pembayaran ('cash', 'credit', 'installment', 'preorder')
-   `subtotal`: Total sebelum diskon
-   `discount_percentage`: Diskon persen dari supplier
-   `discount_amount`: Diskon nominal dari supplier
-   `cashback_amount`: Cashback/bonus dari supplier
-   `tax_amount`: Pajak pembelian
-   `total_amount`: Total yang harus dibayar
-   `paid_amount`: Jumlah yang sudah dibayar
-   `debt_amount`: Sisa hutang ke supplier
-   `status`: Status ('pending', 'partial', 'paid', 'completed')
-   `notes`: Catatan pembelian

**Kegunaan:**

-   Nota pembelian barang (Purchase Order)
-   Total biaya pembelian
-   Diskon & cashback dari supplier
-   Status pembayaran ke supplier
-   Tracking hutang ke supplier
-   **Dasar perhitungan HPP** produk
-   Laporan pembelian & hutang supplier

**Contoh Data:**

```
PURCHASE_ID: 1
PO Number: PO-2024-12-001
Date: 2024-12-25
Supplier: PT Indofood Sukses Makmur
Staff: Ahmad Yani (gudang01)
Payment: credit (tempo 30 hari)
Subtotal: Rp 10.000.000
Diskon Supplier (5%): Rp 500.000
Cashback: Rp 100.000
Tax: Rp 0 (non-PKP)
Total: Rp 9.400.000
Paid: Rp 0
Debt: Rp 9.400.000
Status: pending
```

---

### **PURCHASE_DETAILS**

**Fungsi:** Menyimpan detail produk yang dibeli dari supplier

**Field Penting:**

-   `purchase_detail_id` (PK): ID unik detail
-   `purchase_id` (FK): Header pembelian
-   `product_id` (FK): Produk yang dibeli
-   `quantity`: Jumlah yang dibeli
-   `unit_cost`: **Harga beli per unit (INI = HPP!)**
-   `discount_percentage`: Diskon persen per item
-   `discount_amount`: Diskon nominal per item
-   `cashback_amount`: Cashback per item
-   `subtotal`: Total biaya item

**Kegunaan:**

-   Daftar barang yang dibeli
-   **Harga beli per item (unit_cost = HPP dasar)**
-   Quantity pembelian
-   Diskon per item dari supplier
-   Update stok masuk
-   Membuat batch baru (untuk FIFO)
-   Update average cost (untuk Average)

**Contoh Data (dari PO-2024-12-001):**

```
Detail 1:
- Product: Indomie Goreng
- Quantity: 500 pcs (50 dus)
- Unit Cost: Rp 2.600/pcs ← INI HPP!
- Diskon Item: Rp 0
- Subtotal: Rp 1.300.000

Detail 2:
- Product: Aqua 600ml
- Quantity: 1000 pcs (100 dus)
- Unit Cost: Rp 2.900/pcs ← INI HPP!
- Diskon Item (3%): Rp 87/pcs
- Subtotal: Rp 2.813.000
```

**Dampak ke Stok:**

-   Indomie: stok +500, batch baru @ Rp 2.600
-   Aqua: stok +1000, batch baru @ Rp 2.900

---

### **PURCHASE_RETURNS**

**Fungsi:** Menyimpan header transaksi retur pembelian ke supplier

**Field Penting:**

-   `return_id` (PK): ID unik retur
-   `return_number`: Nomor retur (RETPO-001)
-   `return_date`: Tanggal retur
-   `purchase_id` (FK): Link ke PO asli
-   `user_id` (FK): Staff yang proses
-   `total_return`: Total nilai yang diklaim
-   `reason`: Alasan retur
-   `status`: Status ('pending', 'approved', 'rejected')

**Kegunaan:**

-   Pengembalian barang ke supplier
-   Barang rusak/cacat/salah kirim
-   Alasan pengembalian
-   Total nilai yang diklaim
-   Pengurangan hutang atau pengembalian uang
-   Tracking supplier bermasalah

**Contoh Data:**

```
RETPO-001
Date: 2024-12-26
Purchase: PO-2024-12-001
Total: Rp 260.000
Reason: "10 dus Indomie rusak/penyok"
Status: approved
```

---

### **PURCHASE_RETURN_DETAILS**

**Fungsi:** Menyimpan detail produk yang diretur ke supplier

**Field Penting:**

-   `return_detail_id` (PK): ID unik detail retur
-   `return_id` (FK): Header retur
-   `purchase_detail_id` (FK): Detail pembelian yang diretur
-   `product_id` (FK): Produk yang diretur
-   `quantity`: Jumlah yang diretur
-   `unit_cost`: Harga beli satuan
-   `subtotal`: Total klaim

**Kegunaan:**

-   Daftar barang yang dikembalikan ke supplier
-   Quantity yang diretur
-   Nilai pengembalian/klaim
-   Update stok keluar (barang rusak)
-   Pengurangan hutang

**Contoh Data:**

```
Detail Retur:
- Purchase Detail: #1 (Indomie)
- Qty Diretur: 100 pcs (10 dus)
- Unit Cost: Rp 2.600
- Subtotal: Rp 260.000
- Alasan: Kemasan rusak saat pengiriman
```

---

## <a id="pembayaran"></a>💳 4. TABEL PEMBAYARAN

### **PAYMENTS**

**Fungsi:** Menyimpan riwayat pembayaran/pelunasan (cicilan hutang)

**Field Penting:**

-   `payment_id` (PK): ID unik pembayaran
-   `payment_number`: Nomor bukti bayar (PAY-001)
-   `payment_date`: Tanggal pembayaran
-   `transaction_type`: Jenis transaksi ('sale', 'purchase')
-   `transaction_id` (FK): ID transaksi (sale_id atau purchase_id)
-   `payment_amount`: Jumlah yang dibayar
-   `payment_method`: Metode ('cash', 'transfer', 'debit_card', 'credit_card', 'e-wallet')
-   `reference_number`: Nomor referensi (no transfer, no kartu, dll)
-   `notes`: Catatan pembayaran

**Kegunaan:**

-   Cicilan hutang pelanggan
-   Pelunasan hutang ke supplier
-   Pembayaran bertahap untuk transaksi tempo
-   Tracking metode pembayaran
-   Rekonsiliasi keuangan
-   Bisa untuk 1 transaksi dibayar beberapa kali
-   Laporan kas harian

**Contoh Skenario - Pembayaran Cicilan:**

**Transaksi Awal:**

```
Sale INV-2024-12-001:
- Total: Rp 489.500
- DP: Rp 100.000
- Sisa Hutang: Rp 389.500
- Status: partial
```

**Cicilan 1 (1 minggu kemudian):**

```
Payment PAY-001:
- Date: 2025-01-05
- Type: sale
- Transaction: INV-2024-12-001
- Amount: Rp 200.000
- Method: transfer
- Reference: TRF20250105001
- Notes: "Cicilan 1 dari 2"

Update SALES:
- debt_amount: Rp 189.500
- Status: partial
```

**Cicilan 2 (Pelunasan):**

```
Payment PAY-002:
- Date: 2025-01-12
- Type: sale
- Transaction: INV-2024-12-001
- Amount: Rp 189.500
- Method: cash
- Reference: -
- Notes: "Pelunasan"

Update SALES:
- debt_amount: Rp 0
- Status: paid
```

**Query Tracking Hutang:**

```sql
SELECT
    s.invoice_number,
    s.total_amount,
    s.paid_amount,
    s.debt_amount,
    COALESCE(SUM(p.payment_amount), 0) as total_cicilan,
    s.debt_amount - COALESCE(SUM(p.payment_amount), 0) as sisa_hutang
FROM SALES s
LEFT JOIN PAYMENTS p ON p.transaction_id = s.sale_id
    AND p.transaction_type = 'sale'
WHERE s.status != 'paid'
GROUP BY s.sale_id;
```

---

## <a id="inventory-stok"></a>📦 5. TABEL INVENTORY & STOK

### **STOCK_MOVEMENTS**

**Fungsi:** Mencatat setiap pergerakan stok produk (Kartu Stok)

**Field Penting:**

-   `movement_id` (PK): ID unik pergerakan
-   `movement_date`: Tanggal & waktu pergerakan
-   `product_id` (FK): Produk yang bergerak
-   `movement_type`: Jenis pergerakan
    -   'sale' = Penjualan (keluar)
    -   'purchase' = Pembelian (masuk)
    -   'sale_return' = Retur penjualan (masuk)
    -   'purchase_return' = Retur pembelian (keluar)
    -   'adjustment' = Penyesuaian manual
    -   'opname' = Hasil stok opname
-   `quantity`: Jumlah (+ untuk masuk, - untuk keluar)
-   `reference_id`: ID transaksi referensi
-   `reference_type`: Tipe referensi ('sale', 'purchase', 'return', 'adjustment', 'opname')
-   `notes`: Catatan

**Kegunaan:**

-   **History lengkap** keluar-masuk barang
-   Tracking kapan, berapa, kenapa stok berubah
-   Audit trail stok untuk investigasi selisih
-   Laporan kartu stok per produk
-   Rekonsiliasi stok fisik vs sistem
-   Identifikasi kebocoran/kehilangan stok

**Contoh Data (Kartu Stok Indomie Goreng):**

```
Date       | Type           | Qty  | Balance | Reference
-----------|----------------|------|---------|------------------
2024-12-25 | purchase       | +500 | 500     | PO-2024-12-001
2024-12-26 | sale           | -10  | 490     | INV-2024-12-001
2024-12-26 | sale           | -15  | 475     | INV-2024-12-002
2024-12-27 | purchase       | +300 | 775     | PO-2024-12-002
2024-12-28 | sale_return    | +10  | 785     | RET-001
2024-12-29 | adjustment     | -5   | 780     | ADJ-001 (rusak)
2024-12-30 | opname         | -30  | 750     | OPN-001 (selisih)
```

**Query Kartu Stok:**

```sql
SELECT
    movement_date,
    movement_type,
    quantity,
    @balance := @balance + quantity as balance,
    reference_type,
    reference_id,
    notes
FROM STOCK_MOVEMENTS, (SELECT @balance := 0) vars
WHERE product_id = 1
ORDER BY movement_date ASC;
```

---

### **STOCK_OPNAMES**

**Fungsi:** Menyimpan header/sesi penghitungan fisik stok (Stok Opname)

**Field Penting:**

-   `opname_id` (PK): ID unik opname
-   `opname_number`: Nomor opname (OPN-2024-12)
-   `opname_date`: Tanggal opname
-   `user_id` (FK): Petugas yang menghitung
-   `status`: Status ('draft', 'approved', 'cancelled')
-   `notes`: Catatan opname
-   `approved_at`: Waktu approval
-   `approved_by` (FK): Manager yang approve

**Kegunaan:**

-   Stok opname berkala (bulanan/tahunan)
-   Verifikasi stok sistem vs fisik
-   Approval proses (draft → approved)
-   Dokumentasi siapa & kapan melakukan opname
-   Basis laporan selisih stok
-   Koreksi stok sistem

**Contoh Data:**

```
OPN-2024-12:
- Date: 2024-12-30
- Status: draft
- Petugas: Ahmad Yani (gudang01)
- Notes: "Stok opname akhir bulan Desember 2024"

Setelah hitungan selesai:
- Status: approved
- Approved By: Budi Santoso (manager)
- Approved At: 2024-12-31 16:00:00
```

**Alur Proses:**

1. **Draft**: Buat sesi opname, mulai hitung fisik
2. **Input Data**: Masukkan hasil hitungan per produk
3. **Review**: Cek selisih, investigasi jika ada anomali besar
4. **Approval**: Manager approve, sistem auto-adjust stok
5. **Completed**: Stok sistem sudah sesuai fisik

---

### **STOCK_OPNAME_DETAILS**

**Fungsi:** Menyimpan hasil hitungan per produk saat opname

**Field Penting:**

-   `opname_detail_id` (PK): ID unik detail
-   `opname_id` (FK): Header opname
-   `product_id` (FK): Produk yang dihitung
-   `system_stock`: Stok yang tercatat di sistem
-   `physical_stock`: Hasil hitungan fisik
-   `difference`: Selisih (physical - system)
-   `adjustment_type`: Jenis selisih
    -   'shortage' = Kekurangan (hilang/rusak)
    -   'excess' = Kelebihan (temuan/salah input)
-   `unit_cost`: Harga satuan (untuk hitung nilai)
-   `total_value`: Nilai nominal selisih
-   `reason`: Alasan selisih
-   `notes`: Catatan

**Kegunaan:**

-   Bandingkan stok sistem vs hitungan fisik
-   Identifikasi selisih (kurang/lebih)
-   Catat alasan selisih (hilang, rusak, salah input)
-   Hitung nilai kerugian/kelebihan
-   Adjustment otomatis ke stok sistem (setelah approval)
-   Laporan akurasi inventory

**Contoh Data (OPN-2024-12):**

```
Detail 1:
- Product: Indomie Goreng
- System Stock: 780 pcs
- Physical Stock: 750 pcs
- Difference: -30 pcs
- Type: shortage
- Unit Cost: Rp 2.600
- Total Value: -Rp 78.000 (kerugian)
- Reason: "Kemungkinan rusak tidak tercatat"

Detail 2:
- Product: Aqua 600ml
- System Stock: 450 pcs
- Physical Stock: 452 pcs
- Difference: +2 pcs
- Type: excess
- Unit Cost: Rp 2.900
- Total Value: +Rp 5.800
- Reason: "Temuan barang tidak ter-input"

Detail 3:
- Product: Pulpen AE-7
- System Stock: 75 pcs
- Physical Stock: 75 pcs
- Difference: 0
- Type: -
- Reason: "Sesuai"
```

**Laporan Nilai Selisih:**

```
Total Shortage: -Rp 78.000
Total Excess: +Rp 5.800
Net Loss: -Rp 72.200
```

---

### **STOCK_ADJUSTMENTS**

**Fungsi:** Menyimpan header penyesuaian stok manual (di luar opname)

**Field Penting:**

-   `adjustment_id` (PK): ID unik adjustment
-   `adjustment_number`: Nomor penyesuaian (ADJ-001)
-   `adjustment_date`: Tanggal adjustment
-   `user_id` (FK): Staff yang input
-   `adjustment_type`: Jenis penyesuaian
    -   'damaged' = Barang rusak
    -   'expired' = Kadaluarsa
    -   'lost' = Hilang/dicuri
    -   'found' = Temuan barang
    -   'correction' = Koreksi kesalahan input
    -   'sample' = Barang sample/promosi
-   `status`: Status ('draft', 'approved')
-   `notes`: Catatan

**Kegunaan:**

-   Koreksi stok di luar opname
-   Barang rusak, kadaluarsa, hilang
-   Temuan barang (ketemu stok yang tidak tercatat)
-   Koreksi kesalahan input
-   Barang sample/promosi
-   Tracking kerugian non-operasional

**Contoh Data:**

```
ADJ-2024-12-001:
- Date: 2024-12-28
- Type: damaged
- Status: approved
- Staff: Ahmad Yani
- Notes: "Barang rusak saat display"
```

---

### **STOCK_ADJUSTMENT_DETAILS**

**Fungsi:** Menyimpan detail produk yang disesuaikan

**Field Penting:**

-   `adjustment_detail_id` (PK): ID unik detail
-   `adjustment_id` (FK): Header adjustment
-   `product_id` (FK): Produk yang disesuaikan
-   `quantity`: Jumlah yang disesuaikan
-   `type`: Arah penyesuaian
    -   'in' = Penambahan stok
    -   'out' = Pengurangan stok
-   `unit_cost`: Harga satuan
-   `total_value`: Total nilai penyesuaian
-   `reason`: Alasan detail
-   `notes`: Catatan

**Kegunaan:**

-   Produk mana yang disesuaikan
-   Jumlah tambah/kurang (in/out)
-   Alasan penyesuaian
-   Nilai kerugian untuk laporan keuangan
-   Update stok otomatis
-   Tracking barang bermasalah

**Contoh Data (ADJ-2024-12-001 - Barang Rusak):**

```
Detail 1:
- Product: Indomie Goreng
- Quantity: 5 pcs
- Type: out (pengurangan)
- Unit Cost: Rp 2.600
- Total Value: -Rp 13.000
- Reason: "Kemasan penyok saat dipajang"

Detail 2:
- Product: Aqua 600ml
- Quantity: 3 pcs
- Type: out
- Unit Cost: Rp 2.900
- Total Value: -Rp 8.700
- Reason: "Botol bocor"
```

**Contoh Data (ADJ-2024-12-002 - Temuan Barang):**

```
Detail 1:
- Product: Pulpen AE-7
- Quantity: 10 pcs
- Type: in (penambahan)
- Unit Cost: Rp 1.600
- Total Value: +Rp 16.000
- Reason: "Temuan barang di gudang yang tidak ter-input"
```

---

## <a id="costing"></a>💲 6. TABEL COSTING (FIFO/AVERAGE)

### **PRODUCT_BATCHES**

**Fungsi:** Tracking batch/lot pembelian produk (KRUSIAL untuk FIFO!)

**Field Penting:**

-   `batch_id` (PK): ID unik batch
-   `product_id` (FK): Produk
-   `batch_number`: Nomor batch (BATCH-001-2024-12)
-   `purchase_detail_id` (FK): Link ke detail pembelian
-   `purchase_date`: Tanggal pembelian batch ini
-   `unit_cost`: **HPP batch ini**
-   `initial_quantity`: Jumlah awal batch
-   `current_quantity`: Sisa stok batch ini
-   `expiry_date`: Tanggal kadaluarsa (jika ada)
-   `status`: Status batch
    -   'available' = Masih ada stok
    -   'depleted' = Habis
    -   'expired' = Kadaluarsa

**Kegunaan:**

-   **WAJIB untuk metode FIFO!**
-   Setiap pembelian = batch baru
-   Menyimpan harga beli per batch
-   Tracking sisa stok per batch
-   Tanggal kadaluarsa per batch
-   FEFO (First Expired First Out)
-   History pembelian per batch

**Contoh Data (Produk: Indomie Goreng):**

```
BATCH #1:
- Batch Number: BATCH-001-2024-12-25
- Purchase: PO-2024-12-001, Detail #1
- Purchase Date: 2024-12-25
- Unit Cost: Rp 2.600 ← HPP batch ini
- Initial Qty: 500 pcs
- Current Qty: 470 pcs (sisa)
- Expiry: 2025-06-25
- Status: available

BATCH #2:
- Batch Number: BATCH-002-2024-12-27
- Purchase: PO-2024-12-002, Detail #1
- Purchase Date: 2024-12-27
- Unit Cost: Rp 2.700 ← HPP naik!
- Initial Qty: 300 pcs
- Current Qty: 300 pcs (belum terpakai)
- Expiry: 2025-07-15
- Status: available

BATCH #3:
- Batch Number: BATCH-003-2024-11-20
- Purchase Date: 2024-11-20
- Unit Cost: Rp 2.500
- Initial Qty: 200 pcs
- Current Qty: 0 pcs
- Expiry: 2025-05-20
- Status: depleted (sudah habis)
```

**Logika FIFO Saat Penjualan 50 pcs:**

1. Ambil dari Batch #1 (paling lama): 50 pcs @ Rp 2.600
2. HPP penjualan = Rp 2.600/pcs
3. Update Batch #1: current_qty = 420 pcs

**Logika FIFO Saat Penjualan 500 pcs:**

1. Ambil dari Batch #1: 470 pcs @ Rp 2.600
2. Batch #1 habis, ambil dari Batch #2: 30 pcs @ Rp 2.700
3. HPP gabungan = ((470 × 2.600) + (30 × 2.700)) / 500 = Rp 2.606

---

### **BATCH_MOVEMENTS**

**Fungsi:** Tracking penggunaan batch saat transaksi

**Field Penting:**

-   `batch_movement_id` (PK): ID unik movement
-   `batch_id` (FK): Batch yang dipakai
-   `stock_movement_id` (FK): Link ke stock movement
-   `transaction_type`: Jenis transaksi ('sale', 'adjustment', 'return')
-   `transaction_detail_id` (FK): ID detail transaksi
-   `quantity`: Jumlah dari batch ini yang dipakai
-   `unit_cost`: HPP batch
-   `movement_date`: Tanggal penggunaan

**Kegunaan:**

-   Mencatat batch mana yang dipakai saat jual
-   Link ke transaksi penjualan/adjustment
-   History lengkap penggunaan per batch
-   Audit trail untuk perhitungan HPP
-   Tracking FEFO (First Expired First Out)

**Contoh Data (Penjualan 500 pcs Indomie):**

```
Movement 1:
- Batch: #1
- Stock Movement: #55
- Transaction: sale, sale_detail_id = 10
- Quantity: 470 pcs
- Unit Cost: Rp 2.600
- Date: 2024-12-30

Movement 2:
- Batch: #2
- Stock Movement: #55
- Transaction: sale, sale_detail_id = 10
- Quantity: 30 pcs
- Unit Cost: Rp 2.700
- Date: 2024-12-30
```

**Query HPP Real Penjualan:**

```sql
SELECT
    sd.sale_detail_id,
    p.product_name,
    sd.quantity,
    SUM(bm.quantity * bm.unit_cost) / sd.quantity as hpp_actual
FROM SALES_DETAILS sd
JOIN BATCH_MOVEMENTS bm ON bm.transaction_detail_id = sd.sale_detail_id
    AND bm.transaction_type = 'sale'
JOIN PRODUCTS p ON sd.product_id = p.product_id
WHERE sd.sale_id = 1
GROUP BY sd.sale_detail_id;
```

---

## <a id="pengaturan"></a>⚙️ 7. TABEL PENGATURAN

### **COMPANY_SETTINGS**

**Fungsi:** Pengaturan global sistem aplikasi

**Field Penting:**

-   `setting_id` (PK): ID unik setting
-   `setting_key`: Kunci setting (nama setting)
-   `setting_value`: Nilai setting
-   `description`: Deskripsi setting
-   `updated_at`: Terakhir diupdate

**Kegunaan:**

-   **Metode costing global** (FIFO/Average)
-   Setting pajak (PPN)
-   Format nomor nota
-   Mata uang
-   Setting printer
-   Setting backup otomatis
-   Batas kredit default
-   Dan setting lainnya

**Contoh Data:**

```
1 | costing_method      | FIFO              | Metode perhitungan HPP global
2 | tax_percentage      | 11                | Persentase PPN
3 | tax_enabled         | true              | Aktifkan perhitungan pajak
4 | currency            | IDR               | Mata uang
5 | invoice_prefix      | INV               | Prefix nomor nota
6 | po_prefix           | PO                | Prefix nomor PO
7 | min_stock_alert     | true              | Alert stok minimum
8 | backup_auto         | true              | Backup otomatis
9 | backup_time         | 23:00             | Jam backup
10| credit_limit_default| 1000000           | Limit kredit default customer
11| credit_days_default | 30                | Tempo kredit default (hari)
12| cashback_enabled    | true              | Aktifkan cashback
13| loyalty_enabled     | true              | Aktifkan program loyalty
14| receipt_footer      | Terima kasih...   | Footer struk
15| store_name          | Alfamini Sejahtera| Nama toko
16| store_address       | Jl. Sudirman 123  | Alamat toko
17| store_phone         | 0274-123456       | Telepon toko
```

**Query Get Setting:**

```sql
SELECT setting_value
FROM COMPANY_SETTINGS
WHERE setting_key = 'costing_method';
-- Result: 'FIFO'
```

**Override Costing per Produk:**

```sql
-- Produk tertentu bisa override setting global
UPDATE PRODUCTS
SET costing_method = 'AVERAGE'
WHERE product_id = 5;

-- Produk lain ikuti setting global
UPDATE PRODUCTS
SET costing_method = 'SYSTEM'
WHERE product_id = 10;
```

---

## 🔗 RELASI ANTAR TABEL

### **Relasi Master Data:**

-   CUSTOMER_GROUPS → CUSTOMERS (one-to-many)
-   CUSTOMER_GROUPS → PRODUCT_PRICES (one-to-many)
-   CATEGORIES → PRODUCTS (one-to-many)
-   PRODUCTS → PRODUCT_PRICES (one-to-many)
-   PRODUCTS → PRODUCT_BATCHES (one-to-many)

### **Relasi Transaksi Penjualan:**

-   USERS → SALES (one-to-many)
-   CUSTOMERS → SALES (one-to-many)
-   SALES → SALES_DETAILS (one-to-many)
-   PRODUCTS → SALES_DETAILS (one-to-many)
-   SALES → SALES_RETURNS (one-to-many)
-   SALES_RETURNS → SALES_RETURN_DETAILS (one-to-many)
-   SALES_DETAILS → SALES_RETURN_DETAILS (one-to-many)

### **Relasi Transaksi Pembelian:**

-   USERS → PURCHASES (one-to-many)
-   SUPPLIERS → PURCHASES (one-to-many)
-   PURCHASES → PURCHASE_DETAILS (one-to-many)
-   PRODUCTS → PURCHASE_DETAILS (one-to-many)
-   PURCHASE_DETAILS → PRODUCT_BATCHES (one-to-many)
-   PURCHASES → PURCHASE_RETURNS (one-to-many)
-   PURCHASE_RETURNS → PURCHASE_RETURN_DETAILS (one-to-many)
-   PURCHASE_DETAILS → PURCHASE_RETURN_DETAILS (one-to-many)

### **Relasi Inventory:**

-   PRODUCTS → STOCK_MOVEMENTS (one-to-many)
-   PRODUCTS → STOCK_OPNAME_DETAILS (one-to-many)
-   PRODUCTS → STOCK_ADJUSTMENT_DETAILS (one-to-many)
-   STOCK_OPNAMES → STOCK_OPNAME_DETAILS (one-to-many)
-   STOCK_ADJUSTMENTS → STOCK_ADJUSTMENT_DETAILS (one-to-many)

### **Relasi Costing:**

-   PRODUCT_BATCHES → BATCH_MOVEMENTS (one-to-many)
-   STOCK_MOVEMENTS → BATCH_MOVEMENTS (one-to-many)

---

## 📊 LAPORAN YANG BISA DIHASILKAN

### **Laporan Penjualan:**

-   Penjualan harian/bulanan/tahunan
-   Produk terlaris
-   Penjualan per kategori
-   Penjualan per kasir
-   Penjualan per customer/grup
-   Analisis profit per produk
-   Analisis margin

### **Laporan Pembelian:**

-   Pembelian per supplier
-   Pembelian per produk
-   Analisis harga beli
-   Evaluasi supplier

### **Laporan Keuangan:**

-   Laporan hutang piutang
-   Laporan kas harian
-   Laporan laba rugi
-   Laporan HPP vs harga jual

### **Laporan Inventory:**

-   Kartu stok per produk
-   Laporan stok minimum
-   Laporan stok opname
-   Laporan selisih stok
-   Laporan barang rusak/kadaluarsa
-   Laporan fast moving/slow moving

### **Laporan Customer:**

-   Customer terbaik
-   Riwayat pembelian customer
-   Tracking hutang customer

---

## 🎯 TIPS IMPLEMENTASI

### **1. Indexing untuk Performa:**

```sql
-- Index untuk pencarian cepat
CREATE INDEX idx_products_barcode ON PRODUCTS(barcode);
CREATE INDEX idx_sales_date ON SALES(sale_date);
CREATE INDEX idx_sales_customer ON SALES(customer_id);
CREATE INDEX idx_stock_movements_product ON STOCK_MOVEMENTS(product_id);
CREATE INDEX idx_batches_product_date ON PRODUCT_BATCHES(product_id, purchase_date);
```

### **2. Trigger Otomatis:**

```sql
-- Trigger update stok saat penjualan
CREATE TRIGGER after_sale_detail_insert
AFTER INSERT ON SALES_DETAILS
FOR EACH ROW
BEGIN
    UPDATE PRODUCTS
    SET current_stock = current_stock - NEW.quantity
    WHERE product_id = NEW.product_id;

    INSERT INTO STOCK_MOVEMENTS
    VALUES (NULL, NOW(), NEW.product_id, 'sale', NEW.quantity * -1,
            NEW.sale_id, 'sale', NULL, NOW());
END;
```

### **3. View untuk Laporan:**

```sql
-- View untuk laporan penjualan harian
CREATE VIEW v_daily_sales AS
SELECT
    DATE(sale_date) as sale_date,
    COUNT(*) as total_transactions,
    SUM(total_amount) as total_sales,
    SUM(total_amount - (SELECT SUM(hpp * quantity)
                        FROM SALES_DETAILS
                        WHERE sale_id = s.sale_id)) as total_profit
FROM SALES s
WHERE status = 'paid' OR status = 'completed'
GROUP BY DATE(sale_date);
```

### **4. Stored Procedure untuk FIFO:**

```sql
-- Procedure untuk menghitung HPP FIFO saat penjualan
CREATE PROCEDURE calculate_fifo_cost(
    IN p_product_id INT,
    IN p_quantity INT,
    OUT p_total_cost DECIMAL(15,2)
)
-- (Sudah dijelaskan di atas)
```

---

## ✅ CHECKLIST FITUR

**Master Data:**

-   ✅ User Management dengan Role
-   ✅ Customer Management dengan Grouping
-   ✅ Supplier Management
-   ✅ Product Management dengan Barcode
-   ✅ Category Management
-   ✅ Harga Khusus per Group

**Transaksi:**

-   ✅ Penjualan (Tunai/Kredit/Tempo/Pre-order)
-   ✅ Pembelian (Tunai/Kredit/Tempo/Pre-order)
-   ✅ Retur Penjualan
-   ✅ Retur Pembelian
-   ✅ Pembayaran Cicilan
-   ✅ Diskon per Nota & per Item
-   ✅ Cashback per Nota & per Item

**Inventory:**

-   ✅ Tracking Stok Real-time
-   ✅ Stock Movement History
-   ✅ Stok Opname
-   ✅ Penyesuaian Stok Manual
-   ✅ Alert Stok Minimum

**Costing:**

-   ✅ Metode FIFO
-   ✅ Metode Average
-   ✅ Batch Tracking
-   ✅ Perhitungan HPP Akurat
-   ✅ Perhitungan Profit per Item

**Laporan:**

-   ✅ Laporan Penjualan
-   ✅ Laporan Pembelian
-   ✅ Laporan Laba Rugi
-   ✅ Laporan Hutang Piutang
-   ✅ Laporan Inventory
-   ✅ Kartu Stok

---
