<?php


namespace Helper\Concrete5;


use Concrete\Core\Support\Facade\Application;
use Illuminate\Support\Str;
use Concrete\Core\User\RegistrationService;

class User
{
    /** @var \Concrete\Core\Application\Application  */
    protected $app;
    /** @var RegistrationService */
    protected $registrationService;

    public function __construct()
    {
        $this->app = Application::getFacadeApplication();
        $this->registrationService = $this->app->make(RegistrationService::class);

    }

    public function createUser($username, $password)
    {

        $data = [
            'username'=>$username,
            'password'=>$password,
            'email'=>Str::random(10) . '@localhost'
        ];
        $success = $this->registrationService->create($data);

        return $success;


    }

    public function createRandomUser()
    {
        $username = Str::random(8);
        $password = Str::random(16);

        $userInfo = $this->createUser($username, $password);

        if (!is_object($userInfo)) {
            return null;
        }

        return [$username, $password];


    }
}