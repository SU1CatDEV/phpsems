<?php

namespace Geekbrains\Application1\Domain\Models;

use Geekbrains\Application1\Application\Application;
use Geekbrains\Application1\Infrastructure\Storage;

class User {

    private ?int $idUser;

    private ?string $userName;

    private ?string $userLastName;
    private ?int $userBirthday;

    private static string $storageAddress = '/storage/birthdays.txt';

    public function __construct(string $name = null, string $lastName = null, int $birthday = null, int $id_user = null){
        $this->userName = $name;
        $this->userLastName = $lastName;
        $this->userBirthday = $birthday;
        $this->idUser = $id_user;
    }

    public function setUserId(int $id_user): void {
        $this->idUser = $id_user;
    }

    public function getUserId(): ?int {
        return $this->idUser;
    }

    public function setName(string $userName) : void {
        $this->userName = $userName;
    }

    public function setLastName(string $userLastName) : void {
        $this->userLastName = $userLastName;
    }

    public function getUserName(): string|null {
        return $this->userName;
    }

    public function getUserLastName(): string|null {
        return $this->userLastName;
    }

    public function getUserBirthday(): int|null {
        return $this->userBirthday;
    }

    public function setBirthdayFromString(string $birthdayString) : void {
        $this->userBirthday = strtotime($birthdayString);
    }

    public static function getAllUsersFromStorage(): array {
        $sql = "SELECT * FROM users";

        $handler = Application::$storage->get()->prepare($sql);
        $handler->execute();
        $result = $handler->fetchAll();

        $users = [];

        foreach($result as $item){
            $user = new User($item['user_name'], $item['user_lastname'], $item['user_birthday_timestamp'], $item['id_user']);
            $users[] = $user;
        }
        
        return $users;
    }

    private static function validateDate($date) {
        $format = 'd-m-Y';
        $dateTime = \DateTime::createFromFormat($format, $date);
        
        return $dateTime && $dateTime->format($format) === $date;
    }

    private static function validateName($name) {
        $pattern = '/^(?!.*[А-Яа-яЁё])([A-Za-z\s]+)$|^(?!.*[A-Za-z])([А-Яа-яЁё\s]+)$/u';
        return preg_match($pattern, $name);
    }

    public static function validateRequestData(): bool{
        if(
            isset($_POST['name']) && !empty($_POST['name']) &&
            isset($_POST['lastname']) && !empty($_POST['lastname']) &&
            isset($_POST['birthday']) && !empty($_POST['birthday']) &&
            self::validateName($_POST['name'] . $_POST['lastname']) &&
            self::validateDate($_POST['birthday'])
        ){
            return true;
        }
        else{
            return false;
        }
    }

    public function setParamsFromRequestData(): void {
        $this->userName = $_POST['name'];
        $this->userLastName = $_POST['lastname'];
        $this->setBirthdayFromString($_POST['birthday']); 
    }

    public function saveToStorage(){
        $sql = "INSERT INTO users(user_name, user_lastname, user_birthday_timestamp, `login`) VALUES (:user_name, :user_lastname, :user_birthday, :user_login)";

        $handler = Application::$storage->get()->prepare($sql);
        $handler->execute([
            'user_name' => $this->userName,
            'user_lastname' => $this->userLastName,
            'user_birthday' => $this->userBirthday,
            'user_login' => ""
        ]);
    }

    public static function exists(int $id): bool{
        $sql = "SELECT count(id_user) as user_count FROM users WHERE id_user = :id_user";

        $handler = Application::$storage->get()->prepare($sql);
        $handler->execute([
            'id_user' => $id
        ]);

        $result = $handler->fetchAll();

        if(count($result) > 0 && $result[0]['user_count'] > 0){
            return true;
        }
        else{
            return false;
        }
    }

    public function updateUser(array $userDataArray): void{
        $sql = "UPDATE users SET ";

        $userDataArray = array_filter($userDataArray, function($value) {
            return !empty($value);
        });

        $counter = 0;
        $totalItems = count($userDataArray);

        foreach($userDataArray as $key => $value) {
            $sql .= $key . " = :" . $key;

            if($counter != $totalItems - 1) {
                $sql .= ", ";
            }

            $counter++;
        }

        $sql .= " WHERE id_user = :id_user";

        $userDataArray['id_user'] = $this->idUser;

        $handler = Application::$storage->get()->prepare($sql);
        $handler->execute($userDataArray);
    }

    public static function deleteFromStorage(int $user_id) : void {
        $sql = "DELETE FROM users WHERE id_user = :id_user";

        $handler = Application::$storage->get()->prepare($sql);
        $handler->execute(['id_user' => $user_id]);
    }

    public static function setDBToken(string $user_login, string $token){
        $sql = "UPDATE users SET token = :token WHERE `login` = :user_login";

        $handler = Application::$storage->get()->prepare($sql);
        $handler->execute(['token' => $token, 'user_login' => $user_login]);
    }

    public static function getUserByToken(string $token) {
        $sql = "SELECT * FROM users WHERE token = :token";

        $handler = Application::$storage->get()->prepare($sql);
        $handler->execute(['token' => $token]);
        return $handler->fetchAll();
    }

    public static function tokenLogin(): void {
        if (isset($_COOKIE['token']) && !empty($_COOKIE['token']) && !is_null($_COOKIE['token'])) {
            $result = User::getUserByToken($_COOKIE['token']);
            if ($result) {
                $_SESSION['user_name'] = $result[0]['user_name'];
                $_SESSION['user_lastname'] = $result[0]['user_lastname'];
                $_SESSION['id_user'] = $result[0]['id_user'];
            }
        }
    } 

    public static function unsetToken(int $id_user) {
        $sql = "UPDATE users SET token = null WHERE id_user = :id_user";

        $handler = Application::$storage->get()->prepare($sql);
        $handler->execute(['id_user' => $id_user]);
    }
}