<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\User;
use App\Models\Product;
use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Models\FinancialReport;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\ProductImport;  
class AdminController extends Controller
{

    public function dashboard()
{
    $transactions = Transaction::latest()->get();
    $userCount = User::count(); 
    $productCount = Product::count();
    $salesCount = Sale::count(); 
    $totalRevenue = Sale::sum('price'); 
    $totalSales = $transactions->sum('quantity');
    $totalRevenue = $transactions->sum('total');
    $monthlySales = Transaction::selectRaw('MONTH(created_at) as month, SUM(total) as total')
        ->groupBy('month')
        ->pluck('total', 'month');

    $topProducts = Transaction::join('products', 'transactions.product_id', '=', 'products.id')
        ->selectRaw('products.name, SUM(transactions.quantity) as total_quantity')
        ->groupBy('products.name')
        ->orderByDesc('total_quantity')
        ->take(3)
        ->pluck('total_quantity', 'products.name');

    return view('admin.dashboard', compact('userCount', 'productCount', 'salesCount', 'totalRevenue', 'totalSales', 'monthlySales', 'topProducts'));
}



    public function manageUsers()
    {
            $users = User::all();

            $users = User::paginate(4); 
            return view('admin.users', compact('users'));


    }

    public function updateRole(Request $request, $id)
    {
        $request->validate([
            'role' => ['required', Rule::in(['admin', 'pengguna', 'petugas_kasir', 'petugas_barang'])],
        ]);

        $user = User::findOrFail($id);
        $user->role = $request->input('role');
        $user->save();

        return redirect()->route('admin.users')->with('success', 'Role updated successfully');
    }

    public function storeUser(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
           
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            
        ]);

        return redirect()->route('admin.users')->with('success', 'User created successfully');
    }

    public function editUser($id)
    {
        $user = User::findOrFail($id);
        return view('admin.edit-user', compact('user'));
    }

    public function updateUser(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('users')->ignore($id)],
            'role' => ['required', Rule::in(['admin','petugas_kasir'])],
        ]);

        $user = User::findOrFail($id);
        $user->name = $request->name;
        $user->email = $request->email;
        $user->role = $request->role;

        if ($request->filled('password')) {
            $request->validate([
                'password' => 'nullable|string|min:8|confirmed',
            ]);
            $user->password = Hash::make($request->password);
        }

        $user->save();

        return redirect()->route('admin.users')->with('success', 'User updated successfully');
    }

    public function deleteUser($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return redirect()->route('admin.users')->with('success', 'User deleted successfully');
    }   


    public function salesReport(){
        $sales = Sale::with('product')->get(); 

        return view('admin.reports.sales', compact('sales'));
    }

    public function stockReport()
{
    $stocks = Product::all();
    $stocks = Product::paginate(3);
    return view('admin.reports.stock', compact('stocks'));
}

public function financialReport()
{
     $finances = FinancialReport::all();
    
    return view('admin.reports.financial', compact('finances'));
}

public function searchusers(Request $request)
{
    $query = $request->input('query');

    $users = User::where('name', 'LIKE', "%{$query}%")
        ->orWhere('email', 'LIKE', "%{$query}%")->paginate(4);
      

    return view('admin.users', compact('users'));
}

}