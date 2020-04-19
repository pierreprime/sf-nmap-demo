<?php

namespace App\Service;

class TcpService
{
    public function scanOpenPorts($host, $from, $to){
        // validation
        $error = '';
        $openPorts = [];
        if (empty($host) || empty($from) || empty($to)) {
            $error = "Incomplete data, go back choose IP address and port range";
        } else if (!(filter_var($host, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4))) {
            $error = "This IP address is not valid !";
        } else if (!(is_numeric($from)) || !(is_numeric($to))) {
            $error = "Entered data is not port number";
        } else if ($from > $to || $from == $to) {
            $error = "Please enter lower value in the FROM field";
        } else // everything OK
        {
            // create socket
            $socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);

            for ($port = $from; $port <= $to; $port++) {
                // connect to host and port
                $connection = socket_connect($socket, $host, $port);

                // make list of open ports
                if ($connection) {
                    array_push($openPorts, $port);
                    // close socket connection
                    socket_close($socket);
                    // create new when earlier socket was closed, recreate when connection made, otherwise same socket used
                    $socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
                }
            }
        }

        return array(
            'error' => $error,
            'openPorts' => $openPorts
        );
    }
}
