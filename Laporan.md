# 📊 LAPORAN PENTING APLIKASI MINIMARKET

## Daftar Isi

- [A. Laporan Harian (3 Laporan)](#a-laporan-harian)
- [B. Laporan Mingguan/Bulanan (8 Laporan)](#b-laporan-mingguanbulanan)
- [C. Laporan Real-time (1 Laporan)](#c-laporan-real-time)

---

## A. LAPORAN HARIAN

### 1️⃣ LAPORAN PENJUALAN HARIAN

**Untuk:** Kasir & Manager  
**Frekuensi:** Setiap hari (End of Day)

**Informasi yang Ditampilkan:**

- Total penjualan hari ini (Rp)
- Jumlah transaksi
- Rata-rata nilai per transaksi
- Total profit harian
- Breakdown metode pembayaran (tunai, kredit, transfer, dll)

**Sumber Data:**

```sql
-- Query Laporan Penjualan Harian
SELECT
    DATE(sale_date) as tanggal,
    COUNT(*) as jumlah_transaksi,
    SUM(total_amount) as total_penjualan,
    AVG(total_amount) as rata_rata_transaksi,
    SUM(CASE WHEN payment_type = 'cash' THEN total_amount ELSE 0 END) as penjualan_tunai,
    SUM(CASE WHEN payment_type = 'credit' THEN total_amount ELSE 0 END) as penjualan_kredit,
    SUM(CASE WHEN payment_type = 'installment' THEN total_amount ELSE 0 END) as penjualan_cicilan
FROM SALES
WHERE DATE(sale_date) = CURDATE()
AND status IN ('paid', 'completed', 'partial')
GROUP BY DATE(sale_date);

-- Total Profit Harian
SELECT
    DATE(s.sale_date) as tanggal,
    SUM(sd.profit) as total_profit
FROM SALES s
JOIN SALES_DETAILS sd ON s.sale_id = sd.sale_id
WHERE DATE(s.sale_date) = CURDATE()
AND s.status IN ('paid', 'completed', 'partial')
GROUP BY DATE(s.sale_date);
```

**Tabel yang Digunakan:**

- `SALES` - Data header transaksi penjualan
- `SALES_DETAILS` - Detail produk & profit per item

---

### 2️⃣ LAPORAN KAS HARIAN

**Untuk:** Kasir & Keuangan  
**Frekuensi:** Setiap hari (End of Day)

**Informasi yang Ditampilkan:**

- Saldo awal kas
- Penerimaan:
    - Penjualan tunai
    - Pelunasan piutang (dari customer)
- Pengeluaran:
    - Pembelian tunai
    - Pembayaran hutang (ke supplier)
- Saldo akhir kas
- Selisih (jika ada)

**Sumber Data:**

```sql
-- Penerimaan dari Penjualan Tunai
SELECT
    SUM(total_amount) as penjualan_tunai
FROM SALES
WHERE DATE(sale_date) = CURDATE()
AND payment_type = 'cash'
AND status IN ('paid', 'completed');

-- Penerimaan dari Pelunasan Piutang (Cash)
SELECT
    SUM(payment_amount) as pelunasan_piutang
FROM PAYMENTS
WHERE DATE(payment_date) = CURDATE()
AND transaction_type = 'sale'
AND payment_method = 'cash';

-- Pengeluaran untuk Pembelian Tunai
SELECT
    SUM(total_amount) as pembelian_tunai
FROM PURCHASES
WHERE DATE(purchase_date) = CURDATE()
AND payment_type = 'cash'
AND status IN ('paid', 'completed');

-- Pengeluaran untuk Pembayaran Hutang (Cash)
SELECT
    SUM(payment_amount) as bayar_hutang
FROM PAYMENTS
WHERE DATE(payment_date) = CURDATE()
AND transaction_type = 'purchase'
AND payment_method = 'cash';
```

**Tabel yang Digunakan:**

- `SALES` - Penjualan tunai
- `PURCHASES` - Pembelian tunai
- `PAYMENTS` - Pelunasan & pembayaran

**Catatan:**

- Saldo awal = Saldo akhir hari sebelumnya (atau manual input)
- Saldo akhir = Saldo awal + Penerimaan - Pengeluaran
- Selisih = Saldo fisik (hitung manual) - Saldo sistem

---

### 3️⃣ LAPORAN STOK MINIMUM (ALERT REORDER)

**Untuk:** Gudang & Pembelian  
**Frekuensi:** Setiap hari (atau real-time)

**Informasi yang Ditampilkan:**

- Produk dengan stok di bawah minimum
- Stok saat ini
- Stok minimum
- Selisih kekurangan
- Suggested order quantity

**Sumber Data:**

```sql
-- Produk yang Perlu Reorder
SELECT
    p.product_id,
    p.product_code,
    p.product_name,
    c.category_name,
    p.current_stock,
    p.min_stock,
    p.min_stock - p.current_stock as kekurangan,
    (p.min_stock * 2) - p.current_stock as suggested_order
FROM PRODUCTS p
JOIN CATEGORIES c ON p.category_id = c.category_id
WHERE p.current_stock <= p.min_stock
AND p.is_active = true
ORDER BY (p.min_stock - p.current_stock) DESC;
```

**Tabel yang Digunakan:**

- `PRODUCTS` - Current stock & minimum stock
- `CATEGORIES` - Kategori produk

---

## B. LAPORAN MINGGUAN/BULANAN

### 4️⃣ LAPORAN LABA RUGI

**Untuk:** Owner & Manager  
**Frekuensi:** Mingguan/Bulanan

**Informasi yang Ditampilkan:**

- Total Penjualan (Revenue)
- HPP (Cost of Goods Sold)
- Gross Profit = Revenue - HPP
- Gross Profit Margin %
- Biaya Operasional (manual input di aplikasi)
- Net Profit = Gross Profit - Biaya Operasional
- Net Profit Margin %

**Sumber Data:**

```sql
-- Laporan Laba Rugi Bulanan
SELECT
    DATE_FORMAT(s.sale_date, '%Y-%m') as periode,
    SUM(s.total_amount) as total_revenue,
    SUM(sd.hpp * sd.quantity) as total_hpp,
    SUM(s.total_amount) - SUM(sd.hpp * sd.quantity) as gross_profit,
    (SUM(s.total_amount) - SUM(sd.hpp * sd.quantity)) / SUM(s.total_amount) * 100 as gross_margin_pct
FROM SALES s
JOIN SALES_DETAILS sd ON s.sale_id = sd.sale_id
WHERE s.sale_date >= '2024-01-01'
AND s.sale_date < '2024-02-01'
AND s.status IN ('paid', 'completed', 'partial')
GROUP BY DATE_FORMAT(s.sale_date, '%Y-%m');

-- Atau langsung dari profit yang sudah dihitung
SELECT
    DATE_FORMAT(s.sale_date, '%Y-%m') as periode,
    SUM(s.total_amount) as total_revenue,
    SUM(sd.profit) as gross_profit
FROM SALES s
JOIN SALES_DETAILS sd ON s.sale_id = sd.sale_id
WHERE s.sale_date >= '2024-01-01'
AND s.sale_date < '2024-02-01'
AND s.status IN ('paid', 'completed', 'partial')
GROUP BY DATE_FORMAT(s.sale_date, '%Y-%m');
```

**Tabel yang Digunakan:**

- `SALES` - Total penjualan
- `SALES_DETAILS` - HPP & Profit per item

**Catatan:**

- Biaya operasional (gaji, listrik, sewa, dll) biasanya input manual
- Net Profit = Gross Profit - Biaya Operasional

---

### 5️⃣ LAPORAN PRODUK TERLARIS

**Untuk:** Pembelian & Marketing  
**Frekuensi:** Mingguan/Bulanan

**Informasi yang Ditampilkan:**

- Top 20 produk by quantity (paling laris)
- Top 20 produk by revenue (kontribusi penjualan)
- Top 20 produk by profit (paling menguntungkan)
- Slow moving products (jarang terjual)

**Sumber Data:**

```sql
-- Top 20 Produk by Quantity
SELECT
    p.product_id,
    p.product_name,
    c.category_name,
    SUM(sd.quantity) as total_terjual,
    SUM(sd.subtotal) as total_revenue,
    SUM(sd.profit) as total_profit
FROM SALES_DETAILS sd
JOIN PRODUCTS p ON sd.product_id = p.product_id
JOIN CATEGORIES c ON p.category_id = c.category_id
JOIN SALES s ON sd.sale_id = s.sale_id
WHERE s.sale_date >= '2024-01-01'
AND s.sale_date < '2024-02-01'
AND s.status IN ('paid', 'completed', 'partial')
GROUP BY p.product_id
ORDER BY total_terjual DESC
LIMIT 20;

-- Top 20 Produk by Revenue
-- (Query yang sama, ORDER BY total_revenue DESC)

-- Top 20 Produk by Profit
-- (Query yang sama, ORDER BY total_profit DESC)

-- Slow Moving Products (Jarang Terjual)
SELECT
    p.product_id,
    p.product_name,
    c.category_name,
    p.current_stock,
    COALESCE(SUM(sd.quantity), 0) as total_terjual,
    DATEDIFF(CURDATE(), MAX(s.sale_date)) as hari_terakhir_terjual
FROM PRODUCTS p
JOIN CATEGORIES c ON p.category_id = c.category_id
LEFT JOIN SALES_DETAILS sd ON p.product_id = sd.product_id
LEFT JOIN SALES s ON sd.sale_id = s.sale_id
    AND s.sale_date >= DATE_SUB(CURDATE(), INTERVAL 90 DAY)
    AND s.status IN ('paid', 'completed', 'partial')
WHERE p.is_active = true
GROUP BY p.product_id
HAVING total_terjual < 10 OR total_terjual IS NULL
ORDER BY total_terjual ASC, hari_terakhir_terjual DESC;
```

**Tabel yang Digunakan:**

- `SALES_DETAILS` - Data penjualan per produk
- `PRODUCTS` - Info produk
- `CATEGORIES` - Kategori produk
- `SALES` - Filter berdasarkan periode

---

### 6️⃣ LAPORAN HUTANG PIUTANG

**Untuk:** Keuangan & Owner  
**Frekuensi:** Mingguan/Bulanan

**A. PIUTANG (Hutang Customer)**

**Informasi yang Ditampilkan:**

- Daftar customer yang masih hutang
- Total piutang per customer
- Umur piutang (aging: 0-30 hari, 31-60 hari, >60 hari)
- Piutang yang (akan) jatuh tempo
- Piutang overdue

**Sumber Data:**

```sql
-- Daftar Piutang Customer
SELECT
    c.customer_id,
    c.customer_code,
    c.customer_name,
    c.phone,
    SUM(s.debt_amount) as total_piutang,
    COUNT(s.sale_id) as jumlah_invoice,
    MIN(s.sale_date) as invoice_tertua,
    MAX(s.sale_date) as invoice_terbaru
FROM SALES s
JOIN CUSTOMERS c ON s.customer_id = c.customer_id
WHERE s.debt_amount > 0
AND s.status IN ('pending', 'partial')
GROUP BY c.customer_id
ORDER BY total_piutang DESC;

-- Aging Analysis Piutang (dengan asumsi tempo 30 hari)
SELECT
    c.customer_name,
    s.invoice_number,
    s.sale_date,
    DATE_ADD(s.sale_date, INTERVAL 30 DAY) as due_date,
    DATEDIFF(CURDATE(), DATE_ADD(s.sale_date, INTERVAL 30 DAY)) as hari_lewat_tempo,
    s.total_amount,
    s.paid_amount,
    s.debt_amount,
    CASE
        WHEN DATEDIFF(CURDATE(), s.sale_date) <= 30 THEN '0-30 hari'
        WHEN DATEDIFF(CURDATE(), s.sale_date) <= 60 THEN '31-60 hari'
        WHEN DATEDIFF(CURDATE(), s.sale_date) <= 90 THEN '61-90 hari'
        ELSE '>90 hari'
    END as kategori_aging
FROM SALES s
JOIN CUSTOMERS c ON s.customer_id = c.customer_id
WHERE s.debt_amount > 0
AND s.status IN ('pending', 'partial')
ORDER BY s.sale_date ASC;

-- Piutang Overdue (lewat 30 hari dari tanggal transaksi)
SELECT
    c.customer_name,
    s.invoice_number,
    s.sale_date,
    DATEDIFF(CURDATE(), s.sale_date) as umur_piutang_hari,
    s.debt_amount
FROM SALES s
JOIN CUSTOMERS c ON s.customer_id = c.customer_id
WHERE s.debt_amount > 0
AND s.status IN ('pending', 'partial')
AND DATEDIFF(CURDATE(), s.sale_date) > 30
ORDER BY DATEDIFF(CURDATE(), s.sale_date) DESC;
```

**B. HUTANG (ke Supplier)**

**Informasi yang Ditampilkan:**

- Daftar hutang per supplier
- Total hutang per supplier
- Hutang yang akan jatuh tempo
- Proyeksi pembayaran

**Sumber Data:**

```sql
-- Daftar Hutang ke Supplier
SELECT
    sup.supplier_id,
    sup.supplier_code,
    sup.supplier_name,
    sup.phone,
    SUM(p.debt_amount) as total_hutang,
    COUNT(p.purchase_id) as jumlah_po,
    MIN(p.purchase_date) as po_tertua,
    MAX(p.purchase_date) as po_terbaru
FROM PURCHASES p
JOIN SUPPLIERS sup ON p.supplier_id = sup.supplier_id
WHERE p.debt_amount > 0
AND p.status IN ('pending', 'partial')
GROUP BY sup.supplier_id
ORDER BY total_hutang DESC;

-- Hutang yang Akan Jatuh Tempo (30 hari ke depan)
SELECT
    sup.supplier_name,
    p.purchase_number,
    p.purchase_date,
    DATE_ADD(p.purchase_date, INTERVAL 30 DAY) as due_date,
    DATEDIFF(DATE_ADD(p.purchase_date, INTERVAL 30 DAY), CURDATE()) as hari_sampai_jatuh_tempo,
    p.debt_amount
FROM PURCHASES p
JOIN SUPPLIERS sup ON p.supplier_id = sup.supplier_id
WHERE p.debt_amount > 0
AND p.status IN ('pending', 'partial')
AND DATE_ADD(p.purchase_date, INTERVAL 30 DAY) <= DATE_ADD(CURDATE(), INTERVAL 30 DAY)
ORDER BY due_date ASC;
```

**Tabel yang Digunakan:**

- `SALES` - Piutang customer (debt_amount > 0)
- `PURCHASES` - Hutang supplier (debt_amount > 0)
- `CUSTOMERS` - Info customer
- `SUPPLIERS` - Info supplier
- `PAYMENTS` - History pembayaran/pelunasan

**Catatan:**

- Jatuh tempo dihitung dari `sale_date/purchase_date + 30 hari` (bisa disesuaikan)
- Overdue = sudah lewat jatuh tempo tapi belum dibayar

---

### 7️⃣ LAPORAN STOK OPNAME

**Untuk:** Gudang & Owner  
**Frekuensi:** Bulanan atau sesuai jadwal opname

**Informasi yang Ditampilkan:**

- Hasil perhitungan fisik vs sistem
- Selisih per produk (shortage/excess)
- Nilai total selisih (Rp)
- Akurasi inventory (%)
- Produk dengan selisih besar

**Sumber Data:**

```sql
-- Laporan Hasil Stok Opname
SELECT
    so.opname_number,
    so.opname_date,
    u.full_name as petugas,
    p.product_code,
    p.product_name,
    c.category_name,
    sod.system_stock,
    sod.physical_stock,
    sod.difference,
    sod.adjustment_type,
    sod.unit_cost,
    sod.total_value,
    sod.reason
FROM STOCK_OPNAME_DETAILS sod
JOIN STOCK_OPNAMES so ON sod.opname_id = so.opname_id
JOIN PRODUCTS p ON sod.product_id = p.product_id
JOIN CATEGORIES c ON p.category_id = c.category_id
JOIN USERS u ON so.user_id = u.user_id
WHERE so.opname_id = 1  -- ID opname tertentu
ORDER BY ABS(sod.difference) DESC;

-- Summary Stok Opname
SELECT
    so.opname_number,
    so.opname_date,
    COUNT(sod.opname_detail_id) as total_produk_dihitung,
    SUM(CASE WHEN sod.difference = 0 THEN 1 ELSE 0 END) as produk_sesuai,
    SUM(CASE WHEN sod.difference < 0 THEN 1 ELSE 0 END) as produk_kurang,
    SUM(CASE WHEN sod.difference > 0 THEN 1 ELSE 0 END) as produk_lebih,
    SUM(CASE WHEN sod.adjustment_type = 'shortage' THEN sod.total_value ELSE 0 END) as nilai_shortage,
    SUM(CASE WHEN sod.adjustment_type = 'excess' THEN sod.total_value ELSE 0 END) as nilai_excess,
    SUM(sod.total_value) as nilai_net_selisih
FROM STOCK_OPNAMES so
JOIN STOCK_OPNAME_DETAILS sod ON so.opname_id = sod.opname_id
WHERE so.opname_id = 1
GROUP BY so.opname_id;

-- Akurasi Inventory
SELECT
    (SUM(CASE WHEN difference = 0 THEN 1 ELSE 0 END) / COUNT(*)) * 100 as akurasi_persen
FROM STOCK_OPNAME_DETAILS
WHERE opname_id = 1;
```

**Tabel yang Digunakan:**

- `STOCK_OPNAMES` - Header opname
- `STOCK_OPNAME_DETAILS` - Detail hasil hitungan per produk
- `PRODUCTS` - Info produk
- `CATEGORIES` - Kategori
- `USERS` - Petugas opname

---

### 8️⃣ LAPORAN PEMBELIAN

**Untuk:** Pembelian & Keuangan  
**Frekuensi:** Mingguan/Bulanan

**Informasi yang Ditampilkan:**

- Total pembelian per periode
- Pembelian per supplier
- Produk yang sering dibeli
- Trend harga beli

**Sumber Data:**

```sql
-- Total Pembelian per Periode
SELECT
    DATE_FORMAT(purchase_date, '%Y-%m') as periode,
    COUNT(*) as jumlah_po,
    SUM(total_amount) as total_pembelian,
    AVG(total_amount) as rata_rata_po
FROM PURCHASES
WHERE purchase_date >= '2024-01-01'
AND purchase_date < '2024-02-01'
GROUP BY DATE_FORMAT(purchase_date, '%Y-%m');

-- Pembelian per Supplier
SELECT
    sup.supplier_name,
    COUNT(p.purchase_id) as jumlah_po,
    SUM(p.total_amount) as total_pembelian,
    SUM(p.discount_amount) as total_diskon,
    AVG(p.total_amount) as rata_rata_po
FROM PURCHASES p
JOIN SUPPLIERS sup ON p.supplier_id = sup.supplier_id
WHERE p.purchase_date >= '2024-01-01'
AND p.purchase_date < '2024-02-01'
GROUP BY sup.supplier_id
ORDER BY total_pembelian DESC;

-- Produk yang Sering Dibeli
SELECT
    pr.product_name,
    c.category_name,
    COUNT(DISTINCT pd.purchase_id) as frekuensi_beli,
    SUM(pd.quantity) as total_quantity,
    AVG(pd.unit_cost) as rata_rata_harga_beli,
    MIN(pd.unit_cost) as harga_terendah,
    MAX(pd.unit_cost) as harga_tertinggi
FROM PURCHASE_DETAILS pd
JOIN PRODUCTS pr ON pd.product_id = pr.product_id
JOIN CATEGORIES c ON pr.category_id = c.category_id
JOIN PURCHASES p ON pd.purchase_id = p.purchase_id
WHERE p.purchase_date >= '2024-01-01'
AND p.purchase_date < '2024-02-01'
GROUP BY pr.product_id
ORDER BY total_quantity DESC
LIMIT 20;

-- Trend Harga Beli Produk
SELECT
    pr.product_name,
    p.purchase_date,
    pd.unit_cost,
    pd.quantity
FROM PURCHASE_DETAILS pd
JOIN PURCHASES p ON pd.purchase_id = p.purchase_id
JOIN PRODUCTS pr ON pd.product_id = pr.product_id
WHERE pr.product_id = 1  -- Produk tertentu
ORDER BY p.purchase_date DESC;
```

**Tabel yang Digunakan:**

- `PURCHASES` - Header pembelian
- `PURCHASE_DETAILS` - Detail produk yang dibeli
- `SUPPLIERS` - Info supplier
- `PRODUCTS` - Info produk
- `CATEGORIES` - Kategori

---

### 9️⃣ LAPORAN MARGIN & PROFITABILITAS PRODUK

**Untuk:** Owner & Manager  
**Frekuensi:** Bulanan

**Informasi yang Ditampilkan:**

- HPP vs Harga Jual per produk
- Margin per produk (Rp & %)
- Produk dengan margin tertinggi/terendah
- Kontribusi profit per produk

**Sumber Data:**

```sql
-- Margin & Profitabilitas per Produk
SELECT
    p.product_code,
    p.product_name,
    c.category_name,
    p.average_cost as hpp,
    p.selling_price as harga_jual,
    p.selling_price - p.average_cost as margin_rupiah,
    ((p.selling_price - p.average_cost) / p.selling_price) * 100 as margin_persen,
    COALESCE(SUM(sd.quantity), 0) as total_terjual,
    COALESCE(SUM(sd.subtotal), 0) as total_revenue,
    COALESCE(SUM(sd.profit), 0) as total_profit
FROM PRODUCTS p
JOIN CATEGORIES c ON p.category_id = c.category_id
LEFT JOIN SALES_DETAILS sd ON p.product_id = sd.product_id
LEFT JOIN SALES s ON sd.sale_id = s.sale_id
    AND s.sale_date >= '2024-01-01'
    AND s.sale_date < '2024-02-01'
    AND s.status IN ('paid', 'completed', 'partial')
WHERE p.is_active = true
GROUP BY p.product_id
ORDER BY total_profit DESC;

-- Produk dengan Margin Tertinggi
SELECT
    product_name,
    selling_price,
    average_cost,
    selling_price - average_cost as margin,
    ((selling_price - average_cost) / selling_price) * 100 as margin_pct
FROM PRODUCTS
WHERE is_active = true
AND selling_price > 0
ORDER BY margin_pct DESC
LIMIT 20;

-- Produk dengan Margin Terendah
-- (Query yang sama, ORDER BY margin_pct ASC)
```

**Tabel yang Digunakan:**

- `PRODUCTS` - HPP (average_cost), harga jual
- `SALES_DETAILS` - Profit aktual dari penjualan
- `SALES` - Filter periode
- `CATEGORIES` - Kategori produk

---

### 🔟 LAPORAN CUSTOMER TERBAIK

**Untuk:** Marketing & Owner  
**Frekuensi:** Bulanan

**Informasi yang Ditampilkan:**

- Top 20 customer by revenue
- Top 20 customer by frekuensi transaksi
- Perbandingan member vs non-member
- Customer retention

**Sumber Data:**

```sql
-- Top Customer by Revenue
SELECT
    c.customer_code,
    c.customer_name,
    cg.group_name,
    COUNT(s.sale_id) as jumlah_transaksi,
    SUM(s.total_amount) as total_belanja,
    AVG(s.total_amount) as rata_rata_transaksi,
    MIN(s.sale_date) as transaksi_pertama,
    MAX(s.sale_date) as transaksi_terakhir
FROM CUSTOMERS c
LEFT JOIN CUSTOMER_GROUPS cg ON c.group_id = cg.group_id
LEFT JOIN SALES s ON c.customer_id = s.customer_id
    AND s.sale_date >= '2024-01-01'
    AND s.sale_date < '2024-02-01'
    AND s.status IN ('paid', 'completed', 'partial')
GROUP BY c.customer_id
HAVING jumlah_transaksi > 0
ORDER BY total_belanja DESC
LIMIT 20;

-- Top Customer by Frekuensi
-- (Query yang sama, ORDER BY jumlah_transaksi DESC)

-- Perbandingan Member vs Non-Member
SELECT
    cg.group_name,
    COUNT(DISTINCT c.customer_id) as jumlah_customer,
    COUNT(s.sale_id) as jumlah_transaksi,
    SUM(s.total_amount) as total_revenue,
    AVG(s.total_amount) as rata_rata_transaksi
FROM CUSTOMER_GROUPS cg
LEFT JOIN CUSTOMERS c ON cg.group_id = c.group_id
LEFT JOIN SALES s ON c.customer_id = s.customer_id
    AND s.sale_date >= '2024-01-01'
    AND s.sale_date < '2024-02-01'
    AND s.status IN ('paid', 'completed', 'partial')
GROUP BY cg.group_id
ORDER BY total_revenue DESC;
```

**Tabel yang Digunakan:**

- `CUSTOMERS` - Data customer
- `CUSTOMER_GROUPS` - Grup/tier customer
- `SALES` - Transaksi penjualan

---

### 1️⃣1️⃣ LAPORAN INVENTORY TURNOVER

**Untuk:** Gudang & Pembelian  
**Frekuensi:** Bulanan

**Informasi yang Ditampilkan:**

- Inventory turnover ratio per produk
- Fast moving items (perputaran cepat)
- Slow moving items (perputaran lambat)
- Dead stock (>90 hari tidak bergerak)
- Nilai inventory yang menumpuk

**Sumber Data:**

```sql
-- Inventory Turnover per Produk
SELECT
    p.product_code,
    p.product_name,
    c.category_name,
    p.current_stock,
    p.average_cost,
    p.current_stock * p.average_cost as nilai_stok,
    COALESCE(SUM(sd.quantity), 0) as total_terjual_30hari,
    CASE
        WHEN p.current_stock > 0 AND SUM(sd.quantity) > 0
        THEN (p.current_stock / (SUM(sd.quantity) / 30))
        ELSE NULL
    END as days_in_inventory,
    CASE
        WHEN SUM(sd.quantity) > 0
        THEN (SUM(sd.quantity) / 30) * 30 / p.current_stock
        ELSE 0
    END as turnover_ratio_30hari
FROM PRODUCTS p
JOIN CATEGORIES c ON p.category_id = c.category_id
LEFT JOIN SALES_DETAILS sd ON p.product_id = sd.product_id
LEFT JOIN SALES s ON sd.sale_id = s.sale_id
    AND s.sale_date >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
    AND s.status IN ('paid', 'completed', 'partial')
WHERE p.is_active = true
GROUP BY p.product_id
ORDER BY turnover_ratio_30hari DESC;

-- Fast Moving Items (Turnover tinggi)
-- (Query di atas, filter: turnover_ratio_30hari > 2)

-- Slow Moving Items (Turnover rendah)
-- (Query di atas, filter: turnover_ratio_30hari < 1 AND turnover_ratio_30hari > 0)

-- Dead Stock (Tidak bergerak >90 hari)
SELECT
    p.product_code,
    p.product_name,
    p.current_stock,
    p.average_cost,
    p.current_stock * p.average_cost as nilai_stok,
    MAX(s.sale_date) as transaksi_terakhir,
    DATEDIFF(CURDATE(), MAX(s.sale_date)) as hari_tidak_bergerak

FROM PRODUCTS p
LEFT JOIN SALES_DETAILS sd ON p.product_id = sd.product_id
LEFT JOIN SALES s ON sd.sale_id = s.sale_id
    AND s.status IN ('paid', 'completed', 'partial')
WHERE p.is_active = true
AND p.current_stock > 0
GROUP BY p.product_id
HAVING hari_tidak_bergerak > 90 OR hari_tidak_bergerak IS NULL
ORDER BY nilai_stok DESC;
```

**Tabel yang Digunakan:**

- `PRODUCTS` - Current stock, average cost
- `SALES_DETAILS` - Data penjualan
- `SALES` - Filter status & periode
- `CATEGORIES` - Kategori

**Rumus:**

- **Turnover Ratio** = (Qty Terjual dalam periode) / (Average Inventory)
- **Days in Inventory** = (Current Stock / Avg Daily Sales)
- Fast Moving: Turnover > 2 kali per bulan
- Slow Moving: Turnover < 1 kali per bulan
- Dead Stock: Tidak terjual > 90 hari

---

### 1️⃣2️⃣ LAPORAN RETUR

**Untuk:** Quality Control & Pembelian  
**Frekuensi:** Bulanan

**A. RETUR PENJUALAN (dari Customer)**

**Informasi yang Ditampilkan:**

- Total nilai retur dari customer
- Produk yang sering diretur
- Alasan retur
- Persentase retur terhadap penjualan

**Sumber Data:**

```sql
-- Summary Retur Penjualan
SELECT
    COUNT(DISTINCT sr.return_id) as jumlah_retur,
    SUM(sr.total_return) as total_nilai_retur,
    (SELECT SUM(total_amount) FROM SALES
     WHERE sale_date >= '2024-01-01'
     AND sale_date < '2024-02-01'
     AND status IN ('paid', 'completed', 'partial')) as total_penjualan,
    (SUM(sr.total_return) / (SELECT SUM(total_amount) FROM SALES
     WHERE sale_date >= '2024-01-01'
     AND sale_date < '2024-02-01'
     AND status IN ('paid', 'completed', 'partial'))) * 100 as persentase_retur
FROM SALES_RETURNS sr
WHERE sr.return_date >= '2024-01-01'
AND sr.return_date < '2024-02-01'
AND sr.status = 'approved';

-- Produk yang Sering Diretur
SELECT
    p.product_code,
    p.product_name,
    c.category_name,
    COUNT(DISTINCT srd.return_id) as frekuensi_retur,
    SUM(srd.quantity) as total_qty_retur,
    SUM(srd.subtotal) as total_nilai_retur,
    GROUP_CONCAT(DISTINCT sr.reason SEPARATOR '; ') as alasan_retur
FROM SALES_RETURN_DETAILS srd
JOIN SALES_RETURNS sr ON srd.return_id = sr.return_id
JOIN PRODUCTS p ON srd.product_id = p.product_id
JOIN CATEGORIES c ON p.category_id = c.category_id
WHERE sr.return_date >= '2024-01-01'
AND sr.return_date < '2024-02-01'
AND sr.status = 'approved'
GROUP BY p.product_id
ORDER BY frekuensi_retur DESC, total_nilai_retur DESC
LIMIT 20;

-- Detail Retur Penjualan
SELECT
    sr.return_number,
    sr.return_date,
    s.invoice_number,
    c.customer_name,
    sr.total_return,
    sr.reason,
    sr.status
FROM SALES_RETURNS sr
JOIN SALES s ON sr.sale_id = s.sale_id
JOIN CUSTOMERS c ON s.customer_id = c.customer_id
WHERE sr.return_date >= '2024-01-01'
AND sr.return_date < '2024-02-01'
ORDER BY sr.return_date DESC;
```

**B. RETUR PEMBELIAN (ke Supplier)**

**Informasi yang Ditampilkan:**

- Total retur ke supplier
- Supplier dengan retur terbanyak
- Produk yang sering diretur
- Alasan retur

**Sumber Data:**

```sql
-- Summary Retur Pembelian
SELECT
    COUNT(DISTINCT pr.return_id) as jumlah_retur,
    SUM(pr.total_return) as total_nilai_retur
FROM PURCHASE_RETURNS pr
WHERE pr.return_date >= '2024-01-01'
AND pr.return_date < '2024-02-01'
AND pr.status = 'approved';

-- Retur per Supplier
SELECT
    sup.supplier_name,
    COUNT(DISTINCT pr.return_id) as frekuensi_retur,
    SUM(pr.total_return) as total_nilai_retur,
    GROUP_CONCAT(DISTINCT pr.reason SEPARATOR '; ') as alasan_retur
FROM PURCHASE_RETURNS pr
JOIN PURCHASES p ON pr.purchase_id = p.purchase_id
JOIN SUPPLIERS sup ON p.supplier_id = sup.supplier_id
WHERE pr.return_date >= '2024-01-01'
AND pr.return_date < '2024-02-01'
AND pr.status = 'approved'
GROUP BY sup.supplier_id
ORDER BY frekuensi_retur DESC;

-- Produk yang Sering Diretur ke Supplier
SELECT
    prod.product_code,
    prod.product_name,
    COUNT(DISTINCT prd.return_id) as frekuensi_retur,
    SUM(prd.quantity) as total_qty_retur,
    SUM(prd.subtotal) as total_nilai_retur
FROM PURCHASE_RETURN_DETAILS prd
JOIN PURCHASE_RETURNS pr ON prd.return_id = pr.return_id
JOIN PRODUCTS prod ON prd.product_id = prod.product_id
WHERE pr.return_date >= '2024-01-01'
AND pr.return_date < '2024-02-01'
AND pr.status = 'approved'
GROUP BY prod.product_id
ORDER BY frekuensi_retur DESC
LIMIT 20;
```

**Tabel yang Digunakan:**

- **SALES_RETURNS** - Header retur penjualan
- **SALES_RETURN_DETAILS** - Detail produk yang diretur
- **PURCHASE_RETURNS** - Header retur pembelian
- **PURCHASE_RETURN_DETAILS** - Detail produk yang diretur
- **SALES** - Transaksi asli
- **PURCHASES** - Pembelian asli
- **CUSTOMERS** - Info customer
- **SUPPLIERS** - Info supplier
- **PRODUCTS** - Info produk

---

## C. LAPORAN REAL-TIME

### 1️⃣3️⃣ DASHBOARD EKSEKUTIF

**Untuk:** Owner & Manager  
**Frekuensi:** Real-time / On-demand

**Informasi yang Ditampilkan:**

**A. Ringkasan Hari Ini:**

- Total penjualan hari ini
- Total profit hari ini
- Jumlah transaksi
- Rata-rata nilai transaksi
- Top 5 produk terlaris hari ini

**B. Ringkasan Bulan Ini:**

- Total penjualan bulan ini
- Growth vs bulan lalu (%)
- Total profit bulan ini
- Target penjualan & pencapaian (jika ada)

**C. Alert & Notifikasi:**

- Produk stok minimum (perlu reorder)
- Piutang overdue (lewat 30 hari)
- Hutang yang akan jatuh tempo (7 hari ke depan)
- Produk mendekati expired (30 hari ke depan)

**Sumber Data:**

```sql
-- A. HARI INI
-- Total Penjualan Hari Ini
SELECT
    COUNT(*) as jumlah_transaksi,
    SUM(total_amount) as total_penjualan,
    AVG(total_amount) as rata_rata_transaksi
FROM SALES
WHERE DATE(sale_date) = CURDATE()
AND status IN ('paid', 'completed', 'partial');

-- Total Profit Hari Ini
SELECT SUM(profit) as total_profit
FROM SALES_DETAILS sd
JOIN SALES s ON sd.sale_id = s.sale_id
WHERE DATE(s.sale_date) = CURDATE()
AND s.status IN ('paid', 'completed', 'partial');

-- Top 5 Produk Terlaris Hari Ini
SELECT
    p.product_name,
    SUM(sd.quantity) as qty_terjual,
    SUM(sd.subtotal) as revenue
FROM SALES_DETAILS sd
JOIN PRODUCTS p ON sd.product_id = p.product_id
JOIN SALES s ON sd.sale_id = s.sale_id
WHERE DATE(s.sale_date) = CURDATE()
AND s.status IN ('paid', 'completed', 'partial')
GROUP BY p.product_id
ORDER BY qty_terjual DESC
LIMIT 5;

-- B. BULAN INI
-- Total Penjualan Bulan Ini
SELECT
    SUM(total_amount) as penjualan_bulan_ini
FROM SALES
WHERE DATE_FORMAT(sale_date, '%Y-%m') = DATE_FORMAT(CURDATE(), '%Y-%m')
AND status IN ('paid', 'completed', 'partial');

-- Penjualan Bulan Lalu (untuk comparison)
SELECT
    SUM(total_amount) as penjualan_bulan_lalu
FROM SALES
WHERE DATE_FORMAT(sale_date, '%Y-%m') = DATE_FORMAT(DATE_SUB(CURDATE(), INTERVAL 1 MONTH), '%Y-%m')
AND status IN ('paid', 'completed', 'partial');

-- Growth Calculation (di aplikasi):
-- growth_pct = ((bulan_ini - bulan_lalu) / bulan_lalu) * 100

-- C. ALERT
-- Alert: Stok Minimum
SELECT
    product_code,
    product_name,
    current_stock,
    min_stock
FROM PRODUCTS
WHERE current_stock <= min_stock
AND is_active = true
ORDER BY (min_stock - current_stock) DESC
LIMIT 10;

-- Alert: Piutang Overdue (asumsi tempo 30 hari)
SELECT
    c.customer_name,
    s.invoice_number,
    s.sale_date,
    DATEDIFF(CURDATE(), s.sale_date) as hari_lewat,
    s.debt_amount
FROM SALES s
JOIN CUSTOMERS c ON s.customer_id = c.customer_id
WHERE s.debt_amount > 0
AND s.status IN ('pending', 'partial')
AND DATEDIFF(CURDATE(), s.sale_date) > 30
ORDER BY DATEDIFF(CURDATE(), s.sale_date) DESC
LIMIT 10;

-- Alert: Hutang Jatuh Tempo (7 hari ke depan, asumsi tempo 30 hari)
SELECT
    sup.supplier_name,
    p.purchase_number,
    p.purchase_date,
    DATE_ADD(p.purchase_date, INTERVAL 30 DAY) as due_date,
    p.debt_amount
FROM PURCHASES p
JOIN SUPPLIERS sup ON p.supplier_id = sup.supplier_id
WHERE p.debt_amount > 0
AND p.status IN ('pending', 'partial')
AND DATE_ADD(p.purchase_date, INTERVAL 30 DAY) BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 7 DAY)
ORDER BY due_date ASC;

-- Alert: Produk Mendekati Expired (30 hari ke depan)
SELECT
    p.product_code,
    p.product_name,
    pb.batch_number,
    pb.expiry_date,
    pb.current_quantity,
    DATEDIFF(pb.expiry_date, CURDATE()) as hari_sampai_expired
FROM PRODUCT_BATCHES pb
JOIN PRODUCTS p ON pb.product_id = p.product_id
WHERE pb.status = 'available'
AND pb.current_quantity > 0
AND pb.expiry_date IS NOT NULL
AND pb.expiry_date BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 30 DAY)
ORDER BY pb.expiry_date ASC
LIMIT 10;
```

**Tabel yang Digunakan:**

- `SALES` - Penjualan
- `SALES_DETAILS` - Detail & profit
- `PRODUCTS` - Stok minimum
- `PRODUCT_BATCHES` - Expired date
- `CUSTOMERS` - Info customer (piutang)
- `SUPPLIERS` - Info supplier (hutang)
- `PURCHASES` - Hutang ke supplier

---

## 📋 RINGKASAN TABEL YANG DIGUNAKAN

| Laporan             | Tabel Utama                                                                                                    |
| ------------------- | -------------------------------------------------------------------------------------------------------------- |
| Penjualan Harian    | SALES, SALES_DETAILS                                                                                           |
| Kas Harian          | SALES, PURCHASES, PAYMENTS                                                                                     |
| Stok Minimum        | PRODUCTS, CATEGORIES                                                                                           |
| Laba Rugi           | SALES, SALES_DETAILS                                                                                           |
| Produk Terlaris     | SALES_DETAILS, PRODUCTS, CATEGORIES, SALES                                                                     |
| Hutang Piutang      | SALES, PURCHASES, CUSTOMERS, SUPPLIERS, PAYMENTS                                                               |
| Stok Opname         | STOCK_OPNAMES, STOCK_OPNAME_DETAILS, PRODUCTS, USERS                                                           |
| Pembelian           | PURCHASES, PURCHASE_DETAILS, SUPPLIERS, PRODUCTS                                                               |
| Margin & Profit     | PRODUCTS, SALES_DETAILS, CATEGORIES, SALES                                                                     |
| Customer Terbaik    | CUSTOMERS, CUSTOMER_GROUPS, SALES                                                                              |
| Inventory Turnover  | PRODUCTS, SALES_DETAILS, SALES, CATEGORIES                                                                     |
| Retur               | SALES_RETURNS, SALES_RETURN_DETAILS, PURCHASE_RETURNS, PURCHASE_RETURN_DETAILS, PRODUCTS, SUPPLIERS, CUSTOMERS |
| Dashboard Eksekutif | SALES, SALES_DETAILS, PRODUCTS, PRODUCT_BATCHES, CUSTOMERS, SUPPLIERS, PURCHASES                               |

---

## ⚡ TIPS IMPLEMENTASI

### **1. Optimasi Query dengan Index**

```sql
-- Index untuk performa query laporan
CREATE INDEX idx_sales_date ON SALES(sale_date);
CREATE INDEX idx_sales_status ON SALES(status);
CREATE INDEX idx_sales_customer ON SALES(customer_id);
CREATE INDEX idx_sales_details_sale ON SALES_DETAILS(sale_id);
CREATE INDEX idx_sales_details_product ON SALES_DETAILS(product_id);
CREATE INDEX idx_products_active ON PRODUCTS(is_active);
CREATE INDEX idx_product_batches_expiry ON PRODUCT_BATCHES(expiry_date);
CREATE INDEX idx_payments_date ON PAYMENTS(payment_date);
CREATE INDEX idx_purchases_date ON PURCHASES(purchase_date);
```

### **2. Gunakan View untuk Query yang Sering Dipakai**

```sql
-- View untuk penjualan harian
CREATE VIEW v_daily_sales AS
SELECT
    DATE(sale_date) as sale_date,
    COUNT(*) as total_transactions,
    SUM(total_amount) as total_sales,
    AVG(total_amount) as avg_transaction
FROM SALES
WHERE status IN ('paid', 'completed', 'partial')
GROUP BY DATE(sale_date);

-- Cara pakai:
SELECT * FROM v_daily_sales WHERE sale_date = CURDATE();
```

### **3. Cache untuk Dashboard Real-time**

- Data dashboard bisa di-cache 5-15 menit
- Refresh otomatis atau manual
- Untuk data critical (kas) harus real-time

### **4. Export Format**

Semua laporan sebaiknya bisa di-export ke:

- ✅ PDF (untuk print)
- ✅ Excel (untuk analisis lebih lanjut)
- ✅ CSV (untuk import ke sistem lain)

### **5. Periode Default**

- Laporan harian: Hari ini
- Laporan mingguan: 7 hari terakhir
- Laporan bulanan: Bulan berjalan
- Dengan opsi custom date range

---

## ✅ CHECKLIST LAPORAN

**Harian:**

- [x] Penjualan Harian
- [x] Kas Harian
- [x] Stok Minimum

**Mingguan/Bulanan:**

- [x] Laba Rugi
- [x] Produk Terlaris
- [x] Hutang Piutang
- [x] Stok Opname
- [x] Pembelian
- [x] Margin & Profitabilitas
- [x] Customer Terbaik
- [x] Inventory Turnover
- [x] Retur

**Real-time:**

- [x] Dashboard Eksekutif

**TOTAL: 13 Laporan Penting** yang mencakup semua aspek operasional minimarket! 🎉</parameter>

```

```
