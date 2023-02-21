<?php
namespace Nest\Auth;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Nest\Users\UserActions;

class ApiAuth {
    private $token;
    public function __construct($token)
    {
        $this->token = $token;
        $this->userAuth();
    }

    function userAuth()
    {
        $decoded = JWT::decode($this->token, new Key(DEF_NEST_SECRET_KEY, 'HS256'));
        $decoded = (array) $decoded;
        print_r($decoded);exit;
    }
}