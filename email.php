<?php
ini_set('date.timezone', 'Asia/Shanghai');

/**
 * 获取每日一句
 * @param int $channel 渠道 1.彩虹屁,2.土味情话,3.格言信息,4.一言
 * @return string
 */
function get_one_words($channel = null)
{
    $channel_list = [1, 2, 3, 4];
    $channel = in_array($channel, $channel_list) ? $channel : random_int(1, count($channel_list));
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
 * 
 * 教程：https://www.sojson.com/blog/305.html
 * 
 * 旧api：http://wttr.in/Shanghai?format=3
 * 
 * @return string
 */
function get_weather()
{
    // 101020100:上海
    $data = json_decode(file_get_contents('http://t.weather.sojson.com/api/weather/city/101020100'), true);
    if ($data['status'] != 200) {
        return $data['message'] ?? 'API挂了';
    }

    // 这个天气的接口更新不及时，有时候当天1点的时候，还是昨天的天气信息，如果天气不一致，则取下一天(今天)的数据
    $weather_data = $data['data']['forecast'][0];
    $is_tomorrow = (int)date('H') >= 20;
    if ($is_tomorrow || $weather_data['ymd'] != date('Y-m-d')) {
        $weather_data = $data['data']['forecast'][1];
    }

    // 格式化数据
    /**
     *{
     *  "date": "05",
     *  "high": "高温 11℃",
     *  "low": "低温 6℃",
     *  "ymd": "2022-03-05",
     *  "week": "星期六",
     *  "sunrise": "06:17",
     *  "sunset": "17:55",
     *  "aqi": 59,
     *  "fx": "东北风",
     *  "fl": "3级",
     *  "type": "晴",
     *  "notice": "愿你拥有比阳光明媚的心情"
     *}
     *
     *2022-03-05,星期六 上海市
     *【今日天气】晴
     *【今日气温】低温 6℃ 高温 11℃
     *【今日风速】东北风3级
     *【出行提醒】愿你拥有比阳光明媚的心情
     */
    $format = "%s,%s %s【今日天气】%s【今日气温】%s %s【今日风速】%s【出行提醒】%s";
    return sprintf(
        $format,
        $weather_data['ymd'],
        $weather_data['week'],
        $data['cityInfo']['city'] . PHP_EOL,
        $weather_data['type'] . PHP_EOL,
        $weather_data['low'],
        $weather_data['high'] . PHP_EOL,
        $weather_data['fx'] . $weather_data['fl'] . PHP_EOL,
        $weather_data['notice']
    );
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
file_put_contents('result.html', join(PHP_EOL, [$text, $one_words . PHP_EOL, $weather]));
