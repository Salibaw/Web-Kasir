@extends('layouts.kasir')

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
<script src="https://cdn.tailwindcss.com"></script>

@section('content')
<body class="bg-gray-100 text-gray-800">
    <div class="container mx-auto px-4 py-6">
        <h2 class="text-2xl font-bold mb-6">Dashboard</h2>

        <!-- Statistik Section -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            <div class="bg-white shadow-lg rounded-lg p-6 text-center hover:shadow-xl transition">
                <i class="fas fa-box text-4xl text-blue-500 mb-4"></i>
                <h3 class="text-lg font-semibold mb-2">Total Products</h3>
                <p class="text-2xl font-bold">{{ $productCount }}</p>
            </div>

            <div class="bg-white shadow-lg rounded-lg p-6 text-center hover:shadow-xl transition">
                <i class="fas fa-shopping-cart text-4xl text-green-500 mb-4"></i>
                <h3 class="text-lg font-semibold mb-2">Total Sales</h3>
                <p class="text-2xl font-bold">{{ $totalSales }}</p>
            </div>

            <div class="bg-white shadow-lg rounded-lg p-6 text-center hover:shadow-xl transition">
                <i class="fas fa-dollar-sign text-4xl text-yellow-500 mb-4"></i>
                <h3 class="text-lg font-semibold mb-2">Total Revenue</h3>
                <p class="text-2xl font-bold">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</p>
            </div>
        </div>

        <!-- Grafik Section -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mt-8">
            <div class="lg:col-span-2 bg-white shadow-lg rounded-lg p-6">
                <h3 class="text-lg font-semibold mb-4">Monthly Sales Overview</h3>
                <canvas id="salesChart" height="100"></canvas>
            </div>

            <div class="bg-white shadow-lg rounded-lg p-6">
                <h3 class="text-lg font-semibold mb-4">Top Selling Products</h3>
                <canvas id="productsChart" height="100"></canvas>
            </div>
        </div>

        <!-- Recent Transactions -->
        <div class="bg-white shadow-lg rounded-lg p-6 mt-8">
            <h3 class="text-lg font-semibold mb-4">Recent Transactions</h3>
            <div class="overflow-x-auto">
                <table class="min-w-full table-auto border-collapse border border-gray-200">
                    <thead>
                        <tr class="bg-gray-50">
                            <th class="px-4 py-2 border">Date</th>
                            <th class="px-4 py-2 border">Product</th>
                            <th class="px-4 py-2 border">Amount</th>
                            <th class="px-4 py-2 border">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        {{-- Add your recent transactions data here --}}
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Data untuk grafik penjualan bulanan
            const monthlySales = @json($monthlySales);
            const labels = Object.keys(monthlySales);
            const data = Object.values(monthlySales);

            const ctx1 = document.getElementById('salesChart').getContext('2d');
            const salesChart = new Chart(ctx1, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Monthly Sales',
                        data: data,
                        borderColor: '#3B82F6',
                        backgroundColor: 'rgba(59, 130, 246, 0.2)',
                        fill: true,
                        tension: 0.3
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: { display: false },
                    },
                    scales: {
                        x: {
                            title: {
                                display: true,
                                text: 'Month',
                            },
                        },
                        y: {
                            title: {
                                display: true,
                                text: 'Revenue',
                            },
                        },
                    }
                }
            });

            // Data untuk grafik produk terlaris
            const topProducts = @json($topProducts);
            const productLabels = Object.keys(topProducts);
            const productData = Object.values(topProducts);

            const ctx2 = document.getElementById('productsChart').getContext('2d');
            const productsChart = new Chart(ctx2, {
                type: 'bar',
                data: {
                    labels: productLabels,
                    datasets: [{
                        label: 'Top Products',
                        data: productData,
                        backgroundColor: '#FBBF24',
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: { display: false },
                    },
                    scales: {
                        x: {
                            title: {
                                display: true,
                                text: 'Product',
                            },
                        },
                        y: {
                            title: {
                                display: true,
                                text: 'Sold Quantity',
                            },
                        },
                    }
                }
            });
        });
    </script>
</body>
@endsection