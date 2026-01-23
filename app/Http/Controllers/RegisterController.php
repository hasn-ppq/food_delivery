<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class RegisterController extends Controller
{
   public function store(Request $request)
    {
        $request->validate([
            'name'     => 'required|string',
            'email'    => 'required|email',
            'phone'    => 'required',
            'password' => 'required|string|min:8|confirmed',
            'role_id'  => 'required|exists:roles,id',
        ],
          [
        'password.min' => 'كلمة المرور يجب أن تكون 8 رموز أو أكثر',
        'password.confirmed' => 'كلمتا المرور غير متطابقتين',
    ]);

        // 🔍 تحقق هل المستخدم موجود
        $user = User::where('email', $request->email)
            ->orWhere('phone', $request->phone)
            ->first();

        if ($user) {

            // 🕒 مسجل وينتظر الموافقة
            if (! $user->is_active) {
                return redirect()->back()->with(
                    'info',
                    'أنت مسجل مسبقاً، طلبك قيد المراجعة من قبل الإدارة'
                );
            }

            // ❌ حساب مفعل موجود
            return redirect()->back()->with(
                'error',
                'هذا الحساب مسجل مسبقاً، يرجى تسجيل الدخول'
            );
        }

        // ✅ تسجيل جديد
        $created = User::create([
            'name'      => $request->name,
            'email'     => $request->email,
            'phone'     => $request->phone,
            'password'  => Hash::make($request->password),
            'role_id'   => $request->role_id,
            'is_active' => false,
        ]);

        // ❌ فشل التسجيل (نادر بس مهم)
        if (! $created) {
            return redirect()->back()->with(
                'error',
                'لم يتم التسجيل، يرجى المحاولة مرة أخرى'
            );
        }

        // 🎉 نجاح
        return redirect()->back()->with(
            'success',
            'تم إرسال طلبك بنجاح، بانتظار موافقة الإدارة'
        );
    }
}
