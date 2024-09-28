<?php

namespace Geekbrains\Application1\Domain\Controllers;

use Geekbrains\Application1\Application\Application;
use Geekbrains\Application1\Application\Render;
use Geekbrains\Application1\Application\Auth;
use Geekbrains\Application1\Domain\Models\User;

class UserController extends AbstractController {

    protected array $actionsPermissions = [
        'actionHash' => ['admin', 'some'],
        'actionSave' => ['admin'],
        'actionUpdate' => ['admin'],
        'actionDelete' => ['admin'],
        'actionAuth' => ['logged_out'],
        'actionLogin' => ['logged_out']
    ];

    public function actionIndex(): string {
        $users = User::getAllUsersFromStorage();
        
        $render = new Render();

        if(!$users){
            return $render->renderPage(
                'user-empty.tpl', 
                [
                    'title' => 'Список пользователей в хранилище',
                    'message' => "Список пуст или не найден"
                ]);
        }
        else{
            return $render->renderPage(
                'user-index.tpl', 
                [
                    'title' => 'Список пользователей в хранилище',
                    'users' => $users
                ]);
        }
    }

    public function actionSave(): string {
        if(User::validateRequestData()) {
            $user = new User();
            $user->setParamsFromRequestData();
            $user->saveToStorage();

            $render = new Render();

            return $render->renderPage(
                'user-created.tpl', 
                [
                    'title' => 'Пользователь создан',
                    'message' => "Создан пользователь " . $user->getUserName() . " " . $user->getUserLastName()
                ]);
        }
        else {
            throw new \Exception("Переданные данные некорректны");
        }
    }

    public function actionUpdate(): string {
        if(!isset($_POST['id'])) {
            throw new \Exception("Переданные данные некорректны"); 
        }
        if(User::exists($_POST['id'])) {
            $user = new User();
            $user->setUserId($_POST['id']);
            
            $arrayData = [];

            if(isset($_POST['name']))
                $arrayData['user_name'] = $_POST['name'];

            if(isset($_POST['lastname'])) {
                $arrayData['user_lastname'] = $_POST['lastname'];
            }
            
            $user->updateUser($arrayData);
        }
        else {
            throw new \Exception("Пользователь не существует");
        }

        $render = new Render();
        return $render->renderPage(
            'user-created.tpl', 
            [
                'title' => 'Пользователь обновлен',
                'message' => "Обновлен пользователь " . $user->getUserId()
            ]);
    }

    public function actionDelete(): string {
        if(!isset($_POST['id'])) {
            throw new \Exception("Переданные данные некорректны"); 
        }
        if(User::exists($_POST['id'])) {
            User::deleteFromStorage($_POST['id']);

            $render = new Render();
            
            return $render->renderPage(
                'user-removed.tpl', []
            );
        }
        else {
            throw new \Exception("Пользователь не существует");
        }
    }

    public function actionAuth(): string {
        $render = new Render();
        
        return $render->renderPageWithForm(
                'user-auth.tpl', 
                [
                    'title' => 'Форма логина'
                ]);
    }

    public function actionHash(): string {
        return Auth::getPasswordHash($_GET['pass_string']);
    }

    public function actionLogin(): string {
        $result = false;

        if(isset($_POST['login']) && isset($_POST['password'])){
            $result = Application::$auth->proceedAuth($_POST['login'], $_POST['password']);
        }
        
        if(!$result){
            $render = new Render();

            return $render->renderPageWithForm(
                'user-auth.tpl', 
                [
                    'title' => 'Форма логина',
                    'auth_success' => false,
                    'auth_error' => 'Неверные логин или пароль'
                ]);
        }
        else{
            $token = bin2hex(random_bytes(32));
            setcookie("token", $token, time() + (86400 * 30), "/");
            User::setDBToken($_POST['login'], $token);
            header("Location: /");
            return "";
        }
    }

    public function actionLogout(): string {
        $render = new Render();

        User::unsetToken($_SESSION['id_user']);

        session_unset();
        session_destroy();

        unset($_COOKIE['token']);

        session_start();

        header("Location: /");
        return "";
    }
}