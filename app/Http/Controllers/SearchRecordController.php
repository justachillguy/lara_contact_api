<?php

namespace App\Http\Controllers;

use App\Models\SearchRecord;
use Illuminate\Http\Request;

class SearchRecordController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $searchRecords = SearchRecord::where("user_id", auth()->id())
        ->latest("id")
        ->limit(5)
        ->get();
        return response()->json([
            "records" => $searchRecords
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        SearchRecord::findOrFail($id)->delete();
        return response()->json([], 204);
    }
}
