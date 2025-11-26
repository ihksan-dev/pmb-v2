<?php
class Notification 
{
    public static function setFlash($type, $message)
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        
        $_SESSION['flash_message'] = [
            'type' => $type,
            'message' => $message
        ];
    }

    public static function getFlash()
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        
        if (isset($_SESSION['flash_message'])) {
            $flash = $_SESSION['flash_message'];
            unset($_SESSION['flash_message']);
            return $flash;
        }
        
        return null;
    }

    public static function hasFlash()
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        
        return isset($_SESSION['flash_message']);
    }

    public static function showFlash()
    {
        $flash = self::getFlash();
        
        if ($flash) {
            $class = $flash['type'] === 'error' ? 'alert-danger' : 'alert-success';
            echo '<div class="alert ' . $class . '" role="alert">' . htmlspecialchars($flash['message']) . '</div>';
        }
    }

    public static function setError($message)
    {
        self::setFlash('error', $message);
    }

    public static function setSuccess($message)
    {
        self::setFlash('success', $message);
    }
}