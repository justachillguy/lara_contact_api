<?php

namespace App\Http\Controllers;

use App\Http\Resources\ContactDetailResource;
use App\Http\Resources\ContactResource;
use App\Models\Contact;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class ContactController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $contacts = Contact::when(request()->has("q"), function ($query) {
            $query->where(function (Builder $builder) {
                $q = request()->q;

                $builder->where("name", "LIKE", "%" . $q . "%");
                $builder->orWhere("phone_number", "LIKE", "%" . $q . "%");
            });
        })
        ->where("user_id", Auth::id())
        ->latest("id")
        ->paginate(5)
        ->withQueryString();

        return ContactResource::collection($contacts);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            "name" => "required",
            "country_code" => ["required", "min:1", "max:256"],
            "phone_number" => "required",
        ]);

        $contact = Contact::create([
            "name" => $request->name,
            "country_code" => $request->country_code,
            "phone_number" => $request->phone_number,
            "user_id" => Auth::id(),
        ]);

        return $request;
    }

    /**
     * Display the specified resource.
     */
    public function show(Contact $contact)
    {
        Gate::authorize("view", $contact);

        if (is_null($contact)) {
            return response()->json([],404);
        }
        return new ContactDetailResource($contact);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Contact $contact)
    {
        Gate::authorize("update", $contact);

        $request->validate([
            "name" => "nullable|min:3|max:20",
            "country_code" => "nullable|integer|min:1|max:265",
            "phone_number" => "nullable|min:7|max:15"
        ]);

        if (is_null($contact)) {
            return response()->json([],404);
        }

        // $contact->update([
        //     "name" => $request->name,
        //     "country_code" => $request->country_code,
        //     "phone_number" => $request->phone_number,
        // ]);


        if($request->has('name')){
            $contact->name = $request->name;
        }

        if($request->has('country_code')){
            $contact->country_code = $request->country_code;
        }

        if($request->has('phone_number')){
            $contact->phone_number = $request->phone_number;
        }

        $contact->update();

        return response()->json([
            $contact
        ]);

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Contact $contact)
    {
        Gate::authorize("delete", $contact);

        $contact->delete();
        return response()->json([
            "message" => "the contact has been deleted"
        ]);
    }

    public function trash()
    {
        $contact = Contact::onlyTrashed()
        ->where("user_id", auth()->id())
        ->get();

        return response()->json([
            $contact
        ]);
    }

    public function restore($id)
    {
        // Gate::authorize("restore", App\Models\Contact::class);
        Contact::onlyTrashed()
        ->where("user_id", auth()->id())
        ->findOrFail($id)
        ->restore();

        return response()->json([
            "message" => "successful"
        ]);
    }

    public function forceDelete($id)
    {
        // Gate::authorize("forceDelete", App\Models\Contact::class);
        $contact = Contact::withTrashed()->findOrFail($id);
        $contact->forceDelete();


        return response()->json([
            "message" => "successful"
        ]);
    }
}
