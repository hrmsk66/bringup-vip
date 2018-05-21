<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class Controllers extends Controller
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
    public function resync(Request $request)
    {
        $validate_rule = [
            'vmanageIP' => 'required'
        ];
        $this->validate($request, $validate_rule);

        $vmanageIP   = $request->input('vmanageIP');

        $cmd1 = "curl --noproxy '*' -s -u admin:admin -k https://${vmanageIP}:443/dataservice/system/device/sync/rootcertchain > ./resyncResult";
        exec($cmd1, $r1);
        $json = file_get_contents('./resyncResult');
        $r1 = json_decode($json, true);

        return ['cmd1' => $cmd1, 'r1' => $r1];
    }
    public function csr(Request $request)
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
