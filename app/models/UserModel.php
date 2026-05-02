<?php

class UserModel extends Model {
    protected string $table = 'users';

    public function findByEmail(string $email): array|false {
        return $this->query(
            "SELECT * FROM {$this->table} WHERE email = ? LIMIT 1",
            [$email]
        )->fetch();
    }

    public function findByPhone(string $phone): array|false {
        return $this->query(
            "SELECT * FROM {$this->table} WHERE phone = ? LIMIT 1",
            [$phone]
        )->fetch();
    }

    public function findByIdentifier(string $identifier): array|false {
        $identifier = trim($identifier);

        // Try email first
        $user = $this->findByEmail($identifier);
        if ($user) return $user;

        // Try as phone number (normalize first)
        if (AuthHelper::isPhone($identifier)) {
            $normalized = AuthHelper::normalizePhone($identifier);
            $user = $this->findByPhone($normalized);
            if ($user) return $user;
        }

        // Final attempt: search phone directly with original input
        return $this->findByPhone($identifier);
    }

    public function createUser(string $name, ?string $email, ?string $phone, string $password, string $role, ?string $profileImage = null): int {
        return $this->insert([
            'name'          => $name,
            'email'         => $email,
            'phone'         => $phone,
            'password'      => AuthHelper::hashPassword($password),
            'role'          => $role,
            'profile_image' => $profileImage,
        ]);
    }

    public function updatePassword(int $userId, string $newPassword): bool {
        return $this->update($userId, [
            'password' => AuthHelper::hashPassword($newPassword),
        ]);
    }

    public function updateEmail(int $userId, string $email): bool {
        return $this->update($userId, ['email' => $email]);
    }

    public function updatePhone(int $userId, string $phone): bool {
        return $this->update($userId, ['phone' => $phone]);
    }
}