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
            'locale' => 'required|string|in:en,es,ar', // allow only supported locales
        ]);

        $locale = $request->input('locale');

        // Save locale in session
        $request->session()->put('app_locale', $locale);
        //session(['locale' => $locale]);
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
    public function getEditTranslations()
    {
        return response()->json([
            'page_title' => __('tickets.edit_title'),
            'title' => __('tickets.title'),
            'description' => __('tickets.description'),
            'priority' => __('tickets.priority'),
            'department' => __('tickets.department'),
            'status' => __('tickets.status'),
            'attachment' => __('tickets.attachment'),
            'change_attachment' => __('tickets.change_attachment'),
            'update' => __('tickets.update'),
            'low' => __('tickets.low'),
            'medium' => __('tickets.medium'),
            'high' => __('tickets.high'),
            'open' => __('tickets.open'),
            'closed' => __('tickets.closed'),
        ]);
    }
    public function getViewTranslations()
    {
        return response()->json([
            'attachment' => __('tickets.attachment'),
            'no_attachment' => __('tickets.no_attachment'),
            'replies' => __('tickets.replies'),
            'reply_placeholder' => __('tickets.reply_placeholder'),
            'submit' => __('tickets.submit'),
            'back' => __('tickets.back'),
            'description' => __('tickets.description'),
            'status' => __('tickets.status'),
            'priority' => __('tickets.priority'),
            'requester' => __('tickets.requester'),
            'open' => __('tickets.open'),
            'closed' => __('tickets.closed'),
            'pending' => __('tickets.pending'),
            'low' => __('tickets.low'),
            'medium' => __('tickets.medium'),
            'high' => __('tickets.high'),
        ]);
    }

    public function getProfileTranslations()
    {
        return response()->json([
            'user_profile' => __('profile.title'),
            'first_name' => __('profile.first_name'),
            'last_name' => __('profile.last_name'),
            'phone' => __('profile.phone'),
            'email' => __('profile.email'),
            'profile_picture' => __('profile.profile_picture'),
            'edit' => __('profile.edit'),
            'save' => __('profile.save'),
            'cancel' => __('profile.cancel'),
            'old_password' => __('profile.old_password'),
            'new_password' => __('profile.new_password'),
            'confirm_password' => __('profile.confirm_password'),
            'change_password' => __('profile.change_password'),
        ]);
    }
    public function getHeader()
    {
        return response()->json([
            'submit_ticket' => __('nav.submit_ticket'),
            'knowledgebase' => __('nav.knowledgebase'),
            'login' => __('nav.login'),
            'my_ticket' => __('nav.my_ticket'),
            'my_profile' => __('nav.my_profile'),
            'logout' => __('nav.logout'),
            'admin_portal' => __('nav.admin_portal'),
        ]);
    }
    public function getHomegrid()
    {
        return response()->json([
            'submit_ticket' => __('nav.submit_ticket'),
            'knowledgebase' => __('nav.knowledgebase'),
            'my_ticket' => __('nav.my_ticket'),
            'register' => __('nav.register'),
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

