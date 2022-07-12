<?php

namespace App\Http\Controllers\Ticket;

use App\TicketService\Events;
use Illuminate\Http\Request;

class TicketController
{

    public function index()
    {
        return view('panel.ticket.index');
    }

    public function takeTicket(Request $request)
    {
        try {

            $event = new Events();

            $event = $event->getAvailabilities($request->form);

        }catch (\Exception $exception){

            return response()->json([
                'status' => false,
                'data' => $exception->getMessage()
            ]);

        }

        return response()->json([
            'status' => true,
            'data' => $event
        ]);

    }
}
