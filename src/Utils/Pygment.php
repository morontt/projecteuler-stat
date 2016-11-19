<?php
/**
 * Created by PhpStorm.
 * User: morontt
 * Date: 14.06.16
 * Time: 0:50
 */

namespace MttProjecteuler\Utils;

class Pygment
{
    /**
     * @param string $url
     * @param string $lexer
     *
     * @return string|null
     */
    public static function highlight($url, $lexer)
    {
        $result = null;
        $matches = [];
        if (preg_match('/^https:\/\/github\.com\/([^\/]+)\/([^\/]+)\/(.*)/', $url, $matches)) {
            $rawUrl = sprintf(
                'https://raw.githubusercontent.com/%s/%s/%s',
                $matches[1],
                $matches[2],
                str_replace('blob/', '', $matches[3])
            );

            $tmp = sys_get_temp_dir();
            $file = $tmp . '/' . pathinfo($matches[3], PATHINFO_BASENAME);

            $fp = fopen($file, 'w');

            $curl = curl_init($rawUrl);
            curl_setopt($curl, CURLOPT_USERAGENT, 'Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:47.0) Gecko/20100101 Firefox/47.0');
            curl_setopt($curl, CURLOPT_TIMEOUT, 15);
            curl_setopt($curl, CURLOPT_FILE, $fp);
            curl_setopt($curl, CURLOPT_HEADER, 0);

            curl_exec($curl);

            if (!curl_errno($curl)) {
                curl_close($curl);
                fclose($fp);

                $output = [];
                exec('pygmentize -f html -l ' . $lexer . ' -O linenos=inline ' . escapeshellarg($file), $output);

                $result = implode("\n", $output);
                unlink($file);
            }
        }

        return $result;
    }
}
