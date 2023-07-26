<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use App\Models\Favourite;
use Illuminate\Http\Request;

class FavourtieController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $favourites = Favourite::where("user_id", auth()->id())
        ->latest("id")
        ->get();

        return response()->json([
            $favourites
        ]);
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
    public function store(Request $request, $id)
    {
        $contact = Contact::findOrFail($id);
        $contactId = $contact->id;
        if (Favourite::where("contact_id", "=", $contactId)->first()) {
            return response()->json([], 406);
        }
        $favourite = Favourite::create([
            "user_id" => auth()->id(),
            "contact_id" => $contact->id,
        ]);

        return response()->json([
            $favourite
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Favourite $favourite)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Favourite $favourite)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Favourite $favourite)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $favourite = Favourite::findOrFail($id);
        $favourite->delete();
        return response()->json([
            "message" => "successful"
        ]);
    }
}
