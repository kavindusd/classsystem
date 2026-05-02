<?php

class OtpModel extends Model {
    protected string $table = 'otps';

    public function store(string $identifier, string $code, string $purpose): bool {
        // Invalidate any existing unused OTPs for same identifier+purpose
        $this->query(
            "UPDATE {$this->table} SET used = 1
             WHERE identifier = ? AND purpose = ? AND used = 0",
            [$identifier, $purpose]
        );

        $expires = date('Y-m-d H:i:s', strtotime('+' . OTP_EXPIRY_MINUTES . ' minutes'));

        $this->insert([
            'identifier' => $identifier,
            'code'       => $code,
            'purpose'    => $purpose,
            'used'       => 0,
            'expires_at' => $expires,
        ]);

        return true;
    }

    public function verify(string $identifier, string $code, string $purpose): bool {
        $now = date('Y-m-d H:i:s');
        $row = $this->query(
            "SELECT * FROM {$this->table}
             WHERE identifier = ? AND code = ? AND purpose = ?
               AND used = 0 AND expires_at > ?",
            [$identifier, $code, $purpose, $now]
        )->fetch();

        return (bool) $row;
    }

    public function invalidate(string $identifier, string $purpose): void {
        $this->query(
            "UPDATE {$this->table} SET used = 1
             WHERE identifier = ? AND purpose = ?",
            [$identifier, $purpose]
        );
    }
}