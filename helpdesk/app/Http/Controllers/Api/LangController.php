<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class LangController extends Controller
{
    public function getFaq()
    {
        // Locale is already set by middleware
        $data = [
            'title' => __('faq.title'),
            'faqs' => __('faq.faqs'),
            'support_alert' => __('faq.support_alert'),
        ];

        return response()->json($data);
    }

    public function setLocale(Request $request)
    {
        $request->validate([
            'locale' => 'required|string|in:en,es', // allow only supported locales
        ]);

        $locale = $request->input('locale');

        // Save locale in session
        $request->session()->put('app_locale', $locale);

        // Also return success message
        return response()->json([
            'message' => 'Locale updated',
            'locale' => $locale,
        ]);
    }
    public function getNewticket()
    {
        return response()->json([
            'new_ticket' => __('tickets.new_ticket'),
            'info_text' => __('tickets.info_text'),
            'email' => __('tickets.email'),
            'title' => __('tickets.title'),
            'priority' => __('tickets.priority'),
            'high' => __('tickets.high'),
            'medium' => __('tickets.medium'),
            'low' => __('tickets.low'),
            'department' => __('tickets.department'),
            'description' => __('tickets.description'),
            'attachment' => __('tickets.attachment'),
            'terms' => __('tickets.terms'),
            'submit' => __('tickets.submit'),
            'placeholders' => [
                'email' => __('tickets.email_placeholder'),
                'title' => __('tickets.title_placeholder'),
                'department' => __('tickets.department_placeholder'),
                'description' => __('tickets.description_placeholder'),
            ]
        ]);
    }
    public function getTickets(){

        return response()->json([
            'list_title' => __('tickets.list_title'),
            'title' => __('tickets.title'),
            'priority' => __('tickets.priority'),
            'status' => __('tickets.status'),
            'department' => __('tickets.department'),
            'actions' => __('tickets.actions'),
            'edit' => __('tickets.edit'),
            'delete' => __('tickets.delete'),
            'no_tickets' => __('tickets.no_tickets'),
            'previous' => __('tickets.previous'),
            'next' => __('tickets.next'),
        ]);
    }

    public function getFaqByLanguage($lang)
    {
        app()->setLocale($lang); // Set locale dynamically

        $faqs = config('faq'); // optional: load config file or just get from lang files directly

        // Or directly from lang files:
        $faqs = [
            'title' => __('faq.title'),
            'faqs' => __('faq.faqs'),
            'support_alert' => __('faq.support_alert'),
        ];

        return response()->json($faqs);
    }

}

