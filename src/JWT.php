<?php
/**
 * JWT生成，验证
 * User: qufo
 * Date: 16/11/19
 * Time: 上午12:04
 */

namespace Qufo\JWT;


class JWT
{

    /**
     * 编码 JWT
     * @param $payload
     * @param null $head
     * @return string
     * @throws Exception
     */
    public static function encode($payload,$head=null) {
        $header = array('typ'=>'JWT','alg'=>'HS256');
        if (isset($head) && is_array($head)) {
            $header = array_merge($head,$header);
        }

        $tokens[] = self::Base64UrlEncode(self::jsonEncode($header));
        $tokens[] = self::Base64UrlEncode(self::jsonEncode($payload));
        $msg = implode('.',$tokens);
        $sign = self::sign($msg);
        $tokens[] = self::Base64UrlEncode($sign);
        return implode('.',$tokens);
    }


    /**
     * 解码 JWT
     *
     * @param $jwt
     * @return mixed
     * @throws \Exception
     */
    public static function decode($jwt){
        $tks = explode('.',$jwt);
        if (count($tks)!=3) {
            throw new \Exception('Wrong number of segments.');
        }

        list($header_s,$payload_s,$sign) = $tks;
        $header  = self::jsonDecode(self::Base64UrlDecode($header_s));
        if (!$header) {
            throw new \Exception('Invalid header.');
        }
        $payload = self::jsonDecode(self::Base64UrlDecode($payload_s));
        if (!$payload){
            throw new \Exception('Invalid payload.');
        }
        if (!self::verify("$header_s.$payload_s",$sign)){
            throw new \Exception('Signature fail.');
        }
        if (isset($payload['nbf']) && $payload['iat'] > time()) {
            throw new \Exception('Token create time error.');
        }
        if (isset($payload['exp']) && ($payload['exp'] < time())) {
            throw new \Exception('Token had expired.');
        }

        return $payload;
    }

    /**
     * 进行签名
     * @param $msg
     * @return string
     */
    public static function sign($msg){
        return hash_hmac(config('JWT_ALGO','SHA256'),$msg,config('JWT_SECRET'),true);
    }

    /**
     * 验签
     * @param $msg
     * @param $sign
     * @return bool
     */
    public static function verify($msg,$sign){
        $hash = hash_hmac(config('JWT_ALGO','SHA256'),$msg,config('JWT_SECRET'),true);
        return hash_equals(self::Base64UrlEncode($hash),$sign);
    }

    /**
     * JSON 编码
     * @param $input
     * @return string
     * @throws \Exception
     */
    public static function jsonEncode($input){
        $json = json_encode($input);
        if (JSON_ERROR_NONE !== json_last_error()){
            throw new \Exception(json_last_error_msg());
        }
        return $json;
    }


    /**
     * JSON解码
     * @param $input
     * @return mixed
     * @throws \Exception
     */
    public static function jsonDecode($input){
        $obj = json_decode($input, true, 512, JSON_BIGINT_AS_STRING);
        if (JSON_ERROR_NONE !== json_last_error()){
            throw new \Exception(json_last_error_msg());
        }
        return $obj;
    }

    /**
     * URL安全的 Base64 编码
     * @param $input
     * @return mixed
     */
    public static function Base64UrlEncode($input)
    {
        return str_replace('=', '', strtr(base64_encode($input), '+/', '-_'));
    }


    /**
     * URL安全的 Base64 解码
     * @param $input
     * @return string
     */
    public static function Base64UrlDecode($input){
        $remain = strlen($input) % 4 ;
        if ($remain) {
            $pad_length = 4 - $remain;
            $input = str_pad($input,$pad_length,'=');
        }
        return base64_decode($input);
    }

}