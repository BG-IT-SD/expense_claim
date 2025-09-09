<?php

namespace App\Http\Controllers\Back;

use App\Http\Controllers\Controller;
use App\Models\ApproveStaff;
use Illuminate\Http\Request;

class ApporveDriverController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {

     $staffapproves = ApproveStaff::whereIn('step', [1, 2])
        ->where('extype', 2)
        ->where('deleted', 0)
        ->orderBy('step')
        ->get();

        return view('back.driverapprove.index' ,compact('staffapproves'));

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
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
