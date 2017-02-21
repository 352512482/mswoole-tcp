<?php
/**
 * Created by PhpStorm.
 * User: Marico
 * Date: 16/6/20
 * Time: 18:39
 */
class Log
{
    /**
     * 记录日志
     * @param string $string
     * @return null
     */
    public static function record($string = '')
    {
        if (APP_DEBUG)
        {
            $folder = APP_PATH.'/runtime/';

            $file = $folder . date('Y-m-d-').APP_SERVER.'.log';

            is_string($string) || $string = var_export($string, true);
            $string .= "\n";

            file_put_contents($file, date('Y-m-d H:i:s').' -> '.$string, FILE_APPEND);
        }
        return null;
    }
}