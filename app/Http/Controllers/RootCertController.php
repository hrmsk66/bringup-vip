<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class RootCertController extends Controller
{
    public function load()
    {
      $cert = file_get_contents('./rootca/cacert.pem');
      return ['cert' => $cert];
    }

    public function push(Request $request)
    {
        $validate_rule = [
            'ip' => 'required',
            'cert' => 'required'
        ];
        $this->validate($request, $validate_rule);

        $ip   = $request->input('ip');
        $cert = $request->input('cert');

        file_put_contents('./rootca/temp/cacert.pem', $cert . "\n");

        $cmd1 = "./scripts/pushRootCert.sh $ip";
        $cmd2 = "./scripts/replaceRootCert.sh $ip";
        exec($cmd1, $r1);
        exec($cmd2, $r2);
        return ['target' => $ip, 'cmd1' => $cmd1, 'r1' => $r1, 'cmd2' => $cmd2, 'r2' => $r2 ];
    }
}