<?php declare(strict_types = 1);


$app->group('/api/v1', function () {
    $this->get(
        '/translate/text/{text}/target-lang/{target-lang}[/source-lang/{source-lang}]',
        \App\Http\Controller\TranslateController::class . ':translate'
    )->setName('translate');

    // Get all cached translations
    $this->get('/translation', \App\Http\Controller\TranslateController::class . ':translations')
         ->setName('translation.index');

    // Update translation
    $this->patch('/translation', \App\Http\Controller\TranslateController::class . ':edit')
         ->setName('translation.edit');
    // Delete translation
    $this->delete('/translation', \App\Http\Controller\TranslateController::class . ':delete')
         ->setName('translation.delete');
});

