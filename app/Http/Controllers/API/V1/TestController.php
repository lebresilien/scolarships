<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\BaseController;
use Illuminate\Http\Request;

/**
 * User resource representation.
 *
 * @Resource("Users", uri="/test")
 */
class TestController extends BaseController
{
    /**
     * Show all users
     *
     * Get a JSON representationes of all the registered users.
     * @Get("/")
     * @Versions({"v1"})
    */
    public function test(){
        
        $users = [
            [
                "name" => "tapondjou",
                "phone" => 694282821
            ],
            [
                "name" => "lebresilien",
                "phone" =>  694282821
            ],
            [
                "name" =>  "pepita",
                "phone" => 694282821
            ]
        ];

        return $this->response->array($users);
    }
}
