<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\ContactMail;
class EmailController extends Controller
{
    public function sendEmail(Request $request)
    {
        $data = $request->validate([
            'email' => 'required|email',
            'subject' => 'required',
            'description' => 'required',
        ]);
    
        // Send the email using the ContactMail Mailable
        Mail::to($data['email'])->send(new ContactMail($data));
    
        // Return a response
        return response()->json(['message' => 'Email sent successfully']);
    }
    
    
}
