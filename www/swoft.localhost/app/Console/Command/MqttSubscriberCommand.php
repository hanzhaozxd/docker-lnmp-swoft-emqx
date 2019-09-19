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

    /**
     * 此命令模拟一个智能门禁大致的几个监控节点
     *
     * 地点为 BeijingHaiDian
     *
     * 门禁机 DoorComputer001
     *      1. 开关门推送   开始，自动1次 接到关门推送后 5秒后再次开门
     *
     * 门    Door001
     *      1. 开关门推送   主题订阅 BeijingHaiDian/Door001/IsOpen    1-是 0-否  3秒后自动关门
     *
     *
     *
     * 参考资料（需谨记）
     * MQTT-Topic 相关知识：
     * 主题层级分隔符— /
     * 多层通配符— #
     *      如果客户端订阅主题 “first/second/#” 所有以first/second开头的消息都会触发
     * 单层通配符- +
     *      类似# 但只跳过一个层级
     * 通配符- $
     *      不能用在主题开头（包括次级开头） 用来匹配一个字符
     *
     * @CommandMapping(alias="subTest")
     */
    public function subscriberTest()
    {
        $topic = 'BeijingHaiDian/Door001/IsOpen';

        // Boss 主要目的是测试通配符，现实中可以是门禁平台的行为数据库
        $boss = new Boss();
        $boss_connection = $boss -> connectEmqx();
        $boss -> subscribeTopic($boss_connection, 'BeijingHaiDian/#');

        // 门禁机1
        $door_computer_first = new DoorComputer(1);
        $door_computer_first_connection = $door_computer_first -> connectEmqx();
//        $door_computer_first -> subscribeTopic($door_computer_first_connection, $topic);

        // 门1
        $door_first = new Door(1);
        $door_first_connection = $door_first -> connectEmqx();
        $door_first -> subscribeTopic($door_first_connection, $topic);

        while (true){
            $boss_connection -> proc();
            $door_computer_first_connection -> proc();
            $door_first_connection -> proc();
        }

        $boss_connection -> close();
        $door_computer_first_connection -> close();
        $door_first_connection -> close();
    }
}

class MqttUser
{
    public $name;
    public $client_id;

    // TODO 用单例防止重复连接
    public function connectEmqx()
    {
        $connection = new phpMQTT('emqx', 1883, $this->client_id);
        if(!$connection -> connect(true, NULL, '', '')){
            throw new \Exception($this->name . '连接EMQX服务器失败！');
        }

        return $connection;
    }

    public function subscribeTopic(phpMQTT $connection, $topic)
    {
        Show::info($this->name . '订阅了' . $topic . '：');

        // 订阅的主题
        $topics[$topic] = [
            "qos" => 0,
            "function" => [$this, "messageHandler"]
        ];
        $connection -> subscribe($topics, 0);

        // 通过while实现长连接
//        while($connection->proc()){
//
//        }
//
//        $connection->close();
    }

    public function publishContentWithTopic(phpMQTT $connection, $topic, $content)
    {
        $connection->publish($topic, $content, 0);
        $connection->close();
    }

    public function messageHandler($topic, $msg){
        Show::title($this->name . "收到消息于" . date("Y-m-d H:i:s"));
        Show::liteInfo("Topic: {$topic}");
        Show::liteInfo($msg);
        Show::info('-----------------');
    }
}

class DoorComputer extends MqttUser
{
    public function __construct(int $index)
    {
        // 填充为3位数字，这里也限制了门禁机数量上限
        $last_index = str_pad((string) $index, 3, '0', STR_PAD_LEFT);

        $this -> name = '门禁机' . $last_index;
        $this -> client_id = 'DoorComputer' . $last_index;
    }
}

class Door extends MqttUser
{
    public function __construct(int $index)
    {
        // 填充为3位数字，这里也限制了门禁机数量上限
        $last_index = str_pad((string) $index, 3, '0', STR_PAD_LEFT);

        $this -> name = '大门' . $last_index;
        $this -> client_id = 'Door' . $last_index;
    }
}

class Boss extends MqttUser
{
    public function __construct()
    {
        $this -> name = 'Boss';
        $this -> client_id = 'Boss';
    }

    public function messageHandler($topic, $msg){
        Show::title('--------BOSS---------');
        Show::info('Topic——' . $topic);
        Show::info('Msg——' . $msg);
        Show::info('--------BOSS---------');
    }
}
