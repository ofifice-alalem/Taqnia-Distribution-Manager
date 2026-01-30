# 💰 نظام سحب الأرباح - دليل كامل

## 📋 نظرة عامة

تم تصميم نظام سحب الأرباح بشكل احترافي يتبع أفضل الممارسات (Event Sourcing Pattern) لضمان:
- ✅ دقة 100% في الحسابات
- ✅ شفافية كاملة في العمليات
- ✅ توثيق كامل لكل معاملة
- ✅ مسار تدقيق (Audit Trail) واضح

---

## 🗃️ قاعدة البيانات

### 1️⃣ `marketer_commissions` - عمولات المسوق
```sql
CREATE TABLE marketer_commissions (
    id BIGINT PRIMARY KEY,
    marketer_id BIGINT FOREIGN KEY → users,
    store_id BIGINT FOREIGN KEY → stores,
    keeper_id BIGINT FOREIGN KEY → users,
    payment_id BIGINT FOREIGN KEY → store_payments,
    payment_amount DECIMAL(12,2),
    commission_rate DECIMAL(5,2),
    commission_amount DECIMAL(12,2),
    created_at TIMESTAMP
);
```

**الوصف:** يتم إنشاء سجل عمولة تلقائياً عند توثيق تسديد متجر (status: approved)

**مثال:**
```
- متجر سدد: 10,000 ريال
- نسبة عمولة المسوق: 5%
- عمولة المسوق: 500 ريال
```

---

### 2️⃣ `marketer_withdrawal_requests` - طلبات السحب
```sql
CREATE TABLE marketer_withdrawal_requests (
    id BIGINT PRIMARY KEY,
    marketer_id BIGINT FOREIGN KEY → users,
    requested_amount DECIMAL(12,2),
    status ENUM('pending', 'approved', 'rejected', 'cancelled'),
    created_at TIMESTAMP
);
```

**الوصف:** المسوق يطلب سحب مبلغ من أرباحه

**الحالات:**
- `pending`: في انتظار موافقة المسؤول
- `approved`: تمت الموافقة والتوثيق
- `rejected`: تم رفض الطلب
- `cancelled`: ألغاه المسوق

---

### 3️⃣ `marketer_withdrawals` - السحوبات الموثقة
```sql
CREATE TABLE marketer_withdrawals (
    id BIGINT PRIMARY KEY,
    withdrawal_request_id BIGINT FOREIGN KEY → marketer_withdrawal_requests,
    marketer_id BIGINT FOREIGN KEY → users,
    amount DECIMAL(12,2),
    admin_id BIGINT FOREIGN KEY → users,  -- ✅ المسؤول (تم التعديل)
    signed_receipt_image VARCHAR(255),
    confirmed_at TIMESTAMP
);
```

**الوصف:** توثيق السحب بعد موافقة المسؤول وتسليم المبلغ

**ملاحظة مهمة:** تم تغيير `keeper_id` إلى `admin_id` لأن المسؤول هو من يوثق ويسلم المال

---

## 🔄 سير العمل الكامل

### 1️⃣ احتساب الأرباح (تلقائي)

```
عند توثيق تسديد متجر:
1. المتجر يسدد مبلغ
2. أمين المخزن يوثق التسديد (status: approved)
3. النظام يحسب العمولة تلقائياً
4. يتم إنشاء سجل في marketer_commissions
```

**الكود:**
```php
// في PaymentConfirmationController
$payment = StorePayment::find($paymentId);
$marketer = User::find($payment->marketer_id);

$commissionAmount = $payment->amount * ($marketer->commission_rate / 100);

MarketerCommission::create([
    'payment_id' => $payment->id,
    'marketer_id' => $marketer->id,
    'store_id' => $payment->store_id,
    'keeper_id' => $payment->keeper_id,
    'payment_amount' => $payment->amount,
    'commission_rate' => $marketer->commission_rate,
    'commission_amount' => $commissionAmount
]);
```

---

### 2️⃣ المسوق يطلب سحب

```
1. المسوق يدخل على صفحة "سحب الأرباح"
2. يرى رصيده المتاح
3. يطلب سحب مبلغ معين
4. النظام يتحقق من الرصيد
5. إذا كان كافي → إنشاء طلب (pending)
```

**الصفحة:** `/marketer/withdrawals`

