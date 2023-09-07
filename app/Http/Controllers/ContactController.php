<?php

namespace App\Http\Controllers;

use App\Http\Resources\ContactDetailResource;
use App\Http\Resources\ContactResource;
use App\Models\Contact;
use App\Models\SearchRecord;
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
        $contacts = Contact::when(request()->has("keyword"), function ($query) {
            $query->where(function (Builder $builder) {
                $keyword = request()->keyword;

                SearchRecord::create([
                    "user_id" => auth()->id(),
                    "keyword" => $keyword,
                ]);

                $builder->where("name", "LIKE", "%" . $keyword . "%");
                $builder->orWhere("phone_number", "LIKE", "%" . $keyword . "%");
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
            "email" => ["nullable", "email", "unique:contacts,email"],
            "company" => ["nullable", "string", "min:2", "max:255"],
            "job_title" => ["nullable", "string", "min:3", "max:255"],
            "birthday" => ["nullable", "date"],
            "notes" => ["nullable", "max:255"],
            "photo" => ['mimes:jpg,png,pdf', 'max:2048', "file"],
        ]);

        // Contact::create([
        //     "name" => $request->name,
        //     "country_code" => $request->country_code,
        //     "phone_number" => $request->phone_number,
        //     "user_id" => Auth::id(),
        // ]);

        $contact = new Contact;

        $contact->name = $request->name;
        $contact->country_code = $request->country_code;
        $contact->phone_number = $request->phone_number;
        $contact->user_id = auth()->id();

        if ($request->has("email")) {
            $contact->email = $request->email;
        }

        if ($request->has("company")) {
            $contact->company = $request->company;
        }

        if ($request->has("job_title")) {
            $contact->job_title = $request->job_title;
        }

        if ($request->has("birthday")) {
            $contact->birthday = $request->birthday;
        }

        if ($request->has("notes")) {
            $contact->notes = $request->notes;
        }

        if ($request->has("photo")) {
            $contact->email = $request->email;
        }

        $contact->save();

        return response()->json(
            [
                "message" => "A new contact has been saved to your lists."
            ]
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(Contact $contact)
    {
        Gate::authorize("view", $contact);

        if (is_null($contact)) {
            return response()->json([], 404);
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
            "phone_number" => "nullable|min:7|max:15",
            "email" => ["nullable", "email", "unique:contacts,email"],
            "company" => ["nullable", "string", "min:2", "max:255"],
            "job_title" => ["nullable", "string", "min:3", "max:255"],
            "birthday" => ["nullable", "date"],
            "notes" => ["nullable", "max:255"],
            "photo" => ["nullable", 'mimes:jpg,png,pdf', 'max:2048', "file"],
        ]);

        if ($request->has('name')) {
            $contact->name = $request->name;
        }

        if ($request->has('country_code')) {
            $contact->country_code = $request->country_code;
        }

        if ($request->has('phone_number')) {
            $contact->phone_number = $request->phone_number;
        }

        if ($request->has("email")) {
            $contact->email = $request->email;
        }

        if ($request->has("company")) {
            $contact->company = $request->company;
        }

        if ($request->has("job_title")) {
            $contact->job_title = $request->job_title;
        }

        if ($request->has("birthday")) {
            $contact->birthday = $request->birthday;
        }

        if ($request->has("notes")) {
            $contact->notes = $request->notes;
        }

        if ($request->has("photo")) {
            $contact->email = $request->email;
        }


        $contact->update();

        return response()->json([
            "message" => "Tje information of a contact from your list has been updated."
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
            "message" => "A contact has been removed from your list."
        ]);
    }

    public function multipleDelete(Request $request)
    {
        // Gate::authorize("delete", $contact);
        if (!is_array($request->ids)) {
            return response()->json([
                "message" => "sth wrong"
            ]);
        }
        $idsToDelete = $request->ids;
        $contactQuan = count($idsToDelete);
        Contact::where("user_id", auth()->id())->whereIn("id", $idsToDelete)->delete();

        return response()->json([
            "message" => $contactQuan . " contacts have been removed from your list."
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

    public function restoreAll()
    {
        // Gate::authorize("restore", App\Models\Contact::class);
        // $contacts =
        Contact::onlyTrashed()
            ->where("user_id", auth()->id())
            ->restore();
        // ->restore();
        // return $contacts;
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

    public function emptyBin()
    {
        // Gate::authorize("forceDelete", App\Models\Contact::class);
        Contact::onlyTrashed()
            ->where("user_id", auth()->id())
            ->forceDelete();

        return response()->json([
            "message" => "successful"
        ]);
    }

    public function bin()
    {
        $contact = Contact::onlyTrashed()->where("user_id", auth()->id())->get();
        return response()->json(
            [
                "contact" => $contact,
            ]
        );
    }
}
