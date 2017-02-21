<?php
/**
 * HTTP封装
 * User: Marico
 * Date: 16/2/26
 * Time: 15:51
 */
class Http
{
    /**
     * URL访问请求，进行GET请求
     * @param $url 目标地址
     * @param $data 请求参数
     * @return $resutl 请求返回结果
     */
    public static function get($url)
    {
        $ch = curl_init ();
        curl_setopt ( $ch, CURLOPT_URL, $url );
        curl_setopt ( $ch, CURLOPT_HEADER, 0 );
        curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1 );
        $result = curl_exec ( $ch );
        curl_close ( $ch );
        return $result;
    }

    /**
     * URL访问请求，进行POST请求
     * @param $url 目标地址
     * @param $data 请求参数
     * @return $resutl 请求返回结果
     */
    public static function post($url , $data)
    {
        $ch = curl_init ();
        curl_setopt ( $ch, CURLOPT_URL, $url );
        curl_setopt ( $ch, CURLOPT_POST, 1 );
        curl_setopt ( $ch, CURLOPT_HEADER, 0 );
        curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1 );
        curl_setopt ( $ch, CURLOPT_POSTFIELDS, json_encode($data) );
        $result = curl_exec ( $ch );
        curl_close ( $ch );
        return $result;
    }
}