<?php
ini_set('date.timezone', 'Asia/Shanghai');

/**
 * HTTP请求
 * @param $url
 * @return string
 */
function request_api($url)
{
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "GET");
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 10); //建立连接超时
    curl_setopt($curl, CURLOPT_TIMEOUT, 30); //最大持续连接时间
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
    $result = curl_exec($curl);
    curl_close($curl);
    return $result;
}

/**
 * 获取每日一句
 * @param $channel
 * @return string
 */
function get_one_words($channel = null)
{
    $channel_list = [1, 2, 3, 4];
    $channel = ($channel && $channel_list[$channel]) ? $channel : mt_rand(1, count($channel_list));
    $one_words = '';
    switch ($channel) {
        case 1: // 彩虹屁
            $one_words = file_get_contents('https://chp.shadiao.app/api.php');
            break;
        case 2: // 土味情话
            $one_words = file_get_contents('https://api.lovelive.tools/api/SweetNothings');
            break;
        case 3: // 格言信息
            $one_words = json_decode(file_get_contents('http://open.iciba.com/dsapi'), true)['note'];
            break;
        case 4: // 一言
            $one_words = json_decode(file_get_contents('https://v1.hitokoto.cn/'), true)['hitokoto'];
            break;
    }
    return $one_words;
}

/**
 * 获取天气
 * @return string
 */
function get_weather()
{
    return request_api('https://wttr.in/Shanghai?format=3');
}

// 相差天数
$diff = (strtotime(date('Y-m-d')) - strtotime('2015-07-07')) / 86400;
// 标题
$text = "[每日一句]爱你的第{$diff}天";
// 天气
$weather = get_weather();
// 一句话
$one_words = get_one_words();
// 输出文件
file_put_contents('result.html', join(PHP_EOL, [$text, $weather, $one_words]));
