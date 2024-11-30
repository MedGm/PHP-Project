
<?php
class AuthService {
    private $db;
    private const MAX_LOGIN_ATTEMPTS = 3;
    private const LOCKOUT_DURATION = 900; // 15 minutes

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function authenticateAdmin($email, $password): bool {
        if ($this->isLockedOut($email)) {
            throw new Exception('Account is locked. Please try again later.');
        }

        $stmt = $this->db->prepare("SELECT * FROM administrators WHERE email = ?");
        $stmt->execute([$email]);
        $admin = $stmt->fetch();

        if ($admin && password_verify($password, $admin['password'])) {
            $this->resetLoginAttempts($email);
            return true;
        }

        $this->incrementLoginAttempts($email);
        return false;
    }

    public function authenticateCoordinator($email, $password, $program): bool {
        if ($this->isLockedOut($email)) {
            throw new Exception('Account is locked. Please try again later.');
        }

        $stmt = $this->db->prepare("
            SELECT * FROM coordinators 
            WHERE email = ? AND program = ?
        ");
        $stmt->execute([$email, $program]);
        $coordinator = $stmt->fetch();

        if ($coordinator && password_verify($password, $coordinator['password'])) {
            $this->resetLoginAttempts($email);
            return true;
        }

        $this->incrementLoginAttempts($email);
        return false;
    }

    private function isLockedOut($email): bool {
        $stmt = $this->db->prepare("
            SELECT attempts, last_attempt 
            FROM login_attempts 
            WHERE email = ?
        ");
        $stmt->execute([$email]);
        $result = $stmt->fetch();

        if (!$result) {
            return false;
        }

        if ($result['attempts'] >= self::MAX_LOGIN_ATTEMPTS) {
            $lockoutTime = strtotime($result['last_attempt']) + self::LOCKOUT_DURATION;
            if (time() < $lockoutTime) {
                return true;
            }
            $this->resetLoginAttempts($email);
        }

        return false;
    }

    private function incrementLoginAttempts($email): void {
        $this->db->beginTransaction();
        try {
            $stmt = $this->db->prepare("
                INSERT INTO login_attempts (email, attempts, last_attempt)
                VALUES (?, 1, NOW())
                ON DUPLICATE KEY UPDATE
                    attempts = attempts + 1,
                    last_attempt = NOW()
            ");
            $stmt->execute([$email]);
            $this->db->commit();
        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    private function resetLoginAttempts($email): void {
        $stmt = $this->db->prepare("
            DELETE FROM login_attempts 
            WHERE email = ?
        ");
        $stmt->execute([$email]);
    }
}