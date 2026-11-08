<?php

namespace App\Core;

/**
 * Validator - Xác thực dữ liệu input
 */
class Validator
{
    private array $data;
    private array $rules;
    private array $errors = [];
    private array $messages = [];

    public function __construct(array $data, array $rules, array $messages = [])
    {
        $this->data = $data;
        $this->rules = $rules;
        $this->messages = $messages;
    }

    /**
     * Validate dữ liệu
     */
    public function validate(): bool
    {
        $this->errors = [];

        foreach ($this->rules as $field => $ruleString) {
            $rules = explode('|', $ruleString);
            $value = $this->data[$field] ?? null;

            foreach ($rules as $rule) {
                $ruleParts = explode(':', $rule);
                $ruleName = $ruleParts[0];
                $ruleValue = $ruleParts[1] ?? null;

                if (!$this->validateRule($field, $value, $ruleName, $ruleValue)) {
                    break;
                }
            }
        }

        // Lưu old input để dùng trong form
        foreach ($this->data as $key => $value) {
            Session::flash('_old.' . $key, $value);
        }

        return empty($this->errors);
    }

    /**
     * Validate một rule
     */
    private function validateRule(string $field, $value, string $rule, ?string $ruleValue): bool
    {
        switch ($rule) {
            case 'required':
                if (empty($value) && $value !== '0') {
                    $this->addError($field, $rule, "Trường {$field} là bắt buộc.");
                    return false;
                }
                break;

            case 'email':
                if (!empty($value) && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    $this->addError($field, $rule, "Trường {$field} phải là email hợp lệ.");
                    return false;
                }
                break;

            case 'min':
                if (!empty($value) && strlen($value) < (int) $ruleValue) {
                    $this->addError($field, $rule, "Trường {$field} phải có ít nhất {$ruleValue} ký tự.");
                    return false;
                }
                break;

            case 'max':
                if (!empty($value) && strlen($value) > (int) $ruleValue) {
                    $this->addError($field, $rule, "Trường {$field} không được vượt quá {$ruleValue} ký tự.");
                    return false;
                }
                break;

            case 'numeric':
                if (!empty($value) && !is_numeric($value)) {
                    $this->addError($field, $rule, "Trường {$field} phải là số.");
                    return false;
                }
                break;

            case 'integer':
                if (!empty($value) && !filter_var($value, FILTER_VALIDATE_INT)) {
                    $this->addError($field, $rule, "Trường {$field} phải là số nguyên.");
                    return false;
                }
                break;
        }

        return true;
    }

    /**
     * Thêm lỗi
     */
    private function addError(string $field, string $rule, string $message): void
    {
        if (!isset($this->errors[$field])) {
            $this->errors[$field] = [];
        }
        $this->errors[$field][] = $message;
    }

    /**
     * Lấy tất cả lỗi
     */
    public function errors(): array
    {
        return $this->errors;
    }

    /**
     * Lấy lỗi của một field
     */
    public function error(string $field): ?string
    {
        return $this->errors[$field][0] ?? null;
    }

    /**
     * Kiểm tra có lỗi không
     */
    public function fails(): bool
    {
        return !empty($this->errors);
    }

    /**
     * Validate nhanh (static)
     */
    public static function make(array $data, array $rules, array $messages = []): self
    {
        return new self($data, $rules, $messages);
    }
}

