<?php

use Swoft\Context\Context;
use Swoft\Http\Message\ContentType;
use Swoft\Http\Message\Response;

/**
 * Custom global functions
 */

function user_func(): string
{
    return 'hello';
}

/**
 * 拼装json格式的接口返回数据
 *
 * @param int $code
 * @param string $message
 * @param array $data
 * @return string
 */
function make_json_response_content(int $code, string $message='', array $data) : string
{
    return json_encode([
        'code' => $code,
        'message' => $message,
        'data' => $data
    ]);
}

/**
 * http返回json
 *
 * @param string $content
 * @return Response
 * @throws ReflectionException
 * @throws \Swoft\Bean\Exception\ContainerException
 */
function json_response(string $content) : Response
{
    return Context::mustGet()->getResponse()->withContentType(ContentType::JSON)->withContent($content);
}
