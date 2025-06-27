
<?php
class Sample {
    public function run() {
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://aip.baidubce.com/oauth/2.0/token?client_id=TXrGzPQ4BVgRKvOaE3ctCKAP&client_secret=1Cri8FBvMS8exMOaqKZCSrK0XCYRu6TY&grant_type=client_credentials",
            CURLOPT_TIMEOUT => 30,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json',
                'Accept: application/json'
            ),

        ));
        $response = curl_exec($curl);
        curl_close($curl);
        return $response;
    }
}

$rtn = (new Sample())->run();
print_r($rtn);