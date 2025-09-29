<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use Illuminate\Http\Request;

class SupportController extends Controller
{
    public function index()
    {
        $tickets = Ticket::where('user_id', auth()->user()->id)->latest()->paginate(10)->withQueryString();
        return inertia('Client/Support/Index', [
            'tickets' => $tickets,
        ]);
    }

    public function show($id)
    {
        $ticket = Ticket::findOrFail($id);
        $ticket->load('messages');
        return inertia('Client/Support/Show', [
            'ticket' => $ticket,
        ]);
    }

    public function create()
    {
        return inertia('Client/Support/Create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'subject' => 'required',
            'message' => 'required',
        ]);
        
        $ticket = Ticket::create([
            'subject' => $request->subject,
            'user_id' => auth()->user()->id,
        ]);
        $ticket->messages()->create([
            'message' => $request->message,
            'is_admin' => false,
        ]);
        return redirect()->route('client.support.index');
    }

    public function reply($id, Request $request)
    {
        $ticket = Ticket::findOrFail($id);
        $ticket->messages()->create([
            'message' => $request->message,
            'is_admin' => false,
        ]);
        return redirect()->back();
    }
}
