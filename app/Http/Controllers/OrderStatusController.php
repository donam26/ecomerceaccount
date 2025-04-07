/**
     * Kiểm tra trạng thái đơn hàng
     */
    public function checkStatus(Request $request, $orderNumber)
    {
        // Ghi log thông tin request để debug
        Log::info('Order status check request', [
            'order_number' => $orderNumber,
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent()
        ]);

        try {
            // Kiểm tra loại đơn hàng dựa vào prefix
            if (strpos($orderNumber, 'BOOST') === 0) {
                // Đơn hàng cày thuê
                $order = BoostingOrder::where('order_number', $orderNumber)->first();
                
                if (!$order) {
                    Log::warning('Boosting Order not found during status check', ['order_number' => $orderNumber]);
                    return response()->json([
                        'success' => false,
                        'message' => 'Không tìm thấy đơn hàng cày thuê',
                        'redirect' => false
                    ], 404);
                }
                
                Log::info('Boosting Order found during status check', [
                    'order_number' => $orderNumber,
                    'status' => $order->status
                ]);

                // Đối với đơn hàng cày thuê, trạng thái "paid" và "processing" cũng được coi là đã thanh toán
                $isPaid = in_array($order->status, ['paid', 'processing', 'completed']);
                
                return response()->json([
                    'success' => true,
                    'status' => $order->status,
                    'paid' => $isPaid,
                    'order_number' => $order->order_number,
                    'redirect' => $isPaid ? route('boosting.account_info', $order->order_number) : null
                ]);
            } else {
                // Đơn hàng thường
                $order = Order::where('order_number', $orderNumber)->first();

                if (!$order) {
                    Log::warning('Order not found during status check', ['order_number' => $orderNumber]);
                    return response()->json([
                        'success' => false,
                        'message' => 'Không tìm thấy đơn hàng',
                        'redirect' => false
                    ], 404);
                }

                Log::info('Order found during status check', [
                    'order_number' => $orderNumber,
                    'status' => $order->status
                ]);

                return response()->json([
                    'success' => true,
                    'status' => $order->status,
                    'paid' => $order->status === 'completed',
                    'order_number' => $order->order_number,
                    'redirect' => $order->status === 'completed' ? route('payment.success', $order->order_number) : null
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Error checking order status', [
                'order_number' => $orderNumber,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Lỗi hệ thống khi kiểm tra trạng thái đơn hàng',
                'redirect' => false
            ], 500);
        }
    } 