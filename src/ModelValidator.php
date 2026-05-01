<?php

declare(strict_types=1);

namespace Opscale\Validations;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;

class ModelValidator
{
    protected Model $model;

    /** @var array<string, mixed> */
    protected array $data;

    /** @var array<string, mixed> */
    protected array $rules;

    /** @var array<string, string> */
    protected array $customMessages;

    /** @var array<string, string> */
    protected array $customAttributes;

    public function __construct(Model $model)
    {
        $this->model = $model;
        $this->initialize();
    }

    public function initialize(): self
    {
        $this->customMessages = $this->getMessages();
        $this->customAttributes = $this->getAttributes();
        $this->rules = $this->getRules();
        $this->data = $this->getData();

        return $this;
    }

    public function validate(): self
    {
        if ($this->rules !== []) {
            Validator::make($this->data, $this->rules)
                ->setCustomMessages($this->customMessages)
                ->addCustomAttributes($this->customAttributes)
                ->validate();
        }

        return $this;
    }

    /**
     * @return array<string, string>
     */
    protected function getMessages(): array
    {
        if (method_exists($this->model, 'validationMessages')) {
            return $this->model->validationMessages();
        }

        $modelClass = get_class($this->model);

        if (property_exists($modelClass, 'validationMessages')) {
            return $modelClass::$validationMessages;
        }

        return [];
    }

    /**
     * @return array<string, string>
     */
    protected function getAttributes(): array
    {
        if (method_exists($this->model, 'validationAttributes')) {
            return $this->model->validationAttributes();
        }

        $modelClass = get_class($this->model);

        if (property_exists($modelClass, 'validationAttributes')) {
            return $modelClass::$validationAttributes;
        }

        return [];
    }

    /**
     * Resolve validation rules for the current operation.
     *
     * Rules may be a flat `field => rule` array, or a per-context array
     * `field => ['create' => ..., 'update' => ...]` resolved from `$model->exists`.
     *
     * @return array<string, mixed>
     */
    protected function getRules(): array
    {
        if (method_exists($this->model, 'validationRules')) {
            $rules = $this->model->validationRules();
        } else {
            $modelClass = get_class($this->model);

            if (! property_exists($modelClass, 'validationRules')) {
                return [];
            }

            $rules = $modelClass::$validationRules;
        }

        $context = $this->model->exists ? 'update' : 'create';

        $resolved = [];

        foreach ($rules as $field => $rule) {
            if (is_array($rule) && (array_key_exists('create', $rule) || array_key_exists('update', $rule))) {
                if (array_key_exists($context, $rule)) {
                    $resolved[$field] = $rule[$context];
                }
            } else {
                $resolved[$field] = $rule;
            }
        }

        return $resolved;
    }

    /**
     * @return array<string, mixed>
     */
    protected function getData(): array
    {
        $data = $this->model->getAttributes();
        if (method_exists($this->model, 'validationData')) {
            return $this->model->validationData($data);
        }

        return $data;
    }
}
