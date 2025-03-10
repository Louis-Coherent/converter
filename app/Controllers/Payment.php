<?php

namespace App\Controllers;

use Stripe\Stripe;
use Stripe\Checkout\Session;
use App\Controllers\BaseController;
use App\Models\UserModel;

class Payment extends BaseController
{
    public function index()
    {
        if (!auth()->loggedIn()) {
            return redirect('register');
        }
        return view('payments/pricing');
    }

    public function checkout()
    {
        Stripe::setApiKey(env('STRIPE_SECRET_KEY'));

        $session = Session::create([
            'payment_method_types' => ['card'],
            'line_items' => [[
                'price_data' => [
                    'currency' => 'usd',
                    'product_data' => ['name' => 'Unlimited Conversions'],
                    'unit_amount' => 1000, // $10.00
                ],
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            'success_url' => site_url('payment/success') . '?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => site_url('payment/cancel'),
        ]);

        return redirect()->to($session->url);
    }

    public function success()
    {
        $sessionId = $this->request->getGet('session_id');

        if ($sessionId) {

            $userModel = new UserModel();
            $userId = auth()->id();
            $userModel->where('id', $userId)->update($userId, ['is_premium' => 1]);

            return redirect()->to('/')->with('alert', ['message' => 'Payment successful! Enjoy unlimited conversions.', 'type' => 'success']);
        }
        return redirect()->to('/')->with('alert', ['message' => 'Payment verification failed.', 'type' => 'error']);
    }

    public function cancel()
    {
        return redirect()->to('/')->with('alert', ['message' => 'Payment was canceled.', 'type' => 'error']);
    }
}
