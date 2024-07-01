<?php

namespace App\Service;

use App\Constants\AuthenticationPolicy;
use App\Core\Http\Cookies;
use App\Core\Environment;
use App\Core\Http\Jwt;
use App\Interfaces\Repository\UserRepositoryInterface;
use App\Interfaces\Service\AuthServiceInterface;
use App\Interfaces\Service\CurrentTokenServiceInterface;
use App\Model\Entity\UserModel;
use App\Model\ValuableObject\Email;
use App\Model\ValuableObject\Password;

class AuthService implements AuthServiceInterface
{
    public const SESSION_IDENTIFIER = 'basebuild-in';
    public UserModel $loggedUser;

    private UserRepositoryInterface $userRepository;
    private CurrentTokenServiceInterface $currentTokenService;

    public function __construct(UserRepositoryInterface $UserRepository, CurrentTokenServiceInterface $currentTokenService)
    {
        $this->userRepository = $UserRepository;
        $this->currentTokenService = $currentTokenService;
    }

    public function getLoggedUser(): ?UserModel
    {
        return $this->loggedUser;
    }

    public function setLoggedUser(UserModel $user): void
    {
        $this->loggedUser = $user;
    }

    public function validateCredentials(string $email, string $password): ?string
    {
        $email = new Email($email);
        $user = $this->userRepository->findByEmail($email);

        if (is_null($user)) {
            $invalidPassword = new Password(uniqid());
            $invalidPassword->verify($password);
            return null;
        }

        $passwordObject = new Password($password);
        if (!$passwordObject->verify($user->getPasswordHash())) {
            return null;
        }

        return $user->getID();
    }

    public function setCookies(string $userID): void
    {
        $keepLoggedInUntil = time() + AuthenticationPolicy::DEFAULT_KEEP_LOGGED_IN_TIMESTAMP;

        $info = json_encode([
            'userID' => $userID,
            'time' => time()
        ]);

        $encryptedInfo = $this->encryptInfo($info);

        $token = JWT::create($encryptedInfo, $keepLoggedInUntil);

        $this->currentTokenService->save($userID, $token);

        Cookies::set(self::SESSION_IDENTIFIER, $token, $keepLoggedInUntil);
    }

    public function getCookies(): array
    {
        if (!isset($_COOKIE[self::SESSION_IDENTIFIER])) {
            return [];
        }

        $token = $_COOKIE[self::SESSION_IDENTIFIER];

        try {
            $payload = JWT::read($token);

            $payload['data'] = $this->decryptInfo($payload['data']);

            $info = json_decode($payload['data'], true);
            if ($info === null || json_last_error() !== JSON_ERROR_NONE) {
                throw new \Exception("Invalid JSON data in info.");
            }

            return [
                'info' => $info,
                'token' => $token
            ];
        } catch (\Exception $e) {
            throw new \Exception("Invalid token: " . $e->getMessage());
        }
    }

    private function decryptInfo(string $encryptedData): ?string
    {
        $decryptedData = openssl_decrypt($encryptedData, Environment::get('COOKIES_ENCRYPT_TYPE'), Environment::get('COOKIES_ENCRYPT_KEY'));
        if ($decryptedData === false) {
            return null;
        }
        return $decryptedData;
    }

    private function encryptInfo(string $info): ?string
    {
        $encryptedInfo = openssl_encrypt($info, Environment::get('COOKIES_ENCRYPT_TYPE'), Environment::get('COOKIES_ENCRYPT_KEY'));
        if ($encryptedInfo === false) {
            return null;
        }
        return $encryptedInfo;
    }

    public function logout(): void
    {
        Cookies::delete(self::SESSION_IDENTIFIER);
    }
}
