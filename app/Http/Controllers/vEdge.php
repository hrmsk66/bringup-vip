<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class vEdge extends Controller
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
    public function fetch(Request $request)
    {
        $validate_rule = [
            'vmanageIP' => 'required'
        ];
        $this->validate($request, $validate_rule);

        $vmanageIP   = $request->input('vmanageIP');

        $cmd1 = "curl --noproxy '*' -s -u admin:admin -k https://${vmanageIP}:443/dataservice/certificate/vedge/list\?model\=vedge-cloud\&state\=tokengenerated | jq -r '[.data[range(0;20)] | { uuid: .uuid, token: .serialNumber }]' > ./vedgelist";
        exec($cmd1, $r1);
        $json = file_get_contents('./vedgelist');
        $vedgelist = json_decode($json, true);

        return ['cmd1' => $cmd1, 'vedgelist' => $vedgelist];
    }

    public function activate(Request $request)
    {
       $validate_rule = [
            'ip' => 'required',
            'uuid' => 'required',
            'token' => 'required'
        ];
        $this->validate($request, $validate_rule);

        $ip   = $request->input('ip');
        $uuid = $request->input('uuid');
        $token = $request->input('token');

        $cmd1 = "./scripts/activateDevice.sh $ip $uuid $token";
        exec($cmd1, $r1);
        return ['target' => $ip, 'cmd1' => $cmd1, 'r1' => $r1 ];
    }
}
