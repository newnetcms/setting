@extends('core::admin.master')

@section('meta_title', 'Nâng cấp cơ sở dữ liệu')

@section('page_title', 'Nâng cấp cơ sở dữ liệu')

@section('breadcrumb')
    <nav aria-label="đường dẫn" class="col-sm-4 order-sm-last mb-3 mb-sm-0 p-0 ">
        <ol class="breadcrumb d-inline-flex font-weight-600 fs-13 bg-white mb-0 float-sm-right">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard.index') }}">{{ trans('dashboard::message.index.breadcrumb') }}</a></li>
            <li class="breadcrumb-item"><a href="{{ route('setting.admin.setting.index') }}">{{ trans('core::setting.index.breadcrumb') }}</a></li>
            <li class="breadcrumb-item active">Nâng cấp cơ sở dữ liệu</li>
        </ol>
    </nav>
@stop

@section('content')
    <div class="card mb-4 border-warning">
        <div class="card-header bg-warning text-dark">
            <h6 class="fs-17 font-weight-600 mb-0">
                Cảnh báo: đang ở môi trường production
            </h6>
        </div>
        <div class="card-body">
            <p class="mb-2"><strong>Quan trọng:</strong> Bạn đang chuẩn bị chạy migration trên môi trường production.</p>
            <ul class="mb-0">
                <li>Luôn sao lưu cơ sở dữ liệu trước khi chạy migration</li>
                <li>Migration có thể thay đổi hoặc xóa dữ liệu</li>
                <li>Nên kiểm thử migration trên môi trường staging trước</li>
                <li>Đảm bảo không có người dùng đang thao tác trong lúc chạy migration</li>
            </ul>
        </div>
    </div>

    @if(isset($environment))
    <div class="card mb-4">
        <div class="card-header">
            <h6 class="fs-17 font-weight-600 mb-0">Thông tin môi trường</h6>
        </div>
        <div class="card-body">
            <p class="mb-0"><strong>Môi trường hiện tại:</strong> <span class="badge badge-{{ $environment === 'production' ? 'danger' : 'info' }}">{{ $environment }}</span></p>
        </div>
    </div>
    @endif

    @if(isset($webUpgradeDisabled) && $webUpgradeDisabled)
    <div class="card mb-4 border-danger">
        <div class="card-header bg-danger text-white">
            <h6 class="fs-17 font-weight-600 mb-0">Chức năng nâng cấp qua web đang tắt</h6>
        </div>
        <div class="card-body">
            <p class="mb-0">Chạy migration từ trang quản trị đang bị tắt trên production. Chỉ đặt <code>ADMIN_WEB_UPGRADE=true</code> khi bạn thật sự cần nâng cấp qua web trên shared host.</p>
        </div>
    </div>
    @endif

    @if(isset($isRunning) && $isRunning)
    <div class="card mb-4 border-info">
        <div class="card-header bg-info text-white">
            <h6 class="fs-17 font-weight-600 mb-0">Migration đang chạy</h6>
        </div>
        <div class="card-body">
            <p class="mb-0">Một tiến trình migration đang chạy. Vui lòng chờ hoàn tất trước khi chạy migration khác.</p>
        </div>
    </div>
    @endif

    @if(isset($statusOutput))
    <div class="card mb-4">
        <div class="card-header">
            <h6 class="fs-17 font-weight-600 mb-0">Trạng thái migration hiện tại</h6>
        </div>
        <div class="card-body">
            <pre class="mb-0 p-3 bg-light border rounded" style="max-height: 400px; overflow-y: auto; background-color: #fff !important; color: #212529 !important;">{{ $statusOutput }}</pre>
        </div>
    </div>
    @endif

    @if(isset($hasPending) && $hasPending)
    <div class="card mb-4">
        <div class="card-header">
            <h6 class="fs-17 font-weight-600 mb-0">Chạy các migration đang chờ</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('setting.admin.setting.upgrade.run') }}" method="POST">
                @csrf
                <div class="form-group">
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" class="custom-control-input" id="confirmMigration" name="confirm_upgrade" value="1" required>
                        <label class="custom-control-label" for="confirmMigration">
                            Tôi hiểu rủi ro và đã sao lưu cơ sở dữ liệu
                        </label>
                    </div>
                </div>
                <div class="form-group mb-0">
                    <button type="submit" class="btn btn-danger" {{ ((isset($isRunning) && $isRunning) || (isset($webUpgradeDisabled) && $webUpgradeDisabled)) ? 'disabled' : '' }}>
                        Chạy migration ngay
                    </button>
                    <p class="text-muted small mt-2 mb-0">Thao tác này sẽ chạy tất cả migration đang chờ.</p>
                </div>
            </form>
        </div>
    </div>
    @else
    <div class="card mb-4 border-success">
        <div class="card-header bg-success text-white">
            <h6 class="fs-17 font-weight-600 mb-0">Không có migration đang chờ</h6>
        </div>
        <div class="card-body">
            <p class="mb-0">Cơ sở dữ liệu đã được cập nhật. Không có migration nào đang chờ chạy.</p>
        </div>
    </div>
    @endif

    @if(isset($result))
    <div class="card mb-4 border-{{ $result['success'] ?? false ? 'success' : 'danger' }}">
        <div class="card-header bg-{{ $result['success'] ?? false ? 'success' : 'danger' }} text-white">
            <h6 class="fs-17 font-weight-600 mb-0">
                Kết quả migration: {{ $result['success'] ?? false ? 'Thành công' : 'Thất bại' }}
            </h6>
        </div>
        <div class="card-body">
            @if(isset($result['exitCode']))
            <p><strong>Mã thoát:</strong> <span class="badge badge-{{ $result['exitCode'] === 0 ? 'success' : 'danger' }}">{{ $result['exitCode'] }}</span></p>
            @endif

            @if(isset($result['output']) && !empty($result['output']))
            <div class="mb-3">
                <strong>Kết quả xuất ra:</strong>
                <pre class="mt-2 p-3 bg-light border rounded" style="max-height: 400px; overflow-y: auto; background-color: #fff !important; color: #212529 !important;">{{ $result['output'] }}</pre>
            </div>
            @endif

            @if(isset($result['error']) && !empty($result['error']))
            <div class="mb-0">
                <strong>Lỗi:</strong>
                <pre class="mt-2 p-3 bg-light border rounded text-danger" style="max-height: 400px; overflow-y: auto; background-color: #fff !important; color: #212529 !important;">{{ $result['error'] }}</pre>
            </div>
            @endif

            @if(isset($result['exception']))
            <div class="mb-0">
                <strong>Ngoại lệ:</strong>
                <pre class="mt-2 p-3 bg-light border rounded text-danger" style="max-height: 400px; overflow-y: auto; background-color: #fff !important; color: #212529 !important;">{{ $result['exception'] }}</pre>
            </div>
            @endif
        </div>
    </div>
    @endif
@stop
