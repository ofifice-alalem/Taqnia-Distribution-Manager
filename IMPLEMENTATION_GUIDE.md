# 🛠️ دليل التنفيذ التفصيلي - Taqnia Distribution Manager

## 📋 فهم النظام بالكامل

### المبدأ الأساسي
النظام يعمل على **سلسلة توثيق مترابطة**:
```
فاتورة مختومة → رفع صورة → تأكيد أمين المخزن → تحديث المخزون/الديون
```

### تدفق البيانات الكامل
```
المصنع → المخزن الرئيسي → مخزون الحجز → مخزون المسوق → مخزون المتجر → الديون → التسديد
```

---

## 🗃️ تصميم قاعدة البيانات التفصيلي

### 1. الجداول الأساسية

#### users (المستخدمين)
```sql
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    role ENUM('admin', 'warehouse_keeper', 'salesman') NOT NULL,
    phone VARCHAR(20),
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
```

#### products (المنتجات)
```sql
CREATE TABLE products (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    current_price DECIMAL(10,2) NOT NULL,
    unit VARCHAR(20) DEFAULT 'قطعة',
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
```

#### stores (المتاجر)
```sql
CREATE TABLE stores (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    owner_name VARCHAR(100),
    address TEXT,
    phone VARCHAR(20),
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

### 2. جداول المخزون

#### main_stock (المخزن الرئيسي)
```sql
CREATE TABLE main_stock (
    product_id INT PRIMARY KEY,
    quantity INT NOT NULL DEFAULT 0,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES products(id)
);
```

#### marketer_reserved_stock (مخزون الحجز)
```sql
CREATE TABLE marketer_reserved_stock (
    id INT PRIMARY KEY AUTO_INCREMENT,
    marketer_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL DEFAULT 0,
    reserved_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (marketer_id) REFERENCES users(id),
    FOREIGN KEY (product_id) REFERENCES products(id),
    UNIQUE KEY unique_marketer_product (marketer_id, product_id)
);
```

#### marketer_actual_stock (مخزون المسوق الفعلي)
```sql
CREATE TABLE marketer_actual_stock (
    id INT PRIMARY KEY AUTO_INCREMENT,
    marketer_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL DEFAULT 0,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (marketer_id) REFERENCES users(id),
    FOREIGN KEY (product_id) REFERENCES products(id),
    UNIQUE KEY unique_marketer_product (marketer_id, product_id)
);
```

#### store_actual_stock (مخزون المتجر الفعلي)
```sql
CREATE TABLE store_actual_stock (
    id INT PRIMARY KEY AUTO_INCREMENT,
    store_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL DEFAULT 0,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (store_id) REFERENCES stores(id),
    FOREIGN KEY (product_id) REFERENCES products(id),
    UNIQUE KEY unique_store_product (store_id, product_id)
);
```

#### store_reserved_stock (مخزون المتجر قيد التسوية)
```sql
CREATE TABLE store_reserved_stock (
    id INT PRIMARY KEY AUTO_INCREMENT,
    store_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL DEFAULT 0,
    sales_invoice_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (store_id) REFERENCES stores(id),
    FOREIGN KEY (product_id) REFERENCES products(id),
    FOREIGN KEY (sales_invoice_id) REFERENCES sales_invoices(id)
);
```

### 3. جداول الفواتير

#### factory_invoices (فواتير المصنع)
```sql
CREATE TABLE factory_invoices (
    id INT PRIMARY KEY AUTO_INCREMENT,
    invoice_number VARCHAR(50) UNIQUE NOT NULL,
    keeper_id INT NOT NULL,
    manager_id INT,
    total_amount DECIMAL(12,2),
    stamped_image VARCHAR(255),
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (keeper_id) REFERENCES users(id),
    FOREIGN KEY (manager_id) REFERENCES users(id)
);
```

#### factory_invoice_items (تفاصيل فواتير المصنع)
```sql
CREATE TABLE factory_invoice_items (
    id INT PRIMARY KEY AUTO_INCREMENT,
    invoice_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL,
    unit_price DECIMAL(10,2),
    total_price DECIMAL(12,2),
    FOREIGN KEY (invoice_id) REFERENCES factory_invoices(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id)
);
```

#### marketer_requests (طلبات المسوقين)
```sql
CREATE TABLE marketer_requests (
    id INT PRIMARY KEY AUTO_INCREMENT,
    invoice_number VARCHAR(50) UNIQUE NOT NULL,
    marketer_id INT NOT NULL,
    status ENUM('pending', 'approved', 'rejected', 'delivered') DEFAULT 'pending',
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (marketer_id) REFERENCES users(id)
);
```

#### marketer_request_items (تفاصيل طلبات المسوقين)
```sql
CREATE TABLE marketer_request_items (
    id INT PRIMARY KEY AUTO_INCREMENT,
    request_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL,
    FOREIGN KEY (request_id) REFERENCES marketer_requests(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id)
);
```

#### marketer_request_status (حالة الطلب)
```sql
CREATE TABLE marketer_request_status (
    id INT PRIMARY KEY AUTO_INCREMENT,
    request_id INT NOT NULL,
    marketer_id INT NOT NULL,
    keeper_id INT NOT NULL,
    status ENUM('pending', 'approved', 'rejected') NOT NULL,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (request_id) REFERENCES marketer_requests(id),
    FOREIGN KEY (marketer_id) REFERENCES users(id),
    FOREIGN KEY (keeper_id) REFERENCES users(id)
);
```

#### sales_invoices (فواتير البيع)
```sql
CREATE TABLE sales_invoices (
    id INT PRIMARY KEY AUTO_INCREMENT,
    invoice_number VARCHAR(50) UNIQUE NOT NULL,
    marketer_id INT NOT NULL,
    store_id INT NOT NULL,
    total_amount DECIMAL(12,2) NOT NULL,
    status ENUM('pending', 'approved', 'cancelled') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (marketer_id) REFERENCES users(id),
    FOREIGN KEY (store_id) REFERENCES stores(id)
);
```

#### sales_invoice_items (تفاصيل فواتير البيع)
```sql
CREATE TABLE sales_invoice_items (
    id INT PRIMARY KEY AUTO_INCREMENT,
    invoice_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL,
    unit_price DECIMAL(10,2) NOT NULL,
    total_price DECIMAL(12,2) NOT NULL,
    FOREIGN KEY (invoice_id) REFERENCES sales_invoices(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id)
);
```

### 4. جداول التوثيق

#### delivery_confirmations (توثيق الاستلام)
```sql
CREATE TABLE delivery_confirmations (
    id INT PRIMARY KEY AUTO_INCREMENT,
    request_id INT NOT NULL,
    keeper_id INT NOT NULL,
    signed_image VARCHAR(255),
    confirmed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (request_id) REFERENCES marketer_requests(id),
    FOREIGN KEY (keeper_id) REFERENCES users(id)
);
```

#### sales_confirmations (توثيق المبيعات)
```sql
CREATE TABLE sales_confirmations (
    id INT PRIMARY KEY AUTO_INCREMENT,
    sales_invoice_id INT NOT NULL,
    keeper_id INT NOT NULL,
    stamped_invoice_image VARCHAR(255) NOT NULL,
    confirmed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (sales_invoice_id) REFERENCES sales_invoices(id),
    FOREIGN KEY (keeper_id) REFERENCES users(id)
);
```

### 5. جداول الأموال

#### store_debt_ledger (دفتر الذمم)
```sql
CREATE TABLE store_debt_ledger (
    id INT PRIMARY KEY AUTO_INCREMENT,
    store_id INT NOT NULL,
    entry_type ENUM('sale', 'return') NOT NULL,
    reference_id INT NOT NULL,
    amount DECIMAL(12,2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (store_id) REFERENCES stores(id)
);
```

#### store_payments (تسديدات المتاجر)
```sql
CREATE TABLE store_payments (
    id INT PRIMARY KEY AUTO_INCREMENT,
    payment_number VARCHAR(50) UNIQUE NOT NULL,
    store_id INT NOT NULL,
    marketer_id INT NOT NULL,
    keeper_id INT NOT NULL,
    amount DECIMAL(12,2) NOT NULL,
    payment_method ENUM('cash', 'bank_transfer', 'check') DEFAULT 'cash',
    receipt_image VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (store_id) REFERENCES stores(id),
    FOREIGN KEY (marketer_id) REFERENCES users(id),
    FOREIGN KEY (keeper_id) REFERENCES users(id)
);
```

### 6. جداول الإرجاع

#### sales_returns (إرجاعات المبيعات)
```sql
CREATE TABLE sales_returns (
    id INT PRIMARY KEY AUTO_INCREMENT,
    sales_invoice_id INT NOT NULL,
    store_id INT NOT NULL,
    marketer_id INT NOT NULL,
    status ENUM('pending', 'approved') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (sales_invoice_id) REFERENCES sales_invoices(id),
    FOREIGN KEY (store_id) REFERENCES stores(id),
    FOREIGN KEY (marketer_id) REFERENCES users(id)
);
```

#### sales_return_items (تفاصيل الإرجاعات)
```sql
CREATE TABLE sales_return_items (
    id INT PRIMARY KEY AUTO_INCREMENT,
    return_id INT NOT NULL,
    sales_invoice_item_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL,
    unit_price DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (return_id) REFERENCES sales_returns(id) ON DELETE CASCADE,
    FOREIGN KEY (sales_invoice_item_id) REFERENCES sales_invoice_items(id),
    FOREIGN KEY (product_id) REFERENCES products(id)
);
```

#### sales_return_confirmation (توثيق الإرجاع)
```sql
CREATE TABLE sales_return_confirmation (
    id INT PRIMARY KEY AUTO_INCREMENT,
    return_id INT NOT NULL,
    keeper_id INT NOT NULL,
    stamped_image VARCHAR(255),
    confirmed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (return_id) REFERENCES sales_returns(id),
    FOREIGN KEY (keeper_id) REFERENCES users(id)
);
```

### 7. جداول السجلات

#### warehouse_stock_logs (سجل حركات المخزن)
```sql
CREATE TABLE warehouse_stock_logs (
    id INT PRIMARY KEY AUTO_INCREMENT,
    product_id INT NOT NULL,
    action ENUM('add', 'withdraw', 'return') NOT NULL,
    quantity INT NOT NULL,
    invoice_type ENUM('factory', 'marketer_request', 'sales_return') NOT NULL,
    invoice_id INT NOT NULL,
    keeper_id INT NOT NULL,
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES products(id),
    FOREIGN KEY (keeper_id) REFERENCES users(id)
);
```

---

## 🔄 العمليات التفصيلية

### 1. دخول البضاعة من المصنع

#### خطوات التنفيذ:
```javascript
// 1. إنشاء فاتورة المصنع
const createFactoryInvoice = async (invoiceData) => {
    const invoice = await FactoryInvoice.create({
        invoice_number: invoiceData.invoice_number,
        keeper_id: invoiceData.keeper_id,
        stamped_image: invoiceData.stamped_image,
        total_amount: invoiceData.total_amount
    });

    // 2. إضافة تفاصيل المنتجات
    for (const item of invoiceData.items) {
        await FactoryInvoiceItem.create({
            invoice_id: invoice.id,
            product_id: item.product_id,
            quantity: item.quantity,
            unit_price: item.unit_price,
            total_price: item.quantity * item.unit_price
        });

        // 3. تحديث المخزن الرئيسي
        await updateMainStock(item.product_id, item.quantity, 'add');
        
        // 4. تسجيل الحركة
        await logStockMovement({
            product_id: item.product_id,
            action: 'add',
            quantity: item.quantity,
            invoice_type: 'factory',
            invoice_id: invoice.id,
            keeper_id: invoiceData.keeper_id
        });
    }

    return invoice;
};

// دالة تحديث المخزن الرئيسي
const updateMainStock = async (productId, quantity, action) => {
    const stock = await MainStock.findOne({ where: { product_id: productId } });
    
    if (stock) {
        const newQuantity = action === 'add' 
            ? stock.quantity + quantity 
            : stock.quantity - quantity;
        
        await stock.update({ quantity: Math.max(0, newQuantity) });
    } else if (action === 'add') {
        await MainStock.create({ product_id: productId, quantity });
    }
};
```

### 2. طلب المسوق للبضاعة

#### خطوات التنفيذ:
```javascript
// 1. إنشاء طلب المسوق
const createMarketerRequest = async (requestData) => {
    const request = await MarketerRequest.create({
        request_number: generateRequestNumber(),
        marketer_id: requestData.marketer_id,
        status: 'pending'
    });

    // 2. إضافة تفاصيل الطلب
    for (const item of requestData.items) {
        await MarketerRequestItem.create({
            request_id: request.id,
            product_id: item.product_id,
            requested_quantity: item.quantity
        });
    }

    return request;
};

// 3. موافقة أمين المخزن
const approveMarketerRequest = async (requestId, keeperId, approvedItems) => {
    // تحديث حالة الطلب
    await MarketerRequest.update(
        { status: 'approved' },
        { where: { id: requestId } }
    );

    // تسجيل الموافقة
    await MarketerRequestApproval.create({
        request_id: requestId,
        keeper_id: keeperId,
        action: 'approved'
    });

    // نقل الكميات إلى مخزون الحجز
    for (const item of approvedItems) {
        // خصم من المخزن الرئيسي
        await updateMainStock(item.product_id, item.approved_quantity, 'withdraw');
        
        // إضافة لمخزون الحجز
        await addToReservedStock(requestId, item.product_id, item.approved_quantity);
        
        // تحديث الكمية المعتمدة
        await MarketerRequestItem.update(
            { approved_quantity: item.approved_quantity },
            { where: { request_id: requestId, product_id: item.product_id } }
        );
    }
};

// 4. تأكيد الاستلام
const confirmDelivery = async (requestId, keeperId, marketerId, signedImage) => {
    // تسجيل التأكيد
    await DeliveryConfirmation.create({
        request_id: requestId,
        keeper_id: keeperId,
        marketer_id: marketerId,
        signed_image: signedImage
    });

    // نقل من مخزون الحجز إلى المخزون الفعلي
    const reservedItems = await MarketerReservedStock.findAll({
        where: { request_id: requestId }
    });

    for (const item of reservedItems) {
        // إضافة للمخزون الفعلي
        await addToActualStock(marketerId, item.product_id, item.quantity);
        
        // حذف من مخزون الحجز
        await item.destroy();
    }

    // تحديث حالة الطلب
    await MarketerRequest.update(
        { status: 'delivered' },
        { where: { id: requestId } }
    );
};
```

### 3. بيع المسوق للمتجر

#### خطوات التنفيذ:
```javascript
// 1. إنشاء فاتورة البيع
const createSalesInvoice = async (salesData) => {
    const invoice = await SalesInvoice.create({
        invoice_number: generateInvoiceNumber(),
        marketer_id: salesData.marketer_id,
        store_id: salesData.store_id,
        total_amount: salesData.total_amount,
        status: 'pending'
    });

    // 2. إضافة تفاصيل البيع بالأسعار الحالية
    for (const item of salesData.items) {
        const product = await Product.findByPk(item.product_id);
        const totalPrice = item.quantity * product.current_price;

        await SalesInvoiceItem.create({
            invoice_id: invoice.id,
            product_id: item.product_id,
            quantity: item.quantity,
            unit_price: product.current_price, // السعر التاريخي
            total_price: totalPrice
        });

        // 3. خصم مؤقت من مخزون المسوق
        await updateMarketerStock(salesData.marketer_id, item.product_id, item.quantity, 'withdraw');
        
        // 4. إضافة لمخزون المتجر قيد التسوية
        await StoreReservedStock.create({
            store_id: salesData.store_id,
            product_id: item.product_id,
            quantity: item.quantity,
            sales_invoice_id: invoice.id
        });
    }

    return invoice;
};
```

### 4. توثيق البيع وتسجيل الدين

#### خطوات التنفيذ:
```javascript
// 1. توثيق البيع
const confirmSale = async (invoiceId, keeperId, stampedImage) => {
    // تسجيل التوثيق
    await SalesConfirmation.create({
        sales_invoice_id: invoiceId,
        keeper_id: keeperId,
        stamped_invoice_image: stampedImage
    });

    // تحديث حالة الفاتورة
    await SalesInvoice.update(
        { status: 'approved' },
        { where: { id: invoiceId } }
    );

    const invoice = await SalesInvoice.findByPk(invoiceId);
    
    // 2. تسجيل الدين
    await StoreDebtLedger.create({
        store_id: invoice.store_id,
        entry_type: 'sale',
        reference_id: invoiceId,
        amount: invoice.total_amount,
        description: `فاتورة بيع رقم ${invoice.invoice_number}`
    });

    // 3. نقل من مخزون التسوية إلى المخزون الفعلي
    const reservedItems = await StoreReservedStock.findAll({
        where: { sales_invoice_id: invoiceId }
    });

    for (const item of reservedItems) {
        // إضافة للمخزون الفعلي
        await addToStoreActualStock(invoice.store_id, item.product_id, item.quantity);
        
        // حذف من مخزون التسوية
        await item.destroy();
    }
};
```

### 5. التسديد

#### خطوات التنفيذ:
```javascript
// تسجيل التسديد
const recordPayment = async (paymentData) => {
    // إنشاء فاتورة التسديد
    const payment = await StorePayment.create({
        payment_number: generatePaymentNumber(),
        store_id: paymentData.store_id,
        marketer_id: paymentData.marketer_id,
        keeper_id: paymentData.keeper_id,
        amount: paymentData.amount,
        payment_method: paymentData.payment_method,
        receipt_image: paymentData.receipt_image
    });

    // تسجيل في دفتر الذمم
    await StoreDebtLedger.create({
        store_id: paymentData.store_id,
        entry_type: 'payment',
        reference_id: payment.id,
        amount: -paymentData.amount, // سالب لتقليل الدين
        description: `تسديد رقم ${payment.payment_number}`
    });

    return payment;
};
```

### 6. الإرجاع

#### خطوات التنفيذ:
```javascript
// إنشاء طلب إرجاع
const createReturn = async (returnData) => {
    const returnRecord = await SalesReturn.create({
        return_number: generateReturnNumber(),
        sales_invoice_id: returnData.sales_invoice_id,
        store_id: returnData.store_id,
        marketer_id: returnData.marketer_id,
        total_amount: returnData.total_amount,
        status: 'pending',
        reason: returnData.reason
    });

    // إضافة تفاصيل الإرجاع بالأسعار التاريخية
    for (const item of returnData.items) {
        const originalItem = await SalesInvoiceItem.findByPk(item.sales_invoice_item_id);
        
        await SalesReturnItem.create({
            return_id: returnRecord.id,
            sales_invoice_item_id: item.sales_invoice_item_id,
            product_id: originalItem.product_id,
            quantity: item.quantity,
            unit_price: originalItem.unit_price, // السعر التاريخي
            total_price: item.quantity * originalItem.unit_price
        });
    }

    return returnRecord;
};

// موافقة الإرجاع
const approveReturn = async (returnId, keeperId) => {
    const returnRecord = await SalesReturn.findByPk(returnId);
    const returnItems = await SalesReturnItem.findAll({ where: { return_id: returnId } });

    // تحديث حالة الإرجاع
    await returnRecord.update({ status: 'approved' });

    // إعادة الكميات للمسوق
    for (const item of returnItems) {
        await updateMarketerStock(returnRecord.marketer_id, item.product_id, item.quantity, 'add');
        await updateStoreActualStock(returnRecord.store_id, item.product_id, item.quantity, 'withdraw');
    }

    // تقليل الدين
    await StoreDebtLedger.create({
        store_id: returnRecord.store_id,
        entry_type: 'return',
        reference_id: returnId,
        amount: -returnRecord.total_amount, // سالب لتقليل الدين
        description: `إرجاع رقم ${returnRecord.return_number}`
    });
};
```

---

## 🎯 APIs المطلوبة

### 1. APIs المصادقة والمستخدمين
```javascript
POST /api/auth/login
POST /api/auth/logout
GET /api/auth/profile
PUT /api/auth/profile

GET /api/users
POST /api/users
PUT /api/users/:id
DELETE /api/users/:id
```

### 2. APIs المنتجات
```javascript
GET /api/products
POST /api/products
PUT /api/products/:id
DELETE /api/products/:id
PUT /api/products/:id/price
```

### 3. APIs فواتير المصنع
```javascript
GET /api/factory-invoices
POST /api/factory-invoices
GET /api/factory-invoices/:id
PUT /api/factory-invoices/:id
```

### 4. APIs طلبات المسوقين
```javascript
GET /api/marketer-requests
POST /api/marketer-requests
GET /api/marketer-requests/:id
PUT /api/marketer-requests/:id/approve
PUT /api/marketer-requests/:id/reject
POST /api/marketer-requests/:id/confirm-delivery
```

### 5. APIs المبيعات
```javascript
GET /api/sales-invoices
POST /api/sales-invoices
GET /api/sales-invoices/:id
PUT /api/sales-invoices/:id/confirm
PUT /api/sales-invoices/:id/cancel
```

### 6. APIs الإرجاعات
```javascript
GET /api/returns
POST /api/returns
GET /api/returns/:id
PUT /api/returns/:id/approve
PUT /api/returns/:id/reject
```

### 7. APIs التسديدات
```javascript
GET /api/payments
POST /api/payments
GET /api/payments/:id
```

### 8. APIs التقارير
```javascript
GET /api/reports/stock
GET /api/reports/sales
GET /api/reports/debts
GET /api/reports/marketer-performance
```

### 9. APIs المخزون
```javascript
GET /api/stock/main
GET /api/stock/marketer/:id
GET /api/stock/store/:id
GET /api/stock/movements
```

### 10. APIs رفع الملفات
```javascript
POST /api/upload/invoice-image
POST /api/upload/receipt-image
POST /api/upload/signature
```

---

## 🖥️ واجهات المستخدم

### 1. لوحة الإدارة (Admin Panel)
- إدارة المستخدمين
- إدارة المنتجات والأسعار
- التقارير الشاملة
- إعدادات النظام

### 2. لوحة أمين المخزن (Warehouse Keeper Panel)
- فواتير المصنع
- موافقة طلبات المسوقين
- توثيق المبيعات والإرجاعات
- تقارير المخزون

### 3. لوحة المسوق (Salesman Panel)
- طلب البضاعة
- إدارة المخزون الشخصي
- إنشاء فواتير البيع
- متابعة الديون والتسديدات

---

## 🔧 متطلبات تقنية

### Backend
- **Node.js 18+** أو **PHP 8+**
- **Express.js** أو **Laravel**
- **MySQL 8+** أو **PostgreSQL 14+**
- **JWT** للمصادقة
- **Multer** لرفع الملفات
- **Sharp** لمعالجة الصور

### Frontend
- **React 18+** أو **Vue.js 3+**
- **Bootstrap 5** أو **Tailwind CSS**
- **Axios** للـ API
- **Chart.js** للتقارير
- **React Hook Form** للنماذج

### Infrastructure
- **Docker** للحاويات
- **Nginx** كخادم ويب
- **Redis** للتخزين المؤقت
- **PM2** لإدارة العمليات

---

## 📝 ملاحظات التنفيذ

### الأمان
- تشفير كلمات المرور
- حماية رفع الملفات
- التحقق من الصلاحيات
- تسجيل العمليات الحساسة

### الأداء
- فهرسة قاعدة البيانات
- تخزين مؤقت للاستعلامات
- ضغط الصور
- تحسين الاستعلامات

### قابلية التوسع
- تصميم معياري
- APIs منفصلة
- قاعدة بيانات قابلة للتوسع
- دعم المخازن المتعددة مستقبلاً

---

**تاريخ الإنشاء**: ديسمبر 2024  
**الإصدار**: 1.0  
**الحالة**: جاهز للتنفيذ