<?php declare(strict_types=1);

namespace App\Http\Controller;

use Bluerhinos\phpMQTT;
use Swoft\Context\Context;
use Swoft\Http\Message\ContentType;
use Swoft\Http\Message\Response;
use Swoft\Http\Server\Annotation\Mapping\Controller;
use Swoft\Http\Server\Annotation\Mapping\RequestMapping;

/**
 * Class MqttController
 * @Controller(prefix="mqtt")
 */
class MqttController
{
    /**
     * @RequestMapping(route="publish")
     */
    public function publish(): Response
    {
        // 获取请求参数
        $request = Context::mustGet()->getRequest();

        $topic = $request -> get('topic');          // 要推送的主题
        $content = $request -> get('content');      // 要推送的内容

        // 连接Emqx服务器
        $server = "emqx";
        $port = 1883;
        $username = "";
        $password = "";
        $client_id = "phpMQTT-publisher";

        $mqtt = new phpMQTT($server, $port, $client_id);

        if ($mqtt->connect(true, NULL, $username, $password)) {
            $mqtt->publish($topic, $content, 0);
            $mqtt->close();
            $content = make_json_response_content(1, '推送成功', [
                'topic' => $topic,
                'content' => $content
            ]);
        } else {
            $content = make_json_response_content(-1, '推送失败', []);
        }

        return Context::mustGet()->getResponse()->withContentType(ContentType::JSON)->withContent($content);
    }
}
