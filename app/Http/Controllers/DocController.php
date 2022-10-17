<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Doc;

class DocController extends Controller
{
    public function getAll()
    {
        $array = ['error' => ''];

        $docs = Doc::all();

        foreach($docs as $docKey => $docValue) {
            $docs[$docKey]['fileurl'] = $docValue['fileurl'];
        }

        $array['list'] = $docs;

        return $array;
    }

}
