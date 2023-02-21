<?php
namespace Nest\Auth;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use UnexpectedValueException;
use LogicException;
use Nest\Users\UserActions;

class Auth
{
    private $token;
    public function __construct($token)
    {
        $this->token = $token;
    }

    public function userAuth()
    {
        try {
            $decoded = JWT::decode($this->token, new Key(DEF_NEST_SECRET_KEY, 'HS256'));
            $decodedArray = (array) $decoded;
            $userId = $decodedArray['userId'];
            UserActions::authLoginToken($userId, $this->token);
            return $userId;
        } 
        catch (LogicException $e)
        {
            getJsonRow(false, "User could not be verified!");
        } 
        catch (UnexpectedValueException $e)
        {
            getJsonRow(false, "User could not be verified!");
        }
    }
}