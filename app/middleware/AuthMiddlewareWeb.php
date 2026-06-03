<?php
 
class AuthMiddlewareWeb {
 
  public static function isLogin() {
    if (isset($_SESSION['token'])) {
      return true;
    } else {
      return false;  
    }
  }
 
  public static function isAdmin() {
    if (isset($_SESSION['token']) && $_SESSION['token']['is_admin'] == 1) {
      return true;
    } else {
      return false;  
    }
  }

 public static function canEdit($userId) {
    if (isset($_SESSION['token']) && ($_SESSION['token']['is_admin'] === 1 || $_SESSION['token']['id'] === $userId)) {
      return true;
    }  
      return false;  
    }
    
  } 