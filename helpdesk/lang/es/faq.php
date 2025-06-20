<?php
return [
    'title' => 'Base de Conocimiento del Servicio de Soporte',
    'faqs' => [
        [
            'question' => '¿Cómo enviar un ticket de soporte?',
            'answer' => [
                '1. Inicie sesión en el panel de administración de comercio electrónico.',
                '2. Navegue a Centro de ayuda → Enviar un ticket.',
                '3. Complete los detalles (ID de pedido, tipo de problema, etc.) y envíe.',
            ],
        ],
        [
            'question' => 'Pedido atascado en procesamiento',
            'answer' => [
                'Posibles razones:',
                'Pago no confirmado – Verifique con la pasarela de pago.',
                'Sin stock – Notifique al cliente.',
                'Retraso del sistema – Actualice el pedido después de 30 minutos.',
            ],
        ],
        // Add other FAQs here...
    ],
    'support_alert' => '<strong>¿Necesita más ayuda?</strong> Si no encontró una respuesta, por favor cree un ticket describiendo el problema.',
];
