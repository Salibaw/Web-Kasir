@extends('layouts.admin')

<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

@section('content')
<style>
  body{
    overflow-y: auto;
  }

  .dashboard-container {
      margin-top: 80px;
      padding: 20px;
  }

  .stat-card {
      background-color: white;
      border-radius: 15px;
      transition: transform 0.3s;
      box-shadow: 0 4px 6px rgba(0,0,0,0.1);
  }

  .stat-card:hover {
      transform: translateY(-5px);
  }

  .stat-icon {
      font-size: 2.5rem;
      margin-bottom: 1rem;
  }

  .stat-title {
      color: black; /* Mengubah warna judul statistik menjadi putih */
      font-size: 1.1rem;
      font-weight: 600;
  }

  .stat-value {
      color: black; /* Mengubah warna nilai statistik menjadi putih */
      font-size: 1.8rem;
      font-weight: 700;
  }

  .chart-container {
      background: #00328E;
      border-radius: 15px;
      padding: 20px;
      margin-top: 2rem;
      box-shadow: 0 4px 6px rgba(0,0,0,0.1);
      color: white; /* Mengubah semua tulisan di dalam chart-container menjadi putih */
  }

  .chart-container h5 {
      color: white; /* Mengubah warna judul chart menjadi putih */
  }

  @media (max-width: 768px) {
      .dashboard-container {
          margin-top: 70px;
          padding: 15px;
      }

      .stat-card {
          margin-bottom: 15px;
      }

      .stat-icon {
          font-size: 2rem;
      }

      .stat-value {
          font-size: 1.5rem;
      }
  }
</style>

<div class="container-fluid dashboard-container">
  <h3 class="mb-4">Dashboard</h3>
    <div class="row">
        <!-- Statistik Pengguna -->
        <div class="col-12 col-md-6 col-xl-3 mb-4">
            <div class="stat-card h-100 p-4">
                <div class="text-center">
                    <i class="fas fa-users stat-icon text-primary"></i>
                    <h5 class="stat-title">Total Users</h5>
                    <p class="stat-value mb-0">{{ $userCount }}</p>
                </div>
            </div>
        </div>

        <!-- Statistik Produk -->
        <div class="col-12 col-md-6 col-xl-3 mb-4">
            <div class="stat-card h-100 p-4">
                <div class="text-center">
                    <i class="fas fa-box stat-icon text-success"></i>
                    <h5 class="stat-title">Total Products</h5>
                    <p class="stat-value mb-0">{{ $productCount }}</p>
                </div>
            </div>
        </div>

        <!-- Statistik Penjualan -->
        <div class="col-12 col-md-6 col-xl-3 mb-4">
            <div class="stat-card h-100 p-4">
                <div class="text-center">
                    <i class="fas fa-shopping-cart stat-icon text-info"></i>
                    <h5 class="stat-title">Total Sales</h5>
                    <p class="stat-value mb-0">{{ $totalSales }}</p>
                </div>
            </div>
        </div>

        <!-- Statistik Pendapatan -->
        <div class="col-12 col-md-6 col-xl-3 mb-4">
            <div class="stat-card h-100 p-4">
                <div class="text-center">
                    <i class="fas fa-dollar-sign stat-icon text-warning"></i>
                    <h5 class="stat-title">Total Revenue</h5>
                    <p class="stat-value mb-0">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <!-- Grafik Penjualan Bulanan -->
        <div class="col-12 col-lg-8 mb-4">
          <div class="chart-container">
            <h5 class="mb-4">Monthly Sales Overview</h5>
            <canvas id="salesChart" height="300"></canvas>
        </div>
        </div>

        <!-- Grafik Produk Terlaris -->
        <div class="col-12 col-lg-4">
            <div class="chart-container">
                <h5 class="mb-4">Top Selling Products</h5>
                <canvas id="productsChart" height="300"></canvas>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-12">
            <div class="chart-container">
                <h5 class="mb-4">Recent Transactions</h5>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Product</th>
                                <th>Amount</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            {{-- Add your recent transactions data here --}}
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

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
                  label: 'Penjualan Bulanan',
                  data: data,
                  borderColor: 'white',
                  fill: false,
              }],
          },
          options: {
              responsive: true,
              scales: {
                  x: {
                      title: {
                          display: true,
                          text: 'Bulan',
                          color: 'white', // Mengubah warna teks sumbu X menjadi putih
                      },
                      ticks: {
                          color: 'white', // Mengubah warna label sumbu X menjadi putih
                      }
                  },
                  y: {
                      title: {
                          display: true,
                          text: 'Pendapatan',
                          color: 'white', // Mengubah warna teks sumbu Y menjadi putih
                      },
                      ticks: {
                          color: 'white', // Mengubah warna label sumbu Y menjadi putih
                      }
                  }
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
                  label: 'Produk Terlaris',
                  data: productData,
                  backgroundColor: 'white',
                  borderColor: 'white',
                  borderWidth: 1,
              }],
          },
          options: {
              responsive: true,
              scales: {
                  x: {
                      title: {
                          display: true,
                          text: 'Produk',
                          color: 'white', // Mengubah warna teks sumbu X menjadi putih
                      },
                      ticks: {
                          color: 'white', // Mengubah warna label sumbu X menjadi putih
                      }
                  },
                  y: {
                      title: {
                          display: true,
                          text: 'Jumlah Terjual',
                          color: 'white', // Mengubah warna teks sumbu Y menjadi putih
                      },
                      ticks: {
                          color: 'white', // Mengubah warna label sumbu Y menjadi putih
                      }
                  }
              }
          }
      });
  });
</script>
