<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TheSieuReService
{
    /**
     * API URL
     */
    protected $apiUrl;

    /**
     * API Key
     */
    protected $partnerID;

    /**
     * API Secret
     */
    protected $partnerKey;

    /**
     * Khởi tạo service
     */
    public function __construct()
    {
        $this->apiUrl = config('services.thesieure.url', 'https://thesieure.com/chargingws/v2');
        $this->partnerID = config('services.thesieure.partner_id');
        $this->partnerKey = config('services.thesieure.partner_key');
    }

    /**
     * Tạo chữ ký cho yêu cầu API
     * 
     * @param string $code Mã thẻ cào
     * @param string $serial Số serial của thẻ
     * @return string Chuỗi chữ ký đã được mã hoá MD5
     */
    protected function sign($code, $serial)
    {
        return md5($this->partnerKey . $code . $serial);
    }

    /**
     * Gửi thẻ cào để nạp tiền
     */
    public function chargeCard($telco, $code, $serial, $amount, $requestId)
    {
        try {
            $sign = $this->sign($code, $serial);

            $response = Http::post($this->apiUrl, [
                'telco' => $telco,
                'code' => $code,
                'serial' => $serial,
                'amount' => $amount,
                'request_id' => $requestId,
                'partner_id' => $this->partnerID,
                'sign' => $sign,
                'command' => 'charging',
            ]);

            $data = $response->json();
            Log::info('TheSieuRe API Response', ['request_id' => $requestId, 'response' => $data]);

            return [
                'success' => true,
                'data' => $data
            ];
        } catch (\Exception $e) {
            Log::error('TheSieuRe API Error', [
                'request_id' => $requestId,
                'exception' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'data' => [
                    'status' => 99,
                    'message' => 'Lỗi kết nối API: ' . $e->getMessage(),
                ]
            ];
        }
    }

    /**
     * Kiểm tra trạng thái thẻ cào
     */
    public function checkCard($requestId)
    {
        try {
            // Đối với check status, cần truy vấn thông tin code và serial từ database
            $cardDeposit = \App\Models\CardDeposit::where('request_id', $requestId)->first();
            
            if (!$cardDeposit) {
                return [
                    'success' => false,
                    'data' => [
                        'status' => 99,
                        'message' => 'Không tìm thấy thông tin thẻ cào',
                    ]
                ];
            }
            
            $sign = $this->sign($cardDeposit->code, $cardDeposit->serial);

            $response = Http::post($this->apiUrl, [
                'request_id' => $requestId,
                'partner_id' => $this->partnerID,
                'sign' => $sign,
                'command' => 'check',
            ]);

            $data = $response->json();
            Log::info('TheSieuRe Check API Response', ['request_id' => $requestId, 'response' => $data]);

            return [
                'success' => true,
                'data' => $data
            ];
        } catch (\Exception $e) {
            Log::error('TheSieuRe Check API Error', [
                'request_id' => $requestId,
                'exception' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'data' => [
                    'status' => 99,
                    'message' => 'Lỗi kết nối API: ' . $e->getMessage(),
                ]
            ];
        }
    }

    /**
     * Tính toán số tiền thực tế được cộng vào tài khoản
     * dựa trên nhà mạng và mệnh giá thẻ nạp
     * 
     * @param string $telco Nhà mạng
     * @param float $amount Mệnh giá thẻ
     * @return float Số tiền thực tế được nạp vào tài khoản
     */
    public function calculateActualAmount($telco, $amount)
    {
        // Tỷ lệ chiết khấu theo nhà mạng (%)
        $discountRates = [
            'VIETTEL' => 20, // Chiết khấu 20%
            'MOBIFONE' => 25, // Chiết khấu 25%
            'VINAPHONE' => 25, // Chiết khấu 25%
            'VIETNAMOBILE' => 30, // Chiết khấu 30%
            'ZING' => 18, // Chiết khấu 18%
            'GATE' => 18, // Chiết khấu 18%
            'VCOIN' => 18, // Chiết khấu 18%
        ];

        // Lấy tỷ lệ chiết khấu theo nhà mạng, mặc định là 30% nếu không tìm thấy
        $discountRate = $discountRates[strtoupper($telco)] ?? 30;
        
        // Tính toán số tiền thực tế
        $actualAmount = $amount * (100 - $discountRate) / 100;
        
        return $actualAmount;
    }

    /**
     * Dịch mã lỗi từ API
     */
    public function getStatusMessage($status)
    {
        $messages = [
            1 => 'Thẻ đúng - Gạch thẻ thành công',
            2 => 'Thẻ đúng - Đang chờ xử lý',
            3 => 'Thẻ sai mệnh giá',
            4 => 'Thẻ không đúng hoặc đã được sử dụng',
            99 => 'Lỗi kết nối hệ thống',
            100 => 'Giao dịch thành công',
        ];

        return $messages[$status] ?? 'Mã lỗi không xác định: ' . $status;
    }
} 