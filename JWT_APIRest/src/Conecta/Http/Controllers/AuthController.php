<?php

namespace Link\Http\Controllers;

class AuthController
{
    private static $key = '123456'; //Application Key

    public function login()
    {

        if ($_POST['email'] = 'test@gmail.com' && $_POST['password'] = '123') {
            //Header Token
            $header = [
                'typ' => 'JWT',
                'alg' => 'HS256'
            ];

            //Payload - Content
            $payload = [
                'name' => 'Aniel Lenon',
                'email' => $_POST['email'],
            ];

            //JSON
            $header = json_encode($header);
            $payload = json_encode($payload);

            //Base 64
            $header = self::base64UrlEncode($header);
            $payload = self::base64UrlEncode($payload);

            //Sign
            $sign = hash_hmac('sha256', $header . "." . $payload, self::$key, true);
            $sign = self::base64UrlEncode($sign);

            //Token
            $token = $header . '.' . $payload . '.' . $sign;

            return $token;
        }

        throw new \Exception('Não autenticado');
    }

    public static function checkAuth()
    {
        $http_header = apache_request_headers();

        if (isset($http_header['Authorization']) && $http_header['Authorization'] != null) {
            $bearer = explode(' ', $http_header['Authorization']);
            //$bearer[0] = 'bearer';
            //$bearer[1] = 'token jwt';

            $token = explode('.', $bearer[1]);
            $header = $token[0];
            $payload = $token[1];
            $sign = $token[2];

            //Conferir Assinatura
            $valid = hash_hmac('sha256', $header . "." . $payload, self::$key, true);
            $valid = self::base64UrlEncode($valid);

            if ($sign === $valid) {
                return true;
            }
        }

        return false;
    }


    /*Criei os dois métodos abaixo, pois o jwt.io agora recomenda o uso do 'base64url_encode' no lugar do 'base64_encode'*/
    private static function base64UrlEncode($data)
    {
        // Antes de tudo, você deve codificar $data para a string Base64
        $b64 = base64_encode($data);

        // Certifique-se de obter um resultado válido, caso contrário, retorne FALSE, como a função base64_encode() faz
        if ($b64 === false) {
            return false;
        }

        // Converta Base64 para Base64URL substituindo “+” por “-” e “/” por “_”
        $url = strtr($b64, '+/', '-_');

        // Remova o caractere de preenchimento do final da linha e retorne o resultado Base64URL
        return rtrim($url, '=');
    }
}