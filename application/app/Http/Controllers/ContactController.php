<?php

namespace App\Http\Controllers;

use App\Mail\ContactMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    public function send(Request $request)
    {
        $validated = $request->validate([
            'subject' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email:rfc,dns', 'max:255'],
            'phone' => ['required', 'string', 'regex:/^(\\+212|0)[5-7][0-9]{8}$/'],
            'texte' => ['required', 'string', 'max:5000'],
        ]);

        Mail::to('carloc@agency.com')->send(new ContactMail(
            $validated['subject'],
            $validated['email'],
            $validated['phone'],
            $validated['texte']
        ));

        return redirect('/')->with('success', 'Votre message a été envoyé');
    }
}
