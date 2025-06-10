<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\MailRequest;
use App\Models\Mailconfig;

class MailConfigController extends Controller
{
    //index,update,create
    public function index(){
        $mails=Mailconfig::all();
        return response()->json(['success' => true,'data' =>$mails ]);
    }
    public function store(MailRequest $request){
        
    }
    public function update(MailRequest $request){}
}
