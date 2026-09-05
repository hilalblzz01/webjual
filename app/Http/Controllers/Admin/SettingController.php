<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Services\QrisService;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    /**
     * Display settings page.
     */
    public function index()
    {
        $qrisString = QrisService::getQrisString();
        $qrisMerchantName = QrisService::getMerchantName();

        return view('admin.settings.index', compact('qrisString', 'qrisMerchantName'));
    }

    /**
     * Update settings.
     */
    public function update(Request $request)
    {
        $request->validate([
            'qris_string'        => 'required|string|max:2000',
            'qris_merchant_name' => 'required|string|max:255',
        ], [
            'qris_string.required'        => 'String QRIS wajib diisi!',
            'qris_merchant_name.required' => 'Nama Merchant/Toko QRIS wajib diisi!',
        ]);

        Setting::set('qris_string', trim($request->qris_string));
        Setting::set('qris_merchant_name', trim($request->qris_merchant_name));

        return back()->with('success', 'Pengaturan String QRIS & Merchant Toko berhasil diperbarui!');
    }
}
