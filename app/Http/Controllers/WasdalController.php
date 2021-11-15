<?php

namespace App\Http\Controllers;

use App\Models\NPWPD;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class WasdalController extends Controller
{
    public function getListNPWPD()
    {
        $get = NPWPD::with('nopd')->get();

        return response()->json([
            'status' => true,
            'code' => 200,
            'message' => 'Data ditemukan',
            'data' => $get
        ]);
    }
}
