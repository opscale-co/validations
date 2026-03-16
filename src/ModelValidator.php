<?php

namespace Opscale\Validations;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;

/**
 * Class ModelValidator.
 */
class ModelValidator
{
    /**
     * The model object to validate.
     */
    protected Model $model;

    /**
     * The data to be validated.
     */
    protected array $data;

    /**
     * The rules to be applied to the data.
     */
    protected array $rules;

    /**
     * The array of custom error messages.
     */
    protected array $customMessages;

    /**
     * The array of custom attribute names.
     */
    protected array $customAttributes;

    /**
     * ModelValidator constructor.
     */
    public function __construct(Model $model)
    {
        $this->model = $model;
        $this->initialize();
    }

    /**
     * Initialize this class by setting up the needed params.
     *
     * @return $this
     */
    public function initialize()
    {
        $this->customMessages = $this->getMessages();
        $this->customAttributes = $this->getAttributes();
        $this->rules = $this->getRules();
        $this->data = $this->getData();

        return $this;
    }

    /**
     * Validate the model params.
     *
     * @return $this
     */
    public function validate()
    {
        if ($this->rules) {
            Validator::make($this->data, $this->rules)
                ->setCustomMessages($this->customMessages)
                ->addCustomAttributes($this->customAttributes)
                ->validate();
        }

        return $this;
    }

    /**
     * Get the validation messages.
     *
     * @return array
     */
    protected function getMessages()
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
     * Get the validation attributes.
     *
     * @return array
     */
    protected function getAttributes()
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
     * Get the validation rules resolved for the current operation.
     *
     * Rules can be defined as:
     *   - A string: applies to both create and update.
     *   - An array with 'create' and/or 'update' keys: applies conditionally.
     *
     * @return array
     */
    protected function getRules()
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
     * Get the validation data.
     *
     * @return array
     */
    protected function getData()
    {
        $data = $this->model->getAttributes();
        if (method_exists($this->model, 'validationData')) {
            return $this->model->validationData($data);
        }

        return $data;
    }
}
