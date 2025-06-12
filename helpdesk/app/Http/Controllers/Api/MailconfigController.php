<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\MailRequest;
use App\Models\MailConfig;

class MailconfigController extends Controller
{
    //index,update,create
    public function index(){
        $mails=MailConfig::all();
        return response()->json(['success' => true,'data' =>$mails ]);
    }
    public function store(MailRequest $request){
        
    }
    public function update(String $id){
        MailConfig::query()->update(['active' => 0]);

        // Find the one to activate and set active = 1
        $mail = MailConfig::findOrFail($id);
        $mail->update(['active' => 1]);

        return response()->json(['success' => true, 'message' => 'Mail configuration updated.']);
    }
}
