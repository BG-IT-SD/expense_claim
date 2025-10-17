<?php

namespace App\Http\Controllers\Back;

use App\Http\Controllers\Controller;
use App\Models\MessageAlert;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $message = MessageAlert::where('status', 1)
            ->where('deleted', 0)
            ->first();

        if ($message && $message->message) {
            $msg = trim($message->message);

            //ตรวจว่าเป็น Base64 จริงหรือไม่ (กันข้อความเก่า)
            $isBase64 = base64_encode(base64_decode($msg, true)) === $msg;

            if ($isBase64) {
                $decoded = base64_decode($msg);
                if ($decoded !== false) {
                    $message->message = $decoded; // ส่ง HTML จริงให้ Blade
                }
            }
        }

        return view('back.message.index', compact('message'));
    }



    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)

    {
        $validated = $request->validate([
            'message' => ['required', 'string'],   // HTML จาก Quill
        ]);

        $messageAlert = MessageAlert::where('id', $id)
            ->where('deleted', 0)
            ->firstOrFail();

        $messageAlert->message = $validated['message'];


        $messageAlert->save();

        // return back()->with('success', 'Update Message Alert เรียบร้อยแล้ว');
        return redirect()
            ->route('MessageAlert.index')
            ->with('success', 'Update Message Alert เรียบร้อยแล้ว');
    }






    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
