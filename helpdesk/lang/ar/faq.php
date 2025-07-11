<?php

return [
    'title' => 'قاعدة معارف مركز الخدمة',
    'faqs' => [
        [
            'question' => 'كيف يمكن تقديم تذكرة دعم؟',
            'answer' => [
                '1. قم بتسجيل الدخول إلى لوحة إدارة المتجر الإلكتروني.',
                '2. انتقل إلى مركز المساعدة → تقديم تذكرة.',
                '3. املأ التفاصيل (رقم الطلب، نوع المشكلة، إلخ) وقم بالإرسال.',
            ],
        ],
        [
            'question' => 'الطلب عالق في حالة المعالجة',
            'answer' => [
                'أسباب محتملة:',
                'لم يتم تأكيد الدفع – تحقق من بوابة الدفع.',
                'المنتج غير متوفر – قم بإبلاغ العميل.',
                'تأخير في النظام – قم بتحديث الطلب بعد 30 دقيقة.',
            ],
        ],
        // ... يمكن إضافة أسئلة شائعة أخرى هنا
    ],
    'support_alert' => '<strong>تحتاج إلى مزيد من الدعم؟</strong> إذا لم تجد إجابة، يرجى رفع تذكرة وشرح المشكلة.',
];
