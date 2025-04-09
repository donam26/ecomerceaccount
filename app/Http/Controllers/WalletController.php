<?php

namespace App\Http\Controllers;

use App\Models\WalletTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WalletController extends Controller
{
    /**
     * Hiển thị thông tin ví của người dùng
     */
    public function index()
    {
        $user = Auth::user();
        $wallet = $user->getWallet();
        $transactions = $wallet->transactions()
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('wallet.index', compact('wallet', 'transactions'));
    }

    /**
     * Hiển thị lịch sử giao dịch của ví
     */
    public function transactions()
    {
        $user = Auth::user();
        $wallet = $user->getWallet();
        $transactions = $wallet->transactions()
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('wallet.transactions', compact('wallet', 'transactions'));
    }

    /**
     * Hiển thị form nạp tiền vào ví
     */
    public function showDepositForm()
    {
        $user = Auth::user();
        $wallet = $user->getWallet();
        
        return view('wallet.deposit', compact('wallet'));
    }

    /**
     * Xử lý yêu cầu nạp tiền
     */
    public function processDeposit(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:10000',
        ]);

        $user = Auth::user();
        $amount = $request->input('amount');
        
        // Tạo mã nạp tiền
        $depositCode = 'DEP' . time() . rand(100, 999);
        
        // Lưu thông tin giao dịch tạm thời
        session([
            'deposit' => [
                'amount' => $amount,
                'code' => $depositCode,
            ]
        ]);
        
        // Chuyển hướng đến trang thanh toán VNPay
        return redirect()->route('payment.vnpay', [
            'amount' => $amount,
            'order_id' => $depositCode,
            'order_type' => 'deposit',
            'return_url' => route('wallet.deposit.callback'),
        ]);
    }
    
    /**
     * Xử lý callback sau khi nạp tiền thành công
     */
    public function depositCallback(Request $request)
    {
        // Lấy thông tin thanh toán từ VNPay
        $vnp_ResponseCode = $request->input('vnp_ResponseCode');
        $vnp_TxnRef = $request->input('vnp_TxnRef');
        $vnp_Amount = $request->input('vnp_Amount');
        
        // Kiểm tra giao dịch thành công
        if ($vnp_ResponseCode == '00') {
            $depositInfo = session('deposit');
            
            if (!$depositInfo || $depositInfo['code'] !== $vnp_TxnRef) {
                return redirect()->route('wallet.deposit')->with('error', 'Thông tin nạp tiền không hợp lệ');
            }
            
            $amount = $depositInfo['amount'];
            $user = Auth::user();
            $wallet = $user->getWallet();
            
            // Cập nhật số dư và ghi log giao dịch
            $transaction = $wallet->deposit(
                $amount,
                WalletTransaction::TYPE_DEPOSIT,
                'Nạp tiền vào ví qua VNPay',
                $vnp_TxnRef,
                'Deposit',
                [
                    'vnp_transaction' => $request->all(),
                ]
            );
            
            // Xóa thông tin tạm thời
            session()->forget('deposit');
            
            return redirect()->route('wallet.index')
                ->with('success', 'Nạp tiền thành công! Số tiền ' . number_format($amount) . 'đ đã được thêm vào ví của bạn.');
        }
        
        return redirect()->route('wallet.deposit')
            ->with('error', 'Thanh toán không thành công. Vui lòng thử lại sau.');
    }
}
