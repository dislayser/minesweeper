<?php

declare(strict_types=1);

namespace Game\Kernel;

class RunAction
{
    /**
     * Summary of __construct
     * @param string|object $action App\Some\Action | App\Some\Action::someMethod | new Action()
     * @param string $method Метод для выполнения
     * @param array $args Аргуметы которые передадутся в конструктор new App\Some\Action(...$args)
     */
    public function __construct(
        private string|object $action,
        private string $method,
        private array $args = [],
    ) {
    }

    /**
     * Выполняет действие.
     *
     * @return mixed Результат выполнения действия.
     * @throws \InvalidArgumentException Если переданы некорректные параметры.
     */
    public function run(): mixed
    {
        // Если $action — это строка, обрабатываем её как класс или статический метод
        if (is_string($this->action)) {
            // Проверяем, является ли строка вызовом статического метода (содержит '::')
            if (str_contains($this->action, '::')) {
                [$class, $staticMethod] = explode('::', $this->action, 2);

                // Проверяем, существует ли класс и метод
                if (!class_exists($class)) {
                    throw new \InvalidArgumentException("Класс '{$class}' не существует.");
                }
                if (!method_exists($class, $staticMethod)) {
                    throw new \InvalidArgumentException("Метод '{$staticMethod}' не существует в классе '{$class}'.");
                }

                // Вызываем статический метод с аргументами
                return call_user_func_array([$class, $staticMethod], $this->args);
            }

            // Если это просто класс, создаём экземпляр и вызываем метод
            if (!class_exists($this->action)) {
                throw new \InvalidArgumentException("Класс '{$this->action}' не существует.");
            }

            // Создаём экземпляр класса с передачей аргументов в конструктор
            $instance = new ($this->action)(...$this->args);

            // Проверяем, существует ли метод
            if (!method_exists($instance, $this->method)) {
                throw new \InvalidArgumentException("Метод '{$this->method}' не существует в классе '{$this->action}'.");
            }

            // Вызываем метод у созданного экземпляра
            return call_user_func_array([$instance, $this->method], $this->args);
        }

        // Если $action — это объект, вызываем указанный метод
        if (is_object($this->action)) {
            // Проверяем, существует ли метод
            if (!method_exists($this->action, $this->method)) {
                throw new \InvalidArgumentException("Method '{$this->method}' does not exist in the provided object.");
            }

            // Вызываем метод у объекта
            return call_user_func_array([$this->action, $this->method], $this->args);
        }

        // Если тип $action неизвестен, выбрасываем исключение
        throw new \InvalidArgumentException("Invalid type for 'action'. Expected string or object.");
    }
}