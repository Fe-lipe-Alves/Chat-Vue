<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class MessageControlle extends Controller
{
    public function listMessages(User $user)
    {
        $userFrom = Auth::id();
        $userTo = $user->id;

        $messages = Message::query()
            ->where(
                function ($query) use ($userFrom, $userTo) {
                    $query->where([
                        'from' => $userFrom,
                        'to' => $userTo
                    ]);
                }
            )->orWhere(
                function ($query) use ($userFrom, $userTo) {
                    $query->where([
                        'from' => $userTo,
                        'to' => $userFrom
                    ]);
                }
            )
            ->orderBy('created_at')->get();

        return response()->json([
            'messages' => $messages
        ], Response::HTTP_OK);
    }

    public function store(Request $request)
    {
        $message = new Message();
        $message->from = Auth::id();
        $message->to = $request->to;
        $message->content = filter_var($request->get('content'), FILTER_SANITIZE_STRIPPED);
        $message->save();
    }
}
