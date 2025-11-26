<?php
require_once '../core/Database.php';

class Captcha 
{
    private $db;

    public function __construct()
    {
        $this->db = new Database();
    }

    public function generateCaptcha()
    {
        // Generate 6 digit random number
        $captcha_code = rand(100000, 999999);
        
        // Get session ID
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        $session_id = session_id();
        
        // Insert into database
        $this->db->query("INSERT INTO captcha_sessions (session_id, captcha_code, ip_address, user_agent) 
                         VALUES (:session_id, :captcha_code, :ip_address, :user_agent)");
        $this->db->bind(':session_id', $session_id);
        $this->db->bind(':captcha_code', $captcha_code);
        $this->db->bind(':ip_address', $_SERVER['REMOTE_ADDR']);
        $this->db->bind(':user_agent', $_SERVER['HTTP_USER_AGENT'] ?? '');
        
        $this->db->execute();
        
        return $session_id;
    }

    public function createCaptchaImage($session_id)
    {
        // Get captcha code from database
        $this->db->query("SELECT captcha_code FROM captcha_sessions WHERE session_id = :session_id AND is_used = 0 AND expires_at > NOW()");
        $result = $this->db->single();
        
        if (!$result) {
            // Generate a new one if not found
            $session_id = $this->generateCaptcha();
            $this->db->query("SELECT captcha_code FROM captcha_sessions WHERE session_id = :session_id");
            $result = $this->db->single();
        }
        
        $captcha_code = $result['captcha_code'];
        
        // Create image
        $width = 150;
        $height = 50;
        $image = imagecreate($width, $height);
        
        // Set colors
        $bg_color = imagecolorallocate($image, rand(0, 100), rand(0, 100), rand(0, 100));
        $text_color = imagecolorallocate($image, rand(150, 255), rand(150, 255), rand(150, 255));
        $line_color = imagecolorallocate($image, rand(100, 200), rand(100, 200), rand(100, 200));
        
        // Add noise
        for ($i = 0; $i < 10; $i++) {
            imageline($image, rand(0, $width), rand(0, $height), rand(0, $width), rand(0, $height), $line_color);
        }
        
        // Add text with random rotation
        $font_size = 20;
        $x = 15;
        for ($i = 0; $i < strlen($captcha_code); $i++) {
            $angle = rand(-15, 15);
            $y = rand(25, 35);
            $char_color = imagecolorallocate($image, rand(150, 255), rand(150, 255), rand(150, 255));
            imagettftext($image, $font_size, $angle, $x, $y, $char_color, 'arial.ttf', $captcha_code[$i]);
            $x += 20;
        }
        
        // Output image
        header('Content-Type: image/png');
        imagepng($image);
        imagedestroy($image);
    }

    public function validateCaptcha($session_id, $input_code)
    {
        $this->db->query("SELECT * FROM captcha_sessions WHERE session_id = :session_id AND captcha_code = :input_code AND is_used = 0 AND expires_at > NOW()");
        $result = $this->db->single();
        
        if ($result) {
            return ['valid' => true, 'data' => $result];
        } else {
            return ['valid' => false, 'data' => null];
        }
    }

    public function markCaptchaUsed($session_id)
    {
        $this->db->query("UPDATE captcha_sessions SET is_used = 1 WHERE session_id = :session_id");
        $this->db->bind(':session_id', $session_id);
        return $this->db->execute();
    }

    public function refreshCaptcha($session_id)
    {
        // Mark old captcha as used
        $this->markCaptchaUsed($session_id);
        
        // Generate new captcha
        return $this->generateCaptcha();
    }
}