<?php declare(strict_types=1);

namespace App\Validator;

use Swoft\Validator\Annotation\Mapping\AlphaNum;
use Swoft\Validator\Annotation\Mapping\Confirm;
use Swoft\Validator\Annotation\Mapping\IsString;
use Swoft\Validator\Annotation\Mapping\Length;
use Swoft\Validator\Annotation\Mapping\Validator;

/**
 * Class UsersRegisterValidator
 * @Validator(name="UsersRegisterValidator")
 */
class UsersRegisterValidator
{
    /**
     * @IsString()
     * @Length(min=6, max=20, message="用户名长度6~20位")
     * @AlphaNum(message="用户名只可以包含大写字母、小写字母、数字。")
     *
     * @var string
     */
    protected $name;

    /**
     * @IsString()
     * @Length(min=8, max=20, message="密码长度8~20位")
     *
     * @var string
     */
    protected $password;

    /**
     * @IsString()
     * @Confirm(name="password")
     * @var
     */
    protected $confirm_password;
}
