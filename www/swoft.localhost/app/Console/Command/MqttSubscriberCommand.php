<?php declare(strict_types=1);

namespace App\Console\Command;

use Bluerhinos\phpMQTT;
use Swoft\Console\Annotation\Mapping\Command;
use Swoft\Console\Annotation\Mapping\CommandMapping;
use Swoft\Console\Annotation\Mapping\CommandOption;
use Swoft\Console\Helper\Show;
use Swoft\Console\Input\Input;

/**
 * Class DemoCommand
 *
 * @Command(name="mqtt")
 */
class MqttSubscriberCommand
{
    /**
     * @CommandMapping(alias="sub")
     * @param Input $input
     * @CommandOption(
     *     "server", short="s", type="string", default="127.0.0.1",
     *     desc="服务器地址"
     * )
     * @CommandOption(
     *     "port", short="p", type="integer", default=1883,
     *     desc="服务器端口"
     * )
     * @CommandOption(
     *     "username", short="u", type="string", default="",
     *     desc="用户名"
     * )
     * @CommandOption(
     *     "password", short="password", type="string", default="",
     *     desc="密码"
     * )
     * @CommandOption(
     *     "client_id", short="id", type="string", default="phpMQTT-subscriber-hanzhao",
     *     desc="ClientID"
     * )
     * @CommandOption(
     *     "qos", short="q", type="integer", default=0,
     *     desc="监听级别"
     * )
     * @CommandOption(
     *     "topic", short="t", type="string",
     *     desc="要监听的主题"
     * )
     */
    public function subscriber(Input $input): void
    {
        // 获取要监听的主题
        $topic = $input -> getOpt('topic');

        // 获取服务器相关的信息
        $server = $input -> getOpt('server', '127.0.0.1');
        $port = $input -> getOpt('port', 1883);
        $username = $input -> getOpt('username', "");
        $password = $input -> getOpt('password', "");
        $client_id = $input -> getOpt('client_id', "phpMQTT-subscriber-hanzhao");

        // 监听级别
        $qos = $input -> getOpt('qos', 0);

        $mqtt = new phpMQTT($server, $port, $client_id);

        // 执行连接
        if(!$mqtt->connect(true, NULL, $username, $password)) {
            Show::error("致命错误：连接失败！！！");
        }else{
            Show::success('连接成功~');
        }

        // 订阅的主题
        $topics[$topic] = [
            "qos" => $qos,                              // 订阅级别为0 至多一次
            "function" => [$this, "messageHandler"]     // 回调函数
        ];
        $mqtt->subscribe($topics, 0);

        // 通过while实现长连接
        while($mqtt->proc()){

        }

        $mqtt->close();
    }

    // 方法处理
    public function messageHandler($topic, $msg){
        Show::liteInfo("Msg Recieved: " . date("Y-m-d H:i:s"));
        Show::liteInfo("Topic: {$topic}");
        Show::liteInfo($msg);
        Show::info('-----------------');
    }
}
