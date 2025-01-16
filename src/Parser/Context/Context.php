<?php
// src/Parser/Context/Context.php

declare(strict_types=1);

namespace Twitf\ExpressionEngine\Parser\Context;

use Twitf\ExpressionEngine\Parser\Exception\ParserException;

class Context
{
    private array $variables = [];
    private array $functions = [];

    /**
     * 注册函数
     */
    public function registerFunction(string $name, callable $function): void
    {
        $this->functions[$name] = $function;
    }

    /**
     * 调用函数
     */
    public function callFunction(string $name, array $args): mixed
    {
        if (!isset($this->functions[$name])) {
            throw new \Exception("Undefined function: $name");
        }
        return ($this->functions[$name])($args);
    }

    /**
     * 设置变量
     */
    public function setVariable(string $name, mixed $value): void
    {
        $this->variables[$name] = $value;
    }

    /**
     * 获取变量值
     *
     * @param string $name 变量名
     * @return mixed 变量值
     * @throws \Exception 当变量不存在且非静默模式时
     */
    public function getVariable(string $name): mixed
    {
        if (!isset($this->variables[$name])) {
            throw new \Exception("Undefined variable: $name");
        }
        return $this->variables[$name];
    }

    /**
     * 检查变量是否存在
     */
    public function hasVariable(string $name): bool
    {
        return isset($this->variables[$name]);
    }

    /**
     * 检查函数是否存在
     */
    public function hasFunction(string $name): bool
    {
        return isset($this->functions[$name]);
    }
}