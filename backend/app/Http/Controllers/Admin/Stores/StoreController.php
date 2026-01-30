<?php

namespace App\Http\Controllers\Admin\Stores;

use App\Http\Controllers\Controller;
use App\Models\Store\Store;
use App\Models\Debt\StoreDebtLedger;
use Illuminate\Support\Facades\DB;

class StoreController extends Controller
{
    public function index()
    {
        $stores = Store::select('stores.*')
            ->leftJoin('store_debt_ledger', 'stores.id', '=', 'store_debt_ledger.store_id')
            ->selectRaw('
                stores.*,
                COALESCE(SUM(CASE WHEN store_debt_ledger.entry_type = "payment" THEN store_debt_ledger.amount ELSE 0 END), 0) as total_paid,
                COALESCE(SUM(CASE WHEN store_debt_ledger.entry_type = "sale" THEN store_debt_ledger.amount ELSE 0 END), 0) as total_debt,
                COALESCE(SUM(CASE WHEN store_debt_ledger.entry_type = "return" THEN store_debt_ledger.amount ELSE 0 END), 0) as total_returns
            ')
            ->groupBy('stores.id', 'stores.name', 'stores.owner_name', 'stores.phone', 'stores.address', 'stores.is_active', 'stores.created_at')
            ->get()
            ->map(function ($store) {
                $store->remaining = $store->total_debt - $store->total_returns - $store->total_paid;
                return $store;
            });

        return view('admin.stores.index', compact('stores'));
    }
}
