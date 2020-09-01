<?php

use Illuminate\Support\Facades\Http;

// Get data user dgn id tertentu dari service-user
function getUser($userId) {
    $url = env('SERVICE_USER_URL').'users/'.$userId;

    try {
        $response = Http::timeout(10)->get($url);
        $data = $response->json();
        // Inject http code
        $data['http_code'] = $response->getStatusCode();
        return $data;
    } catch (\Throwable $th) {
        return [
            'status' => 'error',
            'http_code' => 500,
            'message' => 'service user unavailable'
        ];
    }
}

// Get data user dengan id tertentu (id : 2,4,6)
function getuserByIds($userIds = []) {
    $url = env('SERVICE_USER_URL').'users/';

    try {
        if (count($userIds) === 0) {
            return [
                'status' => 'success',
                'http_code' => '200',
                'data' => []
            ];
        }

        // Tambahkan query params
        $response = Http::timeout(10)->get($url, ['user_ids[]' => $userIds]);
        $data = $response->json();
        $data['http_code'] = $response->getStatusCode();
        return $data;
    } catch (\Throwable $th) {
        return [
            'status' => 'error',
            'http_code' => 500,
            'message' => 'service user unavailable'
        ];
    }
}


/*  Daftarkan di composer.json -> autoload
    Panggil di controller manapun.
*/
?>