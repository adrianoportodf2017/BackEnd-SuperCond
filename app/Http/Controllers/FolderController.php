<?php

namespace App\Http\Controllers;

use App\Models\Folder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Exception;



class FolderController extends Controller
{
    public function index()
    {
        $folders = Folder::with('children')->whereNull('folder_id')->get();
        return response()->json($folders);
    }

    public function store(Request $request)
    {
        $folder = Folder::create($request->all());
        return response()->json($folder);
    }

    public function show($id)
    {
        $folder = Folder::with('children')->find($id);
        return response()->json($folder);
    }

    public function update(Request $request, $id)
    {
        $folder = Folder::find($id);
        $folder->update($request->all());
        return response()->json($folder);
    }

    public function destroy($id)
    {
        Folder::destroy($id);
        return response()->json(['message' => 'Folder deleted']);
    }
}
