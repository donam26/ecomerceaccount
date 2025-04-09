<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SePayService
{
    /**
     * Tạo mã QR code cho thanh toán qua SePay
     * 
     * @param float $amount
     * @param string $content
     * @return string
     */
    public function generateQrCode($amount, $content)
    {
        // Thông tin ngân hàng mặc định
        $bankAccount = config('payment.sepay.account', '103870429701');
        $bankName = config('payment.sepay.bank', 'VietinBank');
        
        // Tạo URL QR code
        $qrUrl = "https://qr.sepay.vn/img?acc={$bankAccount}&bank={$bankName}&amount={$amount}&des=" . urlencode($content) . "&template=compact";
        
        Log::info('Đã tạo QR SePay', [
            'amount' => $amount,
            'content' => $content,
            'url' => $qrUrl
        ]);
        
        return $qrUrl;
    }
    
    /**
     * Kiểm tra trạng thái giao dịch
     * 
     * @param string $depositCode
     * @return array
     */
    public function checkTransactionStatus($depositCode)
    {
        try {
            // Giả lập kiểm tra trạng thái, trong thực tế sẽ gọi API của SePay
            return [
                'success' => true,
                'status' => 'pending',
                'message' => 'Đang chờ thanh toán'
            ];
        } catch (\Exception $e) {
            Log::error('Lỗi khi kiểm tra trạng thái giao dịch SePay', [
                'deposit_code' => $depositCode,
                'error' => $e->getMessage()
            ]);
            
            return [
                'success' => false,
                'status' => 'error',
                'message' => 'Có lỗi xảy ra khi kiểm tra trạng thái'
            ];
        }
    }
    
    /**
     * Xác minh webhook từ SePay
     * 
     * @param array $data
     * @return bool
     */
    public function verifyWebhook($data)
    {
        // Trong thực tế, cần phải xác minh webhook bằng token hoặc chữ ký
        return true;
    }
} 