<?php

namespace App\Console\Commands;

use Edujugon\PushNotification\PushNotification;
use Exception;
use Illuminate\Console\Command;

class TestPush extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:push';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send test push notification';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
//        $notification = new PushNotification('apn');
//        $notification->setService('apn');
//        $notification->setDevicesToken('b4ffc30115645974dcd6cbcb59c71bbed6af7afeb34a5eec12b8e68c397ccbe8');
//        $notification->setMessage([
//            'aps' => [
//                'alert' => [
//                    'title' => 'This is the title',
//                    'body' => 'This is the body'
//                ],
//                'sound' => 'default',
//                'badge' => 1
//
//            ],
//            'extraPayLoad' => [
//                'custom' => 'My custom data',
//            ]
//        ]);

//        dump($notification->send()->getFeedback());

//        dump($this::sendNotificationAPN('b4ffc30115645974dcd6cbcb59c71bbed6af7afeb34a5eec12b8e68c397ccbe8', 'New Title', 'Body;)', null, 'default'));

        $this->n2();
    }

    private static function sendNotificationAPN($device_token, $title, $body, $category, $sound) {
        $alert = array(
            'aps' => array(
                'alert'    => array(
                    'title' => $title,
                    'body'  => $body
                ),
                'badge' => 0,
                'sound'    => $sound,
//                'category' => $category,
                'content-available' => true
            )
        );

//        foreach ($optionals as $key => $option) {
//            $alert[$key] = $option;
//        }

        $alert = json_encode($alert);

        $url = 'https://api.development.push.apple.com/3/device/' . $device_token;

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $alert);
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_2_0);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array("apns-topic: 'com.liinc.leadinclusively'"));
        curl_setopt($ch, CURLOPT_SSLCERT, '/home/ddi-pc-23/apn_cert/AuthKey_82BZM73K98.p8');
//        curl_setopt($ch, CURLOPT_SSLCERTPASSWD, /* passphrase for cert */);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_VERBOSE, 1);
        curl_setopt($ch, CURLOPT_HEADER, 1);
        $response = curl_exec($ch);
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);


        $ret = array(
            'body' => $response,
            'httpcode' => $httpcode
        );

        return $ret;
    }

    public function n2() {
        $keyfile = '/home/ddi-pc-23/apn_cert/AuthKey_82BZM73K98.p8';               # <- Your AuthKey file
        $keyid = '82BZM73K98';                            # <- Your Key ID
        $teamid = 'KMZ7Z5CZPV';                           # <- Your Team ID (see Developer Portal)
        $bundleid = 'com.liinc.leadinclusively';                # <- Your Bundle ID
        $url = 'https://api.development.push.apple.com';  # <- development url, or use http://api.push.apple.com for production environment
        $token = 'b4ffc30115645974dcd6cbcb59c71bbed6af7afeb34a5eec12b8e68c397ccbe8';              # <- Device Token

        $message = '{"aps":{"alert":"Hi there!","sound":"default"}}';

        $key = openssl_pkey_get_private('file://'.$keyfile);

        $header = ['alg'=>'ES256','kid'=>$keyid];
        $claims = ['iss'=>$teamid,'iat'=>time()];

        $header_encoded = $this->base64($header);
        $claims_encoded = $this->base64($claims);

        $signature = '';
        openssl_sign($header_encoded . '.' . $claims_encoded, $signature, $key, 'sha256');
        $jwt = $header_encoded . '.' . $claims_encoded . '.' . base64_encode($signature);

        // only needed for PHP prior to 5.5.24
        if (!defined('CURL_HTTP_VERSION_2_0')) {
            define('CURL_HTTP_VERSION_2_0', 3);
        }

        $http2ch = curl_init('https://api.development.push.apple.com/3/device/89ea1e8de409a15c30de009001f92e5a15d7c0aefd7fb377a525c0c02487c38c');
        curl_setopt_array($http2ch, array(
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_2_0,
            CURLOPT_URL => 'https://api.development.push.apple.com/3/device/89ea1e8de409a15c30de009001f92e5a15d7c0aefd7fb377a525c0c02487c38c',
            CURLOPT_PORT => 443,
            CURLOPT_HTTPHEADER => array(
                "apns-topic: {$bundleid}",
                "authorization: bearer $jwt"
            ),
            CURLOPT_POST => TRUE,
            CURLOPT_POSTFIELDS => $message,
            CURLOPT_RETURNTRANSFER => TRUE,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HEADER => 1
        ));

        $result = curl_exec($http2ch);
        if ($result === FALSE) {
            throw new Exception("Curl failed: ".curl_error($http2ch));
        }

        $status = curl_getinfo($http2ch, CURLINFO_HTTP_CODE);
        echo $status;


    }

    function base64($data)
    {
        return rtrim(strtr(base64_encode(json_encode($data)), '+/', '-_'), '=');
    }
}
