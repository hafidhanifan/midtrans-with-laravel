<?php


namespace App\Http\Controllers;


use Illuminate\Http\Request;
use App\Services\MidtransService;
use App\Models\Order;
use Midtrans\Snap;
use Illuminate\Support\Str;


class PaymentController extends Controller
{
    public function checkoutPage()
    {
        // tampilkan halaman checkout (contoh sederhana)
        return view('checkout');
    }


    public function createTransaction(Request $request)
    {
        // validasi sederhana
        $request->validate(['amount' => 'required|numeric|min:100']);


        // buat order di DB
        $orderId = 'INV-' . time() . '-' . Str::random(5);


        $order = Order::create([
            'order_id' => $orderId,
            'user_id' => auth()->id() ?? null,
            'amount' => $request->amount,
            'status' => 'pending'
        ]);


        // konfigurasi midtrans
        MidtransService::setup();


        $params = [
            'transaction_details' => [
                'order_id' => $order->order_id,
                'gross_amount' => $order->amount,
            ],
            'customer_details' => [
                'first_name' => $request->input('first_name', 'Pembeli'),
                'email' => $request->input('email', 'customer@example.com'),
                'phone' => $request->input('phone', '081234567890'),
            ],
        ];


        $snapToken = Snap::getSnapToken($params);


        // kembalikan token ke frontend
        return response()->json(['token' => $snapToken, 'order_id' => $order->order_id]);
    }


    // endpoint untuk menerima notification dari Midtrans
    public function notification(Request $request)
    {
        MidtransService::setup();


        $notif = new \Midtrans\Notification();


        // validasi signature (opsional tapi sangat direkomendasikan)
        $expected = hash('sha512', $notif->order_id . $notif->status_code . $notif->gross_amount . config('midtrans.server_key'));
        if (isset($notif->signature_key) && $notif->signature_key !== $expected) {
            return response('Invalid signature', 401);
        }


        $order = Order::where('order_id', $notif->order_id)->first();
        if (!$order) {
            return response('Order not found', 404);
        }


        // idempotency: jika sudah settlement, abaikan update yang sama
        if ($order->status === 'settlement') {
            return response()->json(['message' => 'already settled']);
        }


        $transaction = $notif->transaction_status; // capture/settlement/pending/deny/expire/cancel
        $type = $notif->payment_type ?? null;


    }
}
