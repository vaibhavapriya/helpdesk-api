<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ErrorLog extends Model
{
    /** @use HasFactory<\Database\Factories\ErrorLogFactory> */
    use HasFactory;

    protected $fillable = [
        'error_message',
        'stack_trace',
        'user_id',
        'method',
        'route',
    ];

}
// use App\Models\ErrorLog;
// use Illuminate\Database\QueryException;
// use Exception;
// use Illuminate\Support\Facades\Log;

// public function show(string $id)
// {
//     try {
//         // Find the profile by id
//         $profile = Profile::with(['image'])->findOrFail($id);
//         $this->authorize('view', $profile);

//         return response()->json([
//             'success' => true,
//             'data' => $profile,
//         ]);
        
//     } catch (QueryException $e) {
//         // Log the error to the database
//         ErrorLog::create([
//             'error_message' => $e->getMessage(),
//             'stack_trace' => $e->getTraceAsString(),
//             'user_id' => auth()->id(),
//             'method' => 'ProfileController@show',
//             'route' => request()->route()->getName(), // Or use request()->fullUrl()
//         ]);

//         // Optionally log it in the files too (this can be removed if you only want DB logging)
//         Log::error('Database Query Error: ' . $e->getMessage(), [
//             'exception' => $e,
//             'user_id' => auth()->id(),
//         ]);

//         return response()->json(['success' => false, 'message' => 'Database error occurred.'], 500);
        
//     } catch (Exception $e) {
//         // Log the general error to the database
//         ErrorLog::create([
//             'error_message' => $e->getMessage(),
//             'stack_trace' => $e->getTraceAsString(),
//             'user_id' => auth()->id(),
//             'method' => 'ProfileController@show',
//             'route' => request()->route()->getName(),
//         ]);

//         // Optionally log it in the files too (this can be removed if you only want DB logging)
//         Log::error('General Error in ProfileController@show: ' . $e->getMessage(), [
//             'exception' => $e,
//             'user_id' => auth()->id(),
//         ]);

//         return response()->json(['success' => false, 'message' => 'Unexpected error occurred.'], 500);
//     }
//}

