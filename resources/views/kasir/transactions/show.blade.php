@extends('layouts.kasir')

@section('content')
<div class="col-lg-4">
            <div class="payment-sidebar">
                <h5 class="mb-4">Pembayaran</h5>
                <form class="payment-form" action="{{ route('kasir.transactions.store') }}" method="POST">
                    @csrf
                    <div class="form-group">
                        <label>Uang Tunai Diterima</label>
                        <input type="number" name="cash_received" id="cash_received" class="form-control" min="0"
                            step="0.01" oninput="calculateChange()" required>
                    </div>

                    <div class="form-group">
                        <label>Kembalian</label>
                        <input type="text" id="change" class="form-control" readonly>
                    </div>

                    <div class="payment-total">
                        <label>Total Pembayaran</label>
                        <input type="text" id="total_payment" name="total_payment" class="form-control text-center"
                            readonly>
                    </div>

                    <input type="hidden" id="products_data" name="products_data">
                    <button type="submit" class="btn btn-primary btn-lg w-100">Proses Pembayaran</button>
                </form>
            </div>
        </div>
@endsection