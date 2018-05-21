<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class NewCertController extends Controller
{
    public function create(Request $request)
    {
        $validate_rule = [
            'csr' => 'required'
        ];
        $this->validate($request, $validate_rule);

        $csr   = $request->input('csr');

        file_put_contents('./rootca/temp/csr.pem', $csr);

        $cmd1 = "./scripts/genCert.sh";
        exec($cmd1, $r1);

        $newcert = file_get_contents('./rootca/temp/newcert.crt');
        return ['cmd1' => $cmd1, 'r1' => $r1, 'newcert' => $newcert];
    }
}
