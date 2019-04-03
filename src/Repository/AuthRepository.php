<?php declare(strict_types=1);

namespace App\Repository;

use Psr\Container\ContainerInterface;
use Firebase\JWT\JWT;
use Illuminate\Database\Query\Builder;

final class AuthRepository
{
    /**
     * @var Builder
     */
    protected $userTable;

    /**
     * @var array
     */
    protected $settings;

    /**
     * AuthRepository constructor.
     *
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->userTable = $container->get('database')->table('users');
        $this->settings = $container->get('settings')['jwt'];
    }

    /**
     * Login user.
     *
     * @param string $email
     * @param string $password
     *
     * @return string Access token
     */
    public function login(string $email, string $password): string
    {
        $token = '';

        $users = $this->userTable->where('email', '=', $email);

        if($users->exists()) {
            $user = $users->get()->first();
            
            if($user && password_verify($password, $user->password)) {
                $token = JWT::encode([
                    'id' => $user->id,
                    'email' => $user->email,
                ], $this->settings['secret']);
            }
        }

        return $token;
    }
}
