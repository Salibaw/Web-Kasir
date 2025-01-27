<style>
    body {
        font-family: Arial, sans-serif;
        font-size: 14px;
        margin: 0;
        padding: 0;
    }

    .receipt-container {
        max-width: 100%;
        width: 400px;
        margin: 20px auto;
        padding: 20px;
        border: 1px solid #ddd;
        border-radius: 8px;
        background: #fff;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .receipt-header {
        text-align: center;
        margin-bottom: 20px;
    }

    .receipt-header h4 {
        margin: 0;
    }

    .receipt-body {
        margin-bottom: 20px;
    }

    .receipt-body table {
        width: 100%;
        border-collapse: collapse;
    }

    .receipt-body th,
    .receipt-body td {
        padding: 8px;
        text-align: left;
        border-bottom: 1px solid #ddd;
    }

    .receipt-footer {
        text-align: center;
        margin-top: 20px;
    }

    .receipt-footer p {
        margin: 0;
    }

    .total-container {
        text-align: right;
        margin-top: 10px;
    }

    .total-container strong {
        font-size: 16px;
    }

    .button-container {
        text-align: center;
        margin-top: 20px;
    }

    .button-container a {
        display: inline-block;
        margin: 0 10px;
        padding: 10px 20px;
        border-radius: 5px;
        text-decoration: none;
        color: #fff;
    }

    .button-back {
        background-color: #007bff;
    }

    .button-download {
        background-color: #28a745;
    }

    /* Responsiveness for smaller screens */
    @media (max-width: 600px) {
        .receipt-container {
            width: 90%;
            padding: 10px;
        }
    }
</style>

<div class="receipt-container">
    <div class="receipt-header">
        <h4>Basmalah</h4>
        <p>Alamat Toko<br>Telepon: (021) 123-4567</p>
    </div>

    <div class="receipt-body">
        <p><strong>Tanggal:</strong> {{ \Carbon\Carbon::now()->format('d/m/Y H:i') }}</p>
        <p><strong>Kasir:</strong> {{ $sale->user->name }}</p>
        <p><strong>Waktu Transaksi:</strong> {{ $sale->created_at->format('d-m-Y H:i:s') }}</p>

        <h2>Daftar Barang</h2>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Nama Produk</th>
                    <th>Harga</th>
                    <th>Jumlah</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($transactions as $transaction)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $transaction->product->name }}</td>
                        <td>Rp{{ number_format($transaction->price, 0, ',', '.') }}</td>
                        <td>{{ $transaction->quantity }}</td>
                        <td>Rp{{ number_format($transaction->total, 0, ',', '.') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="total-container">
            <p><strong>Total Semua Transaksi: Rp{{ number_format($transactions->sum('total'), 2, ',', '.') }}</strong>
            </p>
            <p><strong>Uang Diterima:
                    Rp{{ number_format($transactions->first()->cash_received ?? 0, 2, ',', '.') }}</strong></p>
            <p><strong>Kembalian:
                    Rp{{ number_format($transactions->first()->cash_received - $transactions->sum('total'), 2, ',', '.') }}</strong>
            </p>
            
        </div>
    </div>

    <div class="receipt-footer">
        <p>Terima Kasih Telah Berbelanja!</p>
        <p>Semoga Hari Anda Menyenangkan</p>
    </div>
</div>