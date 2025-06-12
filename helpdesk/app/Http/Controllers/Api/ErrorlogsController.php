<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\ErrorLog;
use Illuminate\Support\Str;

class ErrorlogsController extends Controller
{
    public function index(){
        $errorlogs = ErrorLog::simplePaginate(15);//->with('replies') 
        return response()->json([
        'success' => true,
        'data' => $errorlogs->items(),
        'meta' => [
            'current_page' => $errorlogs->currentPage(),
            'next_page_url' => $errorlogs->nextPageUrl(),
            //'last_page' => $errorlogs->lastPage(),
            'per_page' => $errorlogs->perPage(),
            'prev_page_url' => $errorlogs->previousPageUrl(),
            // 'total' => $errorlogs->total(),only for paginate        
        ]
        ]);
    }
}
