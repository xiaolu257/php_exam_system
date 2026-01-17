<?php

namespace App\Listener;

use Hyperf\Event\Annotation\Listener;
use Hyperf\Event\Contract\ListenerInterface;
use Hyperf\Validation\Contract\ValidatorFactoryInterface;
use Hyperf\Validation\Event\ValidatorFactoryResolved;
use Hyperf\Validation\Validator;

#[Listener]
class ValidatorFactoryResolvedListener implements ListenerInterface
{

    public function listen(): array
    {
        return [
            ValidatorFactoryResolved::class,
        ];
    }

    public function process(object $event): void
    {
        /**  @var ValidatorFactoryInterface $validatorFactory */
        $validatorFactory = $event->validatorFactory;
        $validatorFactory->extend('index_in', function (string $attribute, mixed $value, array $parameters, Validator $validator): bool {
            $data = $validator->getData();
            $optionsField = $parameters[0];
            $options = $data[$optionsField] ?? null;

            if (!is_array($options)) {
                return false;
            }

            return is_int($value) && array_key_exists($value, $options);
        }, ':attribute 必须是 :field 中的一个有效下标');
        $validatorFactory->replacer('index_in', function (string $message, string $attribute, string $rule, array $parameters, Validator $validator): array|string {
            $optionsField = $parameters[0];
            // 使用 validator 的 getDisplayableAttribute 方法获取映射后的属性名
            $fieldName = $validator->getDisplayableAttribute($optionsField);
            return str_replace(':field', $fieldName, $message);
        });
    }
}
