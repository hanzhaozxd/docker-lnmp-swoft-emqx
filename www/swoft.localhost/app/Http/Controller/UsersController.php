<?php declare(strict_types=1);

namespace App\Http\Controller;

use App\Model\Entity\Users;
use Swoft\Http\Message\Request;
use Swoft\Http\Server\Annotation\Mapping\Controller;
use Swoft\Http\Server\Annotation\Mapping\RequestMapping;
use Swoft\Http\Server\Annotation\Mapping\RequestMethod;
use Swoft\Validator\Exception\ValidatorException;

/**
 * Class UsersController
 * @Controller(prefix="users")
 */
class UsersController
{
    /**
     * @RequestMapping(method={RequestMethod::POST})
     *
     * @param Request $request
     * @return \Swoft\Http\Message\Response
     * @throws \ReflectionException
     * @throws \Swoft\Bean\Exception\ContainerException
     */
    public function register(Request $request)
    {
        // 获取所有参数
        $params = $request -> post();

        // 数据校验
        try{
            $request = \validate($params, 'UsersRegisterValidator');
        }catch (ValidatorException $e){
            // 参数错误
            $content = make_json_response_content(-1, $e->getMessage(), []);
            return json_response($content);
        }

        // 用户名唯一性校验
        $user = Users::firstOrNew(['name' => $params['name']]);

        if($user -> getId()){
            // 已有用户名
            $content = make_json_response_content(-2, '抱歉，用户名已被注册', []);
            return json_response($content);
        }

        $user -> setName($params['name']);
        $user -> setPassword(md5($params['password']));

        if($user -> save()){
            $content = make_json_response_content(1, '注册成功，请登录', []);
        }else{
            $content = make_json_response_content(-3, '抱歉，注册失败', []);
        }

        return json_response($content);
    }
}
