<!DOCTYPE html>
<html>

<head>
    <style>
    body {
        font-family: Arial, sans-serif;
        font-size: 12px;
    }

    .receipt-container {
        max-width: 400px;
        margin: auto;
        padding: 20px;
        border: 1px solid #ddd;
        background: #fff;
    }

    .receipt-header {
        text-align: center;
        margin-bottom: 20px;
    }

    .receipt-header h4 {
        margin: 0;
    }

    .receipt-body table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 20px;
    }

    .receipt-body th,
    .receipt-body td {
        padding: 8px;
        border: 1px solid #ddd;
        text-align: left;
    }

    .total-container {
        text-align: right;
        margin-top: 10px;
    }
    </style>
</head>

<body>
    <div class="receipt-container">
        <div class="receipt-header">
            <h4>Basmalah</h4>
            <p>Alamat Toko<br>Telepon: (021) 123-4567</p>
        </div>

        <div class="receipt-body">
            <p><strong>Tanggal:</strong> {{ \Carbon\Carbon::now()->format('d/m/Y H:i') }}</p>
            <p><strong>Kasir:</strong> {{ auth()->user()->name }}</p>

            <table>
                <thead>
                    <tr>
                        <th>Produk</th>
                        <th>Jumlah</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($transaction as $item)
                    <tr>
                        <td>{{ $item->product->name }}</td>
                        <td>{{ $item->quantity }}</td>
                        <td>Rp{{ number_format($item->total, 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="total-container">
                <p><strong>Total Semua Transaksi: Rp{{ number_format($transaction->sum('total'), 2) }}</strong></p>
                <p><strong>Bayar: Rp{{ number_format($transaction->sum('cash_received'), 2) }}</strong></p>
                <p><strong>Kembalian: Rp{{ number_format($transaction->sum('change'), 2) }}</strong></p>
            </div>
        </div>
        <div class="receipt-footer">
            <p>Terima Kasih Telah Berbelanja!</p>
            <p>Semoga Hari Anda Menyenangkan</p>
        </div>
    </div>
</body>

</html>