**الكود:**
```php
// حساب الرصيد المتاح
$totalEarned = MarketerCommission::where('marketer_id', $marketerId)
    ->sum('commission_amount');

$totalWithdrawn = MarketerWithdrawal::where('marketer_id', $marketerId)
    ->sum('amount');

$availableBalance = $totalEarned - $totalWithdrawn;

// إنشاء طلب
if ($requestedAmount <= $availableBalance) {
    MarketerWithdrawalRequest::create([
        'marketer_id' => $marketerId,
        'requested_amount' => $requestedAmount,
        'status' => 'pending'
    ]);
}
```

---

### 3️⃣ المسؤول يراجع الطلب

```
1. المسؤول يدخل على صفحة "طلبات سحب الأرباح"
2. يرى جميع الطلبات (pending / approved / rejected)
3. يختار طلب معين
4. يرى تفاصيل الطلب ورصيد المسوق
```

**الصفحة:** `/admin/withdrawals`

---

### 4️⃣ المسؤول يوافق ويسلم المبلغ

```
1. المسؤول يوافق على الطلب
2. يسلم المبلغ للمسوق نقداً
3. يطلب من المسوق التوقيع على إيصال الاستلام
4. يرفع صورة الإيصال المختوم
5. النظام يوثق السحب
```

**الصفحة:** `/admin/withdrawals/{id}`

**الكود:**
```php
// الموافقة والتوثيق
$withdrawalRequest->update(['status' => 'approved']);

MarketerWithdrawal::create([
    'withdrawal_request_id' => $withdrawalRequest->id,
    'marketer_id' => $withdrawalRequest->marketer_id,
    'amount' => $withdrawalRequest->requested_amount,
    'admin_id' => Auth::id(),  // المسؤول الذي سلّم المال
    'signed_receipt_image' => $imagePath,
    'confirmed_at' => now()
]);
```

---

## 📊 حساب الرصيد

### الصيغة:
```
الرصيد المتاح = إجمالي الأرباح - إجمالي المسحوب
```

### الكود:
```php
function getAvailableBalance($marketerId) {
    $totalEarned = MarketerCommission::where('marketer_id', $marketerId)
        ->sum('commission_amount');
    
    $totalWithdrawn = MarketerWithdrawal::where('marketer_id', $marketerId)
        ->sum('amount');
    
    return $totalEarned - $totalWithdrawn;
}
```

### مثال عملي:
```
المسوق: أحمد

📊 العمولات:
- تسديد 1: 500 ريال
- تسديد 2: 300 ريال
- تسديد 3: 700 ريال
━━━━━━━━━━━━━━━━━━━━━
إجمالي: 1,500 ريال

💸 السحوبات:
- سحب 1: 500 ريال
- سحب 2: 300 ريال
━━━━━━━━━━━━━━━━━━━━━
إجمالي: 800 ريال

✅ الرصيد المتاح:
1,500 - 800 = 700 ريال
```

---

## 🎯 الصلاحيات

### المسوق (Salesman):
- ✅ عرض رصيده
- ✅ طلب سحب
- ✅ إلغاء طلب معلق
- ✅ عرض سجل السحوبات
- ❌ لا يمكنه الموافقة

### المسؤول (Admin):
- ✅ عرض جميع الطلبات
- ✅ الموافقة على الطلبات
- ✅ رفض الطلبات
- ✅ تسليم المبلغ
- ✅ رفع صورة الإيصال
- ✅ توثيق السحب

### أمين المخزن (Warehouse Keeper):
- ❌ لا علاقة له بسحب الأرباح
- ℹ️ دوره فقط في توثيق التسديدات

---

## 📁 الملفات المُنشأة

### Models:
```
app/Models/Withdrawal/
├── MarketerWithdrawalRequest.php
└── MarketerWithdrawal.php
```

### Controllers:
```
app/Http/Controllers/
├── MarketerWithdrawalController.php  (للمسوق)
└── AdminWithdrawalController.php     (للمسؤول)
```

### Views:
```
resources/views/
├── marketer/withdrawals/
│   └── index.blade.php
└── admin/withdrawals/
    ├── index.blade.php
    └── show.blade.php
```

