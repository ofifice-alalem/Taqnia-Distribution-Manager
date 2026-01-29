<?php

namespace App\Http\Controllers\Warehouse\Sales;

use App\Http\Controllers\Controller;
use App\Services\SalesConfirmationService;
use App\Models\Sales\SalesInvoice;
use Illuminate\Http\Request;

class SalesConfirmationController extends Controller
{
    protected $confirmationService;

    public function __construct(SalesConfirmationService $confirmationService)
    {
        $this->confirmationService = $confirmationService;
    }

    public function index()
    {
        $pendingInvoices = SalesInvoice::with(['marketer', 'store', 'items.product'])
            ->where('status', 'pending')
            ->orderBy('created_at', 'desc')
            ->get();

        $approvedInvoices = SalesInvoice::with(['marketer', 'store', 'keeper', 'items.product'])
            ->where('status', 'approved')
            ->orderBy('confirmed_at', 'desc')
            ->get();

        $pendingCount = $pendingInvoices->count();
        $approvedCount = $approvedInvoices->count();

        return view('warehouse.sales.index', compact(
            'pendingInvoices', 'approvedInvoices',
            'pendingCount', 'approvedCount'
        ));
    }

    public function show($id)
    {
        $invoice = SalesInvoice::with(['marketer', 'store', 'keeper', 'items.product'])
            ->findOrFail($id);

        return view('warehouse.sales.show', compact('invoice'));
    }

    public function confirm(Request $request, $invoiceId)
    {
        $validated = $request->validate([
            'keeper_id' => 'required|exists:users,id',
            'stamped_invoice_image' => 'required|image|max:5120',
        ]);

        try {
            $invoice = $this->confirmationService->confirmSale(
                $invoiceId,
                $validated['keeper_id'],
                $request->file('stamped_invoice_image')
            );

            return redirect()->route('warehouse.sales.index')->with('success', 'تم توثيق البيع بنجاح');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}
