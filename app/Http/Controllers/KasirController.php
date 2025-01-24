<?php

// KasirController.php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\Barang;
use App\Models\Product;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class KasirController extends Controller
{
    public function dashboard()
    {
        $transactions = Transaction::latest()->get();

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

        return view('kasir.dashboard', compact('productCount', 'salesCount', 'totalRevenue', 'totalSales', 'monthlySales', 'topProducts'));
    }
    public function index()
    {
        $transactions = Transaction::with('product')->get();
        // dd($transactions);
        
        // $transactions = Transaction::paginate(3);
        return view('kasir.transactions.show', compact('transactions'));
    }

    public function create()
    {
        $products = Product::all();
        $products = Product::paginate(8);
        return view('kasir.transactions.create', compact('products'));
    }

    public function store(Request $request)
{
    $productsData = json_decode($request->products_data, true);

    if (!is_array($productsData)) {
        return redirect()->back()->with('error', 'Data produk tidak valid.');
    }

    $totalPayment = 0;
    $lastTransactionId = null;

    foreach ($productsData as $productData) {
        $product = Product::findOrFail($productData['id']);

        if ($product->stock < $productData['quantity']) {
            return redirect()->back()->with('error', 'Stok tidak mencukupi untuk produk ' . $product->name);
        }

        $product->stock -= $productData['quantity'];
        $product->save();

        $transaction = new Transaction();
        $transaction->product_id = $product->id;
        $transaction->quantity = $productData['quantity'];
        $transaction->price = $product->price;
        $transaction->total = $product->price * $productData['quantity'];
        $transaction->cash_received = $request->cash_received;
        $transaction->change = $transaction->cash_received - $transaction->total;
        $transaction->save();

        $totalPayment += $transaction->total;
        $lastTransactionId = $transaction->id;
    }

    return redirect()->route('kasir.transactions.show', ['id' => $lastTransactionId]);
}

    public function show($id)
    {
        $transactions = Transaction::where('id', $id)->get();
        return view('kasir.transactions.show', compact('transactions','id'));
    }
    
    private function calculateChange($totalPrice, $cashReceived)
    {
        return $cashReceived - $totalPrice;
    }

    public function stockReport()
    {
        $stocks = Product::all();
        $stocks = Product::paginate(3);
        return view('kasir.reports.stock', compact('stocks'));
    }

    public function destroy($id)
    {
        $transaction = Transaction::findOrFail($id);
        $transaction->delete();

        return redirect()->route('kasir.transactions.index')->with('success', 'Product deleted successfully');
    }

    public function search(Request $request)
    {
        $search = $request->input('search');

        $products = Product::query()
            ->where('name', 'LIKE', "%{$search}%")
            ->orWhere('kode_barang', 'LIKE', "%{$search}%")
            ->paginate(3);

        return view('kasir.transactions.create', compact('products', 'search'));
    }

    public function stocksearch(Request $request)
    {
        $search = $request->input('search');
        $stocks = Product::Where('name', 'LIKE', "%{$search}%")->paginate(3);

        return view('kasir.reports.stocks', compact('stocks'));
    }

    public function downloadPdf($id)
    {
        $transaction = Transaction::where('id', $id)->get();
        $pdf = PDF::loadView('kasir.transactions.pdf', compact('transaction'));
    
        return $pdf->download('transaction_receipt_' . $id . '.pdf');
    }
        
    public function searchpdct(Request $request)
    {
        $search = $request->input('search');

        $products = Product::query()
            ->where('name', 'LIKE', "%{$search}%")
            ->orWhere('kode_barang', 'LIKE', "%{$search}%")
            ->paginate(3);

        return view('kasir.products.index', compact('products', 'search'));

    }

    public function showProfilee()
    {
        return view('admin.profile.index', ['user' => Auth::user()]);
    }

    public function updateProfilee(Request $request)
    {
        $userId = Auth::id();
        $user = User::find($userId);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $userId,
            'current_password' => 'nullable|required_with:new_password',
            'new_password' => 'nullable|min:8|confirmed',
            'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        // Update basic info
        $updateData = [
            'name' => $request->name,
            'email' => $request->email,

        ];

        // Handle password update
        if ($request->filled('current_password')) {
            if (!Hash::check($request->current_password, $user->password)) {
                return back()->withErrors(['current_password' => 'Password saat ini tidak cocok']);
            }
            $updateData['password'] = Hash::make($request->new_password);
        }

        // Handle profile picture upload
        if ($request->hasFile('profile_picture')) {
            // Delete old profile picture if exists
            if ($user->profile_picture && Storage::exists('public/profile_pictures/' . $user->profile_picture)) {
                Storage::delete('public/profile_pictures/' . $user->profile_picture);
            }

            // Store new profile picture
            $fileName = time() . '_' . $request->file('profile_picture')->getClientOriginalName();
            $request->file('profile_picture')->storeAs('public/profile_pictures', $fileName);
            $updateData['profile_picture'] = $fileName;
        }

        // Update the user
        User::where('id', $userId)->update($updateData);

        return back()->with('success', 'Profil berhasil diperbarui');
    }


}