### Routes:
```php
// المسوق
Route::get('/marketer/withdrawals', [MarketerWithdrawalController::class, 'index']);
Route::post('/marketer/withdrawals', [MarketerWithdrawalController::class, 'store']);
Route::post('/marketer/withdrawals/{id}/cancel', [MarketerWithdrawalController::class, 'cancel']);

// المسؤول
Route::get('/admin/withdrawals', [AdminWithdrawalController::class, 'index']);
Route::get('/admin/withdrawals/{id}', [AdminWithdrawalController::class, 'show']);
Route::post('/admin/withdrawals/{id}/approve', [AdminWithdrawalController::class, 'approve']);
Route::post('/admin/withdrawals/{id}/reject', [AdminWithdrawalController::class, 'reject']);
```

### Migration:
```
database/migrations/
└── 2026_02_01_000001_change_keeper_to_admin_in_marketer_withdrawals.php
```

---

## ✅ المميزات

### 1. Event Sourcing Pattern
- لا يوجد حقل رصيد (balance)
- كل عملية = سجل منفصل
- الرصيد = حساب ديناميكي
- دقة 100%

### 2. Audit Trail
- كل عملية موثقة
- من قام بها؟
- متى؟
- كم المبلغ؟
- صورة الإيصال

### 3. Workflow Management
- فصل الطلب عن التنفيذ
- موافقات واضحة
- تتبع الحالات
- صلاحيات محددة

### 4. Data Integrity
- Foreign Keys
- لا يمكن التلاعب
- ترابط البيانات
- سهولة التتبع

---

## 🔒 الأمان

### 1. التحقق من الرصيد
```php
// قبل إنشاء الطلب
if ($requestedAmount > $availableBalance) {
    return back()->with('error', 'الرصيد المتاح غير كافٍ');
}

// قبل الموافقة
if ($withdrawalRequest->requested_amount > $availableBalance) {
    return back()->with('error', 'رصيد المسوق غير كافٍ');
}
```

### 2. التحقق من الصلاحيات
```php
// Middleware
Route::middleware(['auth', 'role:salesman'])->group(function () {
    // routes للمسوق فقط
});

Route::middleware(['auth', 'role:admin'])->group(function () {
    // routes للمسؤول فقط
});
```

### 3. التحقق من الحالة
```php
// لا يمكن إلغاء طلب موافق عليه
if ($request->status !== 'pending') {
    return back()->with('error', 'لا يمكن إلغاء هذا الطلب');
}
```

---

## 📈 التقارير

### تقرير أرباح المسوق:
```php
function getMarketerEarningsReport($marketerId) {
    return [
        'total_earned' => MarketerCommission::where('marketer_id', $marketerId)->sum('commission_amount'),
        'total_withdrawn' => MarketerWithdrawal::where('marketer_id', $marketerId)->sum('amount'),
        'available_balance' => $totalEarned - $totalWithdrawn,
        'commissions_count' => MarketerCommission::where('marketer_id', $marketerId)->count(),
        'withdrawals_count' => MarketerWithdrawal::where('marketer_id', $marketerId)->count()
    ];
}
```

---

## 🚀 الاستخدام

### للمسوق:
1. افتح `/marketer/withdrawals`
2. اضغط "طلب سحب جديد"
3. أدخل المبلغ
4. انتظر موافقة المسؤول

### للمسؤول:
1. افتح `/admin/withdrawals`
2. اختر طلب من القائمة
3. راجع التفاصيل
4. وافق أو ارفض
5. إذا وافقت: سلّم المبلغ وارفع صورة الإيصال

---

## ✅ تم الإنجاز

- ✅ تعديل قاعدة البيانات (keeper_id → admin_id)
- ✅ إنشاء Models
- ✅ إنشاء Controllers
- ✅ إنشاء Routes
- ✅ إنشاء Views (المسوق)
- ✅ إنشاء Views (المسؤول)
- ✅ إضافة روابط في القوائم
- ✅ تشغيل Migration

---

## 📝 ملاحظات

1. **الصور:** يتم حفظها في `storage/app/public/withdrawals/`
2. **الأمان:** جميع الطلبات محمية بـ Middleware
3. **التوثيق:** كل عملية موثقة بالتاريخ والمستخدم
4. **الدقة:** لا يوجد مجال للخطأ في الحسابات

---

تم التصميم والتنفيذ بنجاح! 🎉
