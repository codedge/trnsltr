<?php declare(strict_types = 1);

namespace App\Http\Controller;

use App\Repository\AuthRepository;
use Awurth\SlimValidation\Validator;
use App\Http\Validators\Validator as ValidatorRule;
use Slim\Http\Request;
use Slim\Http\Response;

final class AuthController
{
    /**
     * @var Validator
     */
    protected $validator;

    /**
     * @var AuthRepository
     */
    protected $repository;

    /**
     * HomeController constructor.
     *
     * @param Validator $validator
     * @param AuthRepository $repository
     */
    public function __construct(Validator $validator, AuthRepository $repository)
    {
        $this->validator = $validator;
        $this->repository = $repository;
    }

    /**
     * Get access token.
     *
     * @param Request $request
     * @param Response $response
     *
     * @return Response
     */
    public function token(Request $request, Response $response)
    {
        $validator = $this->validator->validate(
            $request,
            [
                'email' => ValidatorRule::notEmpty(),
                'password' => ValidatorRule::notEmpty(),
            ]
        );

        if($validator->isValid()) {
            $token = $this->repository->login(
                $request->getParsedBodyParam('email'),
                $request->getParsedBodyParam('password')
            );

            if(!empty($token)) {
                return $response->withStatus(200)->withJson([
                    'status' => 'Success',
                    'token' => $token,
                ]);
            }

            return $response->withStatus(401)->withJson([
                'status' => 'Authentication error',
                'message' => 'User not found',
            ]);

        }

        return $response->withStatus(400)->withJson(
            [
                'status' => 'Validation Error',
                'data' => $validator->getErrors()
            ]
        );
    }
}
