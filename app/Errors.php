<?php


namespace App;

trait Errors
{
    private $errors;
    private static $err;
    public function setError($key, $value)
    {
        if ( !$this->errors ) {
            $this->errors = [];
        }
        $this->errors[$key] = $value;
    }
    public function getErrors()
    {
        return $this->errors ? $this->errors : [];
    }
    public function getFirstError($key = null)
    {
        if ( $key ) {
            return isset($this->errors[$key]) ? $this->errors[$key] : null;
        }
        return $this->errors ? reset($this->errors) : null;
    }

    public static function Error($err = null)
    {
        if ( $err ) {
            self::$err = $err;
        } else {
            $err = self::$err;
            self::$err = null;
            return $err;
        }
        return ;
    }

    public static function clearErr()
    {
        self::$err = null;
    }

    public  function clearErrors()
    {
        $this->errors = null;
    }
}

