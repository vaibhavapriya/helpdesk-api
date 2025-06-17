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
    public function store(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email',
            'name' => 'required|string',
            'passcode' => 'required|string',
        ]);

        $mail = MailConfig::create([
            'mail_from_name'    => $validated['name'],
            'mail_from_address' => $validated['email'],
            'host'              => 'smtp.gmail.com',
            'port'              => '587',
            'encryption'        => 'tls',
            'username'          => $validated['email'],
            'password'          => $validated['passcode'],
            'active'            => false,
        ]);

        return response()->json(['success' => true, 'message' => 'Mail configuration added.']);
    }
    public function update(String $id){
        MailConfig::query()->update(['active' => 0]);

        // Find the one to activate and set active = 1
        $mail = MailConfig::findOrFail($id);
        $mail->update(['active' => 1]);

        return response()->json(['success' => true, 'message' => 'Mail configuration updated.']);
    }
    public function destroy(String $id)
    {
        //$id = $request->query('id');
        $config = MailConfig::find($id);

        if (!$config) {
            return response()->json(['status' => 'error'], 404);
        }

        $config->delete();

        return response()->json(['status' => 'success']);
    }
}
