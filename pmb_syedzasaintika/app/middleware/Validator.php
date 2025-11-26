<?php
class Validator 
{
    private $errors = [];

    public function validate($data, $rules)
    {
        $this->errors = [];
        
        foreach ($rules as $field => $rule) {
            $rules_array = explode('|', $rule);
            
            foreach ($rules_array as $rule_part) {
                $rule_parts = explode(':', $rule_part);
                $rule_name = $rule_parts[0];
                
                switch ($rule_name) {
                    case 'required':
                        if (empty($data[$field]) || trim($data[$field]) === '') {
                            $this->errors[$field] = ucfirst(str_replace('_', ' ', $field)) . ' wajib diisi';
                        }
                        break;
                    case 'email':
                        if (!empty($data[$field]) && !filter_var($data[$field], FILTER_VALIDATE_EMAIL)) {
                            $this->errors[$field] = ucfirst(str_replace('_', ' ', $field)) . ' harus berupa email yang valid';
                        }
                        break;
                    case 'min':
                        $min_length = $rule_parts[1];
                        if (!empty($data[$field]) && strlen($data[$field]) < $min_length) {
                            $this->errors[$field] = ucfirst(str_replace('_', ' ', $field)) . ' minimal ' . $min_length . ' karakter';
                        }
                        break;
                    case 'max':
                        $max_length = $rule_parts[1];
                        if (!empty($data[$field]) && strlen($data[$field]) > $max_length) {
                            $this->errors[$field] = ucfirst(str_replace('_', ' ', $field)) . ' maksimal ' . $max_length . ' karakter';
                        }
                        break;
                    case 'numeric':
                        if (!empty($data[$field]) && !is_numeric($data[$field])) {
                            $this->errors[$field] = ucfirst(str_replace('_', ' ', $field)) . ' harus berupa angka';
                        }
                        break;
                    case 'alpha':
                        if (!empty($data[$field]) && !ctype_alpha(str_replace(' ', '', $data[$field]))) {
                            $this->errors[$field] = ucfirst(str_replace('_', ' ', $field)) . ' hanya boleh berisi huruf';
                        }
                        break;
                    case 'alpha_num':
                        if (!empty($data[$field]) && !ctype_alnum($data[$field])) {
                            $this->errors[$field] = ucfirst(str_replace('_', ' ', $field)) . ' hanya boleh berisi huruf dan angka';
                        }
                        break;
                    case 'matches':
                        $field_to_match = $rule_parts[1];
                        if (!empty($data[$field]) && $data[$field] !== $data[$field_to_match]) {
                            $this->errors[$field] = ucfirst(str_replace('_', ' ', $field)) . ' tidak cocok';
                        }
                        break;
                }
            }
        }
        
        return empty($this->errors);
    }

    public function getErrors()
    {
        return $this->errors;
    }

    public function hasErrors()
    {
        return !empty($this->errors);
    }

    public function getError($field)
    {
        return $this->errors[$field] ?? null;
    }
}