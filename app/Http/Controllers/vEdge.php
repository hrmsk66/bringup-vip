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
            'port' => 'required',
            'cert' => 'required'
        ];
        $this->validate($request, $validate_rule);

        $ip   = $request->input('ip');
        $port = $request->input('port');
        $cert = $request->input('cert');

        file_put_contents('./rootca/temp/cacert.pem', $cert . "\n");

        $cmd1 = "./scripts/pushRootCert.sh $ip $port";
        $cmd2 = "./scripts/replaceRootCert.sh $ip $port";
        exec($cmd1, $r1);
        exec($cmd2, $r2);
        return ['target' => $ip . ':' . $port, 'cmd1' => $cmd1, 'r1' => $r1, 'cmd2' => $cmd2, 'r2' => $r2 ];
    }
    public function fetch(Request $request)
    {
        $validate_rule = [
            'vmanageIP' => 'required',
            'vmanagePort' => 'required',
        ];
        $this->validate($request, $validate_rule);

        $vmanageIP   = $request->input('vmanageIP');
        $vmanagePort   = $request->input('vmanagePort');

        // $cmd1 = "curl --noproxy '*' -s -u admin:admin -k https://${vmanageIP}:443/dataservice/certificate/vedge/list\?model\=vedge-cloud\&state\=tokengenerated | jq -r '[.data[range(0;20)] | { uuid: .uuid, token: .serialNumber }]' > ./vedgelist";
        $cmd1 = "curl --noproxy '*' -s -u admin:admin -k https://${vmanageIP}:${vmanagePort}/dataservice/certificate/vedge/list\?model\=vedge-cloud\&state\=tokengenerated | jq -r '[.data[] | { uuid: .uuid, token: .serialNumber }]' > ./vedgelist";
        exec($cmd1, $r1);
        $json = file_get_contents('./vedgelist');
        $vedgelist = json_decode($json, true);

        return ['cmd1' => $cmd1, 'vedgelist' => $vedgelist];
    }

    public function activate(Request $request)
    {
       $validate_rule = [
            'ip' => 'required',
            'port' => 'required',
            'uuid' => 'required',
            'token' => 'required'
        ];
        $this->validate($request, $validate_rule);

        $ip   = $request->input('ip');
        $port   = $request->input('port');
        $uuid = $request->input('uuid');
        $token = $request->input('token');

        $cmd1 = "./scripts/activateDevice.sh $ip $port $uuid $token";
        exec($cmd1, $r1);
        return ['target' => $ip . ':' . $port, 'cmd1' => $cmd1, 'r1' => $r1 ];
    }
}
