# 🔄 تحديث التصميم - Taqnia Distribution Manager v4

## 📋 التغييرات الرئيسية

### 🧠 القاعدة الأساسية المحدثة
**أي عملية داخل النظام يجب أن تكون:**
- ✅ مرتبطة بفاتورة أو طلب رسمي
- ✅ موثقة بصورة (ختم / توقيع)
- ✅ معتمدة من أمين المخزن

**ممنوع منعاً باتاً:**
- ❌ الاعتماد الشفهي
- ❌ التعديل المباشر
- ❌ الحساب المالي بدون توثيق

---

## 🗃️ قاعدة البيانات الجديدة (26 جدول)

### الجداول المضافة الجديدة:
1. **`store_pending_stock`** - مخزون المتجر المرحلي (غير مُلزم قانونياً)
2. **`marketer_commissions`** - تسجيل أرباح المسوق من المقبوض
3. **`marketer_withdrawal_requests`** - طلبات سحب الأرباح
4. **`marketer_withdrawals`** - توثيق استلام الأرباح فعلياً

### التغييرات في الجداول الموجودة:

#### `factory_invoices`
- تغيير `manager_id` إلى `factory_manager_id`

#### `store_debt_ledger`
- إزالة `payment` من `entry_type`
- الآن فقط: `sale / return`

#### `sales_return_confirmation`
- جدول منفصل لتوثيق الإرجاع

---

## 🔄 التدفق المحدث للعمليات

### المرحلة 3: بيع المسوق للمتاجر (Pending)
**التغيير الجديد:**
- الكمية تُضاف إلى `store_pending_stock` (مرحلي)
- **لا دين ولا أرباح في هذه المرحلة**
- المتجر غير مُلزم قانونياً

### المرحلة 5: الديون والأرباح
**قاعدة جديدة:**
> عمولة المسوق تُحسب فقط من المبالغ المقبوضة فعلياً

**التدفق:**
1. تسديد المتجر → `store_payments`
2. حساب العمولة → `marketer_commissions`
3. طلب سحب الأرباح → `marketer_withdrawal_requests`
4. توثيق الاستلام → `marketer_withdrawals`

### المرحلة 6: سحب أرباح المسوق (موثّق)
**عملية جديدة كاملة:**
1. المسوق يقدم طلب سحب
2. أمين المخزن يوافق/يرفض
3. عند الاستلام: توقيع + رفع صورة
4. اعتماد السحب

---

## 📊 مقارنة الإصدارات

| العنصر | v1 | v4 |
|--------|----|----|
| عدد الجداول | 23 | 26 |
| إدارة الأرباح | ❌ | ✅ |
| المخزون المرحلي | ❌ | ✅ |
| سحب الأرباح | ❌ | ✅ |
| توثيق الإرجاع | مدمج | منفصل |

---

## 🎯 الجداول الجديدة بالتفصيل

### `store_pending_stock`
```sql
CREATE TABLE store_pending_stock (
    id INT PRIMARY KEY AUTO_INCREMENT,
    store_id INT NOT NULL,
    sales_invoice_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (store_id) REFERENCES stores(id),
    FOREIGN KEY (sales_invoice_id) REFERENCES sales_invoices(id),
    FOREIGN KEY (product_id) REFERENCES products(id)
);
```

### `marketer_commissions`
```sql
CREATE TABLE marketer_commissions (
    id INT PRIMARY KEY AUTO_INCREMENT,
    marketer_id INT NOT NULL,
    payment_id INT NOT NULL,
    commission_rate DECIMAL(5,2) NOT NULL,
    commission_amount DECIMAL(12,2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (marketer_id) REFERENCES users(id),
    FOREIGN KEY (payment_id) REFERENCES store_payments(id)
);
```

### `marketer_withdrawal_requests`
```sql
CREATE TABLE marketer_withdrawal_requests (
    id INT PRIMARY KEY AUTO_INCREMENT,
    marketer_id INT NOT NULL,
    requested_amount DECIMAL(12,2) NOT NULL,
    status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (marketer_id) REFERENCES users(id)
);
```

### `marketer_withdrawals`
```sql
CREATE TABLE marketer_withdrawals (
    id INT PRIMARY KEY AUTO_INCREMENT,
    withdrawal_request_id INT NOT NULL,
    marketer_id INT NOT NULL,
    amount DECIMAL(12,2) NOT NULL,
    keeper_id INT NOT NULL,
    signed_receipt_image VARCHAR(255) NOT NULL,
    confirmed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (withdrawal_request_id) REFERENCES marketer_withdrawal_requests(id),
    FOREIGN KEY (marketer_id) REFERENCES users(id),
    FOREIGN KEY (keeper_id) REFERENCES users(id)
);
```

---

## 🚀 خطة التحديث

### المرحلة الأولى: تحديث قاعدة البيانات
- [ ] إضافة الجداول الجديدة الـ 4
- [ ] تعديل الجداول الموجودة
- [ ] إنشاء migrations جديدة

### المرحلة الثانية: تحديث Models
- [ ] إنشاء Models للجداول الجديدة
- [ ] تحديث العلاقات
- [ ] إضافة validation rules

### المرحلة الثالثة: تحديث APIs
- [ ] APIs إدارة الأرباح
- [ ] APIs سحب الأرباح
- [ ] APIs المخزون المرحلي
- [ ] تحديث APIs الموجودة

### المرحلة الرابعة: تحديث الواجهات
- [ ] لوحة إدارة الأرباح
- [ ] واجهة سحب الأرباح
- [ ] تحديث تقارير المخزون

---

## 📈 المؤشرات الجديدة

### للمسوقين:
- إجمالي العمولات المستحقة
- العمولات المسحوبة
- الرصيد المتاح للسحب
- تاريخ طلبات السحب

### للإدارة:
- إجمالي العمولات المدفوعة
- المخزون المرحلي للمتاجر
- معدل تحويل Pending إلى Approved
- تقارير الأرباح الشهرية

---

**تاريخ التحديث**: ديسمبر 2024  
**الإصدار**: v4.0  
**الحالة**: جاهز للتنفيذ