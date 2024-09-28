<?php

namespace Geekbrains\Application1\Domain\Controllers;

use Geekbrains\Application1\Application\Application;
use Geekbrains\Application1\Application\Render;
use Geekbrains\Application1\Application\Auth;
use Geekbrains\Application1\Domain\Models\User;

class ManageController extends AbstractController {

    protected array $actionsPermissions = [
        'actionSave' => ['admin'],
        'actionUpdate' => ['admin'],
        'actionDelete' => ['admin']
    ];

    public function actionSave(): string {
        $render = new Render();
        
        return $render->renderPageWithForm(
                'user-form.tpl', 
                [
                    'title' => 'Форма создания пользователя'
                ]);
    }

    public function actionUpdate(): string {
        $render = new Render();
        
        return $render->renderPageWithForm(
                'user-edit.tpl', 
                [
                    'title' => 'Форма обновления пользователя',
                    'name' => isset($_GET['name']) ? $_GET['name'] : "",
                    'lastname' => isset($_GET['lastname']) ? $_GET['lastname'] : "",
                    'id' => isset($_GET['id']) ? $_GET['id'] : ""
                ]);
    }

    public function actionDelete(): string {
        $render = new Render();
        
        return $render->renderPageWithForm(
                'user-delete.tpl', 
                [
                    'title' => 'Форма удаления пользователя'
                ]);
    }
}