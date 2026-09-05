<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Review;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Create admin user
        $admin = User::create([
            'name'              => 'Admin TokoKita',
            'email'             => 'admin@tokokita.com',
            'role'              => 'admin',
            'is_active'         => true,
            'email_verified_at' => now(),
        ]);

        User::create([
            'name'              => 'Hilal Admin',
            'email'             => 'mhilal044@gmail.com',
            'role'              => 'admin',
            'is_active'         => true,
            'email_verified_at' => now(),
        ]);

        // Create sample customers
        $customers = [];
        $customerData = [
            ['name' => 'Budi Santoso', 'email' => 'budi@example.com'],
            ['name' => 'Siti Rahayu', 'email' => 'siti@example.com'],
            ['name' => 'Ahmad Fauzi', 'email' => 'ahmad@example.com'],
            ['name' => 'Dewi Lestari', 'email' => 'dewi@example.com'],
        ];
        foreach ($customerData as $data) {
            $customers[] = User::create(array_merge($data, [
                'role'              => 'customer',
                'is_active'         => true,
                'email_verified_at' => now(),
                'avatar'            => 'https://ui-avatars.com/api/?name=' . urlencode($data['name']) . '&color=FF6B00&background=FFF0E6',
            ]));
        }

        // Create categories for Digital Products & Software
        $categoryData = [
            ['name' => 'Software & Desain',  'sort_order' => 1],
            ['name' => 'Akun Premium',        'sort_order' => 2],
            ['name' => 'Lisensi & Key',       'sort_order' => 3],
            ['name' => 'Game & TopUp',        'sort_order' => 4],
            ['name' => 'Voucher & Gift Card', 'sort_order' => 5],
        ];

        $categories = [];
        foreach ($categoryData as $data) {
            $categories[] = Category::create(array_merge($data, [
                'slug'      => Str::slug($data['name']),
                'is_active' => true,
            ]));
        }

        // Create Digital Products (ONLY 1 Product as requested by user: Adobe 1 Days Full Garansi)
        $products = [];
        $productData = [
            [
                'name'             => 'Adobe 1 Days Full Garansi',
                'price'            => 20000,
                'sale_price'       => 18000,
                'stock'            => 50,
                'category'         => 0, // Software & Desain
                'description'      => 'Akses Adobe Creative Cloud All Apps selama 1 Hari dengan Full Garansi 24 Jam. Pengiriman instan & otomatis via chat admin.',
                'long_description' => "Fitur Produk:\n- Akses Adobe Creative Cloud (Photoshop, Illustrator, Premiere Pro, After Effects, Lightroom, dll)\n- Garansi Full 1 Hari (24 Jam)\n- Private / Shared Account (Legal & Aman)\n- Pengiriman instan otomatis format email:password setelah konfirmasi pembayaran\n- Layanan CS 24/7.",
            ],
        ];

        foreach ($productData as $data) {
            $catIdx = $data['category'];
            $products[] = Product::create([
                'name'             => $data['name'],
                'slug'             => Str::slug($data['name']),
                'description'      => $data['description'],
                'long_description' => $data['long_description'],
                'price'            => $data['price'],
                'sale_price'       => $data['sale_price'],
                'stock'            => $data['stock'],
                'category_id'      => $categories[$catIdx]->id,
                'status'           => 'active',
                'sold_count'       => rand(25, 100),
            ]);
        }

        // Seed stock credentials in DigitalItems table for Adobe 1 Days
        $adobeProduct = $products[0];
        for ($k = 1; $k <= 10; $k++) {
            \App\Models\DigitalItem::create([
                'product_id'  => $adobeProduct->id,
                'credentials' => "adobe_user{$k}@gmail.com:pwaccadobe{$k}",
                'is_used'     => false,
            ]);
        }

        // Create sample orders & reviews for Adobe 1 Days
        $adobeProduct = $products[0];
        foreach ($customers as $customer) {
            $order = Order::create([
                'invoice_number'       => 'INV-' . strtoupper(Str::random(8)) . '-' . date('Ymd'),
                'user_id'              => $customer->id,
                'subtotal'             => 20000,
                'shipping_cost'        => 0, // Produk Digital bebas ongkir
                'total_price'          => 20000,
                'status'               => 'completed',
                'shipping_name'        => $customer->name,
                'shipping_address'     => 'Digital Delivery (WhatsApp / Email)',
                'shipping_phone'       => '081234567890',
                'shipping_city'        => 'Digital',
                'shipping_province'    => 'Online',
                'shipping_postal_code' => '00000',
                'payment_method'       => 'e_wallet',
                'paid_at'              => now()->subDays(rand(1, 10)),
            ]);

            OrderItem::create([
                'order_id'     => $order->id,
                'product_id'   => $adobeProduct->id,
                'product_name' => $adobeProduct->name,
                'quantity'     => 1,
                'price'        => $adobeProduct->effective_price,
                'subtotal'     => $adobeProduct->effective_price,
            ]);

            Review::create([
                'product_id'  => $adobeProduct->id,
                'user_id'     => $customer->id,
                'order_id'    => $order->id,
                'rating'      => 5,
                'comment'     => 'Mantap bang Adobe 1 hari nya langsung aktif hitungan menit! Garansi beneran responsif!',
                'is_approved' => true,
            ]);
        }

        echo "✅ Seeder berhasil! Produk digital Adobe 1 Days & produk non-fisik sudah dibuat.\n";
    }
}
