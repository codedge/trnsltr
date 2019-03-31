<?php declare(strict_types = 1);

namespace App\Http\Controller;

use Awurth\SlimValidation\Validator;
use App\Http\Validators\Validator as ValidatorRule;
use App\Repository\TranslateRepository;
use Slim\Http\Request;
use Slim\Http\Response;

final class TranslateController
{
    /**
     * @var TranslateRepository
     */
    protected $repository;

    /**
     * @var Validator
     */
    protected $validator;

    /**
     * TranslateController constructor.
     *
     * @param Validator           $validator
     * @param TranslateRepository $repository
     */
    public function __construct(Validator $validator, TranslateRepository $repository)
    {
        $this->repository = $repository;
        $this->validator = $validator;
    }

    /**
     * Translate string.
     *
     * @param Request  $request
     * @param Response $response
     * @param array    $args
     *
     * @return Response
     */
    public function translate(Request $request, Response $response, array $args): Response
    {
        $validator = $this->validator->validate(
            $args,
            [
            'text' => ValidatorRule::notEmpty(),
            'target-lang' => ValidatorRule::notEmpty()->length(2),
            'source-lang' => ValidatorRule::optional(ValidatorRule::length(2))
            ]
        );

        if ($validator->isValid()) {
            $translationData = $this->repository->translate($args['text'], $args['target-lang'], (string) $args['source-lang']);

            return $response->withStatus(200)->withJson(
                [
                'status' => 'Success',
                'loaded_from_cache' => $translationData->getLoadedFromCache(),
                'data'   => $translationData,
                ]
            );
        }

        return $response->withStatus(400)->withJson(
            [
            'status' => 'Validation Error',
            'loaded_from_cache' => false,
            'data' => $validator->getErrors()
            ]
        );
    }

    /**
     * Get all translations that are already cached.
     *
     * @param Request $request
     * @param Response $response
     * @param array $args
     *
     * @return Response
     */
    public function translations(Request $request, Response $response, array $args): Response
    {
        $translations = $this->repository->getAllTranslations();

        return $response->withStatus(200)->withJson(
            [
                'status' => 'Success',
                'data'   => $translations,
            ]
        );
    }

    /**
     * @param Request $request
     * @param Response $response
     *
     * @return Response
     */
    public function edit(Request $request, Response $response): Response
    {
        $validator = $this->validator->validate(
            $request,
            [
                'source_text' => ValidatorRule::notEmpty(),
                'source_lang' => ValidatorRule::notEmpty()->length(2),
                'target_lang' => ValidatorRule::notEmpty()->length(2),
                'target_text' => ValidatorRule::notEmpty(),
            ]
        );

        if($validator->isValid()) {
            $changed = $this->repository->edit(
                $request->getParsedBodyParam('source_text'),
                $request->getParsedBodyParam('source_lang'),
                $request->getParsedBodyParam('target_lang'),
                $request->getParsedBodyParam('target_text'),
            );

            if($changed) {
                return $response->withStatus(200)->withJson([
                    'status' => 'Success',
                    'data'   => $changed,
                ]);
            }
        }

        return $response->withStatus(400)->withJson(
            [
                'status' => 'Validation Error',
                'loaded_from_cache' => false,
                'data' => $validator->getErrors()
            ]
        );
    }

    /**
     * Delete a translation.
     *
     * @param Request $request
     * @param Response $response
     *
     * @return Response
     */
    public function delete(Request $request, Response $response): Response
    {
        $validator = $this->validator->validate(
            $request,
            [
                'source_text' => ValidatorRule::notEmpty(),
                'source_lang' => ValidatorRule::notEmpty()->length(2),
                'target_lang' => ValidatorRule::notEmpty()->length(2),
            ]
        );

        if($validator->isValid()) {
            $deleted = $this->repository->delete(
                $request->getParsedBodyParam('source_text'),
                $request->getParsedBodyParam('source_lang'),
                $request->getParsedBodyParam('target_lang'),
            );

            if($deleted) {
                return $response->withStatus(200)->withJson(
                    [
                        'status' => 'Success',
                    ]
                );
            }
        }

        return $response->withStatus(400)->withJson(
            [
                'status' => 'Validation Error',
                'loaded_from_cache' => false,
                'data' => $validator->getErrors()
            ]
        );
    }
}
