<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        //return parent::toArray($request);
        return [
            'success' => true,
            'data' => [
                'id' => $this->id,
                'role' => $this->role, // ensure 'role' exists on your User model
            ],
            // add other fields you want to expose
        ];
    }
}
// {
//   "success": true,
//   "data": {...},            // Actual payload, if any
//   "error": null,            // Or error info if failed
//   "message": "Optional message for additional context",
//   "meta": {                 // Pagination info, timestamps, etc.
//     "page": 1,
//     "pageSize": 10,
//     "total": 100
//   }
// }