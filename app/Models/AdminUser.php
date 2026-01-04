<?php
class AdminUser extends BaseModel {
    protected $table = 'admin_users';
    protected $fillable = ['username', 'password', 'last_login', 'failed_attempts', 'locked_until'];
    
    public function authenticate($username, $password) {
        $user = $this->findOne(['username' => $username]);
        if ($user && password_verify($password, $user['password'])) {
            return $user;
        }
        return false;
    }
    
    public function createUser($username, $password) {
        return $this->create([
            'username' => $username,
            'password' => password_hash($password, PASSWORD_DEFAULT)
        ]);
    }
    
    public function updatePassword($id, $newPassword) {
        return $this->update($id, [
            'password' => password_hash($newPassword, PASSWORD_DEFAULT)
        ]);
    }
    
    public function updateLastLogin($id) {
        return $this->update($id, [
            'last_login' => date('Y-m-d H:i:s')
        ]);
    }
}
?>