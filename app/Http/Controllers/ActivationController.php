<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ActivationController extends Controller
{
    public function fetch(Request $request)
    {
        $validate_rule = [
            'vmanageIP' => 'required'
        ];
        $this->validate($request, $validate_rule);

        $vmanageIP   = $request->input('vmanageIP');

        $cmd1 = "curl -s -u admin:admin -k https://${vmanageIP}:443/dataservice/certificate/vedge/list\?model\=vedge-cloud\&state\=tokengenerated | jq -r '[.data[range(0;20)] | { uuid: .uuid, token: .serialNumber }]' > ./vedgelist";
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
