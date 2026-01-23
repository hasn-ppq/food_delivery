<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <title>تسجيل حساب جديد</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    {{-- Bootstrap --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">

            <div class="card shadow">
                <div class="card-header text-center fw-bold">
                    تسجيل حساب جديد
                </div>

                <div class="card-body">

                   @if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif

@if(session('info'))
    <div class="alert alert-warning">
        {{ session('info') }}
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger">
        {{ session('error') }}
    </div>
@endif
@if ($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

                    <form method="POST" action="{{ route('register.store') }}">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label">الاسم</label>
                            <input type="text" name="name" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">الإيميل</label>
                            <input type="email" name="email" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">رقم الهاتف</label>
                            <input type="text" name="phone" class="form-control" required>
                        </div>

                        <div class="mb-3">
    <label class="form-label">كلمة المرور</label>
    <input
        type="password"
        name="password"
        class="form-control"
        required
        minlength="8"
        
    >
    @error('password')
    <small class="text-danger">{{ $message }}</small>
@enderror
</div>

<div class="mb-3">
    <label class="form-label">تأكيد كلمة المرور</label>
    <input
        type="password"
        name="password_confirmation"
        class="form-control"
        required
        minlength="8"
    >
</div>


                        <div class="mb-3">
                            <label class="form-label">نوع الحساب</label>
                            <select name="role_id" class="form-select" required>
                                <option value="">اختر النوع</option>
                                <option value="22">صاحب مطعم</option>
                                <option value="23">درايفر</option>
                            </select>
                        </div>

                        <button class="btn btn-primary w-100">
                            تسجيل
                        </button>
                    </form>
                    <div class="text-center mt-3 text-muted">
                        بعد التسجيل سيتم مراجعة الطلب من قبل الإدارة
                    </div>
<hr class="my-4">

<div class="text-center">
    <p class="mb-3">هل لديك حساب بالفعل؟</p>

    <div class="d-grid gap-2">
        <a href="{{ url('/restaurant/login') }}" class="btn btn-outline-primary">
            تسجيل دخول صاحب مطعم
        </a>

        <a href="{{ url('/driver/login') }}" class="btn btn-outline-success">
            تسجيل دخول الدليفري
        </a>
    </div>
</div>

                </div>
            </div>

        </div>
    </div>
</div>

</body>
</html>
