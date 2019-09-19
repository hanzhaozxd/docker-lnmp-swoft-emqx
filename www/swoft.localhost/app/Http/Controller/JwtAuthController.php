<?php declare(strict_types=1);

namespace App\Http\Controller;

use App\Model\Entity\Users;
use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Parser;
use Lcobucci\JWT\ValidationData;
use Swoft\Context\Context;
use Swoft\Http\Message\ContentType;
use Swoft\Http\Message\Request;
use Swoft\Http\Message\Response;
use Swoft\Http\Server\Annotation\Mapping\Controller;
use Swoft\Http\Server\Annotation\Mapping\RequestMapping;
use Swoft\Stdlib\Helper\ArrayHelper;
use Swoft\Validator\Exception\ValidatorException;

/**
 * Class JwtAuthController
 * @Controller(prefix="jwt")
 */
class JwtAuthController
{
    /**
     * @RequestMapping(route="login")
     *
     * @param Request $request
     * @return Response
     * @throws \ReflectionException
     * @throws \Swoft\Bean\Exception\ContainerException
     */
    public function login(Request $request): Response
    {
        // 获取所有参数
        $params = $request -> get();

        // 数据校验
        try{
            $params['name'] = ArrayHelper::getValue($params, 'username');
            \validate($params, 'UsersLoginValidator');
        }catch (ValidatorException $e){
            // 参数错误
            $content = make_json_response_content(-1, $e->getMessage(), []);
            return json_response($content);
        }

        try{
            $user = Users::where('name', $params['name'])
                -> first();
            if(
                is_null($user) ||
                !hash_equals($user->getPassword(), md5($params['password']))
            ){
                throw new \InvalidArgumentException("抱歉，您输入的账号或密码错误");
            }
        }catch (\Exception $e){
            $content = make_json_response_content(-1, $e->getMessage(), []);
            return json_response($content);
        }

        // 生成token
        $time = time();
        $token = (new Builder()) -> issuedBy('swoft.localhost')
            -> permittedFor('swoft.localhost')
            -> identifiedBy('ThisIsKeyValue')
            -> issuedAt($time)
            -> canOnlyBeUsedAfter($time)
            -> expiresAt($time + 3600)
            -> withClaim('uid', $user->getId())
            -> getToken();

        // 将token返回
        $content = make_json_response_content(1, '登录成功', [
            'token' => $token -> __toString(),
        ]);

        return json_response($content);
    }

    /**
     * @RequestMapping(route="me")
     */
    public function me(): Response
    {
        // 获取请求参数
        $request = Context::mustGet()->getRequest();

        $token = $request->getHeaderLine('token');

        if(empty($token)){
            $content = make_json_response_content(-1, 'Token不能为空', []);
        }else{
            try{
                // 解析Token
                $token = (new Parser()) -> parse($token);

                // 创建验证器
                $data = new ValidationData();
                $data->setIssuer('swoft.localhost');
                $data->setAudience('swoft.localhost');
                $data->setId('ThisIsKeyValue');

                // 校验Token
                if($token -> validate($data)){
                    $content = make_json_response_content(1, '权限有效果，返回个人信息', [
                        'uid' => $token -> getClaim('uid')
                    ]);
                }else{
                    $content = make_json_response_content(-2, 'Token错误，请重新登录', []);
                }
            }catch (\InvalidArgumentException $e){
                $content = make_json_response_content(-3, 'Token错误，请重新登录', [
                    'exception_message' => $e -> getMessage()
                ]);
            }
        }

        return Context::mustGet()->getResponse()->withContentType(ContentType::JSON)->withContent($content);
    }
}
