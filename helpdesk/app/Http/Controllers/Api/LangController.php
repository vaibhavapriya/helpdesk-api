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

