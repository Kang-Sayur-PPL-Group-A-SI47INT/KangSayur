

ace App\Http\Controllers\Farmer

p\Http\Controllers\Controller;
p\Models\Transaction;
luminate\Http\RedirectResponse;
luminate\Http\Request;
luminate\View\View;

OrderController extends Controller

blic function index(): View
{
    $transactions = Transaction::where('farmer_id', auth()->id())->get();
    return view('farmer.orders.index', compact('transactions'));
}   