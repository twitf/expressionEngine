<?php

declare(strict_types=1);

namespace Twitf\ExpressionEngine\Parser\Operator;

class OperatorPrecedence
{
    // 运算符优先级定义
    private const PRECEDENCE = [
        '='  => 1,    // 赋值运算符最低优先级
        '||' => 3,    // 逻辑或
        '&&' => 4,    // 逻辑与
        '|'  => 5,    // 位或
        '^'  => 6,    // 位异或
        '&'  => 7,    // 位与
        '==' => 8,    // 相等
        '!=' => 8,
        '>'  => 9,    // 比较
        '>=' => 9,
        '<'  => 9,
        '<=' => 9,
        '<<' => 10,   // 位移
        '>>' => 10,
        '+'  => 11,   // 加减
        '-'  => 11,
        '*'  => 12,   // 乘除
        '/'  => 12,
        '%'  => 12,
        '!'  => 13,   // 一元运算符
        '~'  => 13,
        '?'  => 2,    // 三元运算符 (优先级在赋值之上，其他运算符之下)
        ':'  => 2,    // 三元运算符的冒号部分应该和问号有相同优先级
    ];

    /**
     * 获取运算符优先级
     * @param string $operator 运算符
     * @return int 优先级(数字越大优先级越高)
     */
    public static function getPrecedence(string $operator): int
    {
        return self::PRECEDENCE[$operator] ?? 0;
    }

    /**
     * 比较两个运算符的优先级
     * @param string $op1 运算符1
     * @param string $op2 运算符2
     * @return int 如果op1优先级高于op2返回1，相等返回0，低于返回-1
     */
    public static function compare(string $op1, string $op2): int
    {
        $p1 = self::getPrecedence($op1);
        $p2 = self::getPrecedence($op2);

        return $p1 <=> $p2;
    }
}