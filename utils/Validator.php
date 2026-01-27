<?php

/**
 * Clase para validación y sanitización de datos
 */
class Validator {
    private $errors = [];

    /**
     * Sanitizar string
     */
    public static function sanitizeString($value) {
        return trim(htmlspecialchars(stripslashes($value), ENT_QUOTES, 'UTF-8'));
    }

    /**
     * Sanitizar email
     */
    public static function sanitizeEmail($value) {
        return filter_var(trim($value), FILTER_SANITIZE_EMAIL);
    }

    /**
     * Sanitizar número entero
     */
    public static function sanitizeInt($value) {
        return filter_var($value, FILTER_SANITIZE_NUMBER_INT);
    }

    /**
     * Sanitizar número flotante
     */
    public static function sanitizeFloat($value) {
        return filter_var($value, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
    }

    /**
     * Validar email
     */
    public function validateEmail($email, $fieldName = 'email') {
        if (empty($email)) {
            $this->errors[] = "$fieldName es requerido";
            return false;
        }
        
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->errors[] = "$fieldName no es válido";
            return false;
        }
        
        return true;
    }

    /**
     * Validar string requerido
     */
    public function validateRequired($value, $fieldName) {
        if (empty($value) || (is_string($value) && strlen(trim($value)) === 0)) {
            $this->errors[] = "$fieldName es requerido";
            return false;
        }
        return true;
    }

    /**
     * Validar longitud mínima
     */
    public function validateMinLength($value, $min, $fieldName) {
        if (strlen($value) < $min) {
            $this->errors[] = "$fieldName debe tener al menos $min caracteres";
            return false;
        }
        return true;
    }

    /**
     * Validar longitud máxima
     */
    public function validateMaxLength($value, $max, $fieldName) {
        if (strlen($value) > $max) {
            $this->errors[] = "$fieldName no debe exceder $max caracteres";
            return false;
        }
        return true;
    }

    /**
     * Validar número
     */
    public function validateNumber($value, $fieldName, $isInteger = false) {
        if (!is_numeric($value)) {
            $this->errors[] = "$fieldName debe ser un número válido";
            return false;
        }
        
        if ($isInteger && !is_int((int)$value) && strval((int)$value) !== strval($value)) {
            $this->errors[] = "$fieldName debe ser un número entero";
            return false;
        }
        
        return true;
    }

    /**
     * Validar rango numérico
     */
    public function validateRange($value, $min, $max, $fieldName) {
        if (!is_numeric($value)) {
            $this->errors[] = "$fieldName debe ser un número";
            return false;
        }
        
        if ($value < $min || $value > $max) {
            $this->errors[] = "$fieldName debe estar entre $min y $max";
            return false;
        }
        
        return true;
    }

    /**
     * Validar formato de fecha
     */
    public function validateDate($date, $format = 'Y-m-d H:i:s', $fieldName = 'fecha') {
        $d = \DateTime::createFromFormat($format, $date);
        
        if (!$d || $d->format($format) !== $date) {
            $this->errors[] = "$fieldName debe tener el formato $format";
            return false;
        }
        
        return true;
    }

    /**
     * Validar patrón regex
     */
    public function validatePattern($value, $pattern, $fieldName) {
        if (!preg_match($pattern, $value)) {
            $this->errors[] = "$fieldName tiene un formato inválido";
            return false;
        }
        return true;
    }

    /**
     * Validar que el valor esté en una lista permitida
     */
    public function validateIn($value, $allowedValues, $fieldName) {
        if (!in_array($value, $allowedValues, true)) {
            $this->errors[] = "$fieldName no es un valor válido";
            return false;
        }
        return true;
    }

    /**
     * Validar contraseña (mínimo 8 caracteres, mayúscula, minúscula, número)
     */
    public function validatePassword($password, $fieldName = 'contraseña') {
        if (strlen($password) < 8) {
            $this->errors[] = "$fieldName debe tener al menos 8 caracteres";
            return false;
        }
        
        if (!preg_match('/[A-Z]/', $password)) {
            $this->errors[] = "$fieldName debe contener al menos una mayúscula";
            return false;
        }
        
        if (!preg_match('/[a-z]/', $password)) {
            $this->errors[] = "$fieldName debe contener al menos una minúscula";
            return false;
        }
        
        if (!preg_match('/[0-9]/', $password)) {
            $this->errors[] = "$fieldName debe contener al menos un número";
            return false;
        }
        
        return true;
    }

    /**
     * Validar URL
     */
    public function validateUrl($url, $fieldName = 'url') {
        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            $this->errors[] = "$fieldName no es una URL válida";
            return false;
        }
        return true;
    }

    /**
     * Obtener errores
     */
    public function getErrors() {
        return $this->errors;
    }

    /**
     * Verificar si hay errores
     */
    public function hasErrors() {
        return !empty($this->errors);
    }

    /**
     * Limpiar errores
     */
    public function clearErrors() {
        $this->errors = [];
    }

    /**
     * Agregar error personalizado
     */
    public function addError($message) {
        $this->errors[] = $message;
    }

    /**
     * Validar un array completo de datos
     */
    public function validate($data, $rules) {
        $this->clearErrors();
        
        foreach ($rules as $field => $fieldRules) {
            $value = $data[$field] ?? null;
            
            foreach ($fieldRules as $rule) {
                $this->applyRule($value, $rule, $field);
            }
        }
        
        return !$this->hasErrors();
    }

    /**
     * Aplicar una regla de validación
     */
    private function applyRule($value, $rule, $field) {
        if (is_string($rule)) {
            // Reglas simples: 'required', 'email', etc.
            switch ($rule) {
                case 'required':
                    $this->validateRequired($value, $field);
                    break;
                case 'email':
                    $this->validateEmail($value, $field);
                    break;
            }
        } elseif (is_array($rule)) {
            // Reglas complejas: ['min_length' => 8], ['range' => [1, 100]], etc.
            $ruleName = key($rule);
            $ruleValue = $rule[$ruleName];
            
            switch ($ruleName) {
                case 'min_length':
                    $this->validateMinLength($value, $ruleValue, $field);
                    break;
                case 'max_length':
                    $this->validateMaxLength($value, $ruleValue, $field);
                    break;
                case 'min':
                case 'range':
                    if (is_array($ruleValue)) {
                        $this->validateRange($value, $ruleValue[0], $ruleValue[1], $field);
                    }
                    break;
                case 'number':
                    $this->validateNumber($value, $field, $ruleValue ?? false);
                    break;
                case 'pattern':
                    $this->validatePattern($value, $ruleValue, $field);
                    break;
                case 'in':
                    $this->validateIn($value, $ruleValue, $field);
                    break;
                case 'url':
                    $this->validateUrl($value, $field);
                    break;
            }
        }
    }
}

?>
