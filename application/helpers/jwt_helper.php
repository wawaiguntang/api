<?php

use Firebase\JWT\JWT as JWT_VENDOR;

function JWT_Verif_Refresh()
{
    if (!isset($_COOKIE['refreshToken'])) {
        return [
            'status' => false,
            'data' => [],
            'message' => 'Refresh token not found'
        ];
    }
    try {
        $map = JWT_VENDOR::decode($_COOKIE['refreshToken'], REFRESH_TOKEN_SECRET, ['HS256']);
        return [
            'status' => true,
            'data' => json_decode(json_encode($map), true)
        ];
    } catch (Exception $e) {
        return [
            'status' => false,
            'data' => [],
            'message' => 'Your token has expired or token is wrong'
        ];
    }
}

function JWT_Verif_Access()
{
    $header = getallheaders();
    if (!isset($header['Authorization'])) {
        return [
            'status' => false,
            'data' => [],
            'message' => 'Authorization is required'
        ];
    }
    try {
        list(, $token) = explode(' ', $header['Authorization']);
        $map = JWT_VENDOR::decode($token, ACCESS_TOKEN_SECRET, ['HS256']);
        $map->accessToken = $token;
        return [
            'status' => true,
            'data' => json_decode(json_encode($map), true)
        ];
    } catch (Exception $e) {
        return [
            'status' => false,
            'data' => [],
            'message' => 'Your token has expired or token is wrong'
        ];
    }
}

function generateAccessToken(string $email): array
{
    $waktu_kadaluarsa =  time() + (60 * 60 * 24);
    $issuer_claim = "API"; // this can be the servername
    $audience_claim = "API";
    $issuedat_claim = time(); // issued at
    $notbefore_claim = $issuedat_claim; //not before in seconds
    $payload = [
        'email' => $email,
        'exp' => $waktu_kadaluarsa,
        "iss" => $issuer_claim,
        "aud" => $audience_claim,
        "iat" => $issuedat_claim,
        "nbf" => $notbefore_claim,
    ];

    $access_token = JWT_VENDOR::encode($payload, ACCESS_TOKEN_SECRET, 'HS256');

    $payload['exp'] = time() + (60 * 60 * 24 * 7);
    $refresh_token = JWT_VENDOR::encode($payload, REFRESH_TOKEN_SECRET, 'HS256');

    setcookie('refreshToken', $refresh_token, $payload['exp'], '', '', false, true);
    return [
        'accessToken' => $access_token,
        'refreshToken' => $refresh_token
    ];
}
