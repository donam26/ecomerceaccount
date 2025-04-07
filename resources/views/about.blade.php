@extends('layouts.app')

@section('title', 'Giới thiệu')

@section('content')
<div class="bg-gray-50 py-12">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <div class="max-w-4xl mx-auto">
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <div class="p-8">
                    <h1 class="text-3xl font-bold text-gray-800 mb-6">Giới thiệu về Game Shop</h1>
                    
                    <div class="prose prose-blue max-w-none">
                        <p class="text-lg text-gray-600 mb-6">Chào mừng bạn đến với Game Shop - nơi cung cấp tài khoản game Playtogerther uy tín hàng đầu Việt Nam.</p>
                        
                        <h2 class="text-2xl font-semibold text-gray-800 mt-8 mb-4">Câu chuyện của chúng tôi</h2>
                        <p class="mb-4">Game Shop được thành lập vào năm 2020 với mục tiêu cung cấp cho người chơi những tài khoản game chất lượng với giá cả hợp lý. Chúng tôi hiểu rằng việc sở hữu một tài khoản game tốt là điều mà nhiều game thủ mong muốn, và chúng tôi đã xây dựng nền tảng này để đáp ứng nhu cầu đó.</p>
                        
                        <p class="mb-4">Với đội ngũ nhân viên giàu kinh nghiệm và am hiểu về game, chúng tôi cam kết mang đến cho khách hàng những sản phẩm và dịch vụ tốt nhất.</p>
                        
                        <h2 class="text-2xl font-semibold text-gray-800 mt-8 mb-4">Sứ mệnh của chúng tôi</h2>
                        <p class="mb-4">Sứ mệnh của Game Shop là trở thành địa chỉ tin cậy hàng đầu cho người chơi game Playtogerther tại Việt Nam, nơi họ có thể dễ dàng tìm kiếm và sở hữu những tài khoản game chất lượng với giá cả hợp lý nhất.</p>
                        
                        <h2 class="text-2xl font-semibold text-gray-800 mt-8 mb-4">Giá trị cốt lõi</h2>
                        <ul class="list-disc ml-6 mb-6">
                            <li class="mb-2"><strong>Uy tín:</strong> Đặt chữ tín lên hàng đầu, đảm bảo mọi thông tin về tài khoản đều chính xác và đúng như mô tả.</li>
                            <li class="mb-2"><strong>Chất lượng:</strong> Chỉ cung cấp những tài khoản đã được kiểm duyệt kỹ lưỡng, đảm bảo chất lượng và giá trị.</li>
                            <li class="mb-2"><strong>Dịch vụ khách hàng:</strong> Luôn sẵn sàng hỗ trợ khách hàng mọi lúc, mọi nơi để đảm bảo trải nghiệm mua sắm tuyệt vời.</li>
                            <li class="mb-2"><strong>Đổi mới:</strong> Không ngừng cải tiến và nâng cấp hệ thống để mang đến trải nghiệm tốt nhất cho người dùng.</li>
                        </ul>
                        
                        <h2 class="text-2xl font-semibold text-gray-800 mt-8 mb-4">Đội ngũ của chúng tôi</h2>
                        <p class="mb-4">Game Shop quy tụ những chuyên gia am hiểu sâu sắc về game Playtogerther, từ những người chơi chuyên nghiệp cho đến những kỹ thuật viên tài năng. Chúng tôi đoàn kết và làm việc với nhau để mang đến những sản phẩm và dịch vụ tốt nhất cho khách hàng.</p>
                        
                        <div class="mt-8 text-center">
                            <p class="font-semibold text-gray-800">Hãy trải nghiệm dịch vụ của chúng tôi ngay hôm nay!</p>
                            <a href="{{ route('accounts.index') }}" class="btn-primary inline-block mt-4">Xem tài khoản game</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 