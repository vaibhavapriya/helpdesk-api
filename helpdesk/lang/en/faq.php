<?php
return [
    'title' => 'Service Desk Knowledge Base',
    'faqs' => [
        [
            'question' => 'How to Submit a Support Ticket?',
            'answer' => [
                '1. Log in to the E-commerce Admin Panel.',
                '2. Navigate to Help Center → Submit a Ticket.',
                '3. Fill in details (Order ID, issue type, etc.) and submit.',
            ],
        ],
        [
            'question' => 'Order Stuck in Processing',
            'answer' => [
                'Possible reasons:',
                'Payment Not Confirmed – Verify with the payment gateway.',
                'Out of Stock – Notify the customer.',
                'System Delay – Refresh the order after 30 minutes.',
            ],
        ],
        // ... add other FAQs here
    ],
    'support_alert' => '<strong>Need more support?</strong> If you did not find an answer, please raise a ticket describing the issue.',
];
