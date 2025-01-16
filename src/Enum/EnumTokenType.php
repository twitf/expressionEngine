<?php

namespace Twitf\ExpressionEngine\Enum;

enum EnumTokenType: string
{
    const OPERATOR        = 'Operator';        // 单字符运算符
    const DOUBLE_OPERATOR = 'DoubleOperator';  // 双字符运算符
    const SYMBOL          = 'Symbol';          // 符号
    const WHITESPACE      = 'Whitespace';      // 空格
    const LINE_FEED       = 'LineFeed';        // 换行
    const KEYWORD         = 'Keyword';         // 关键字
    const IDENTIFIER      = 'Identifier';      // 标识符
    const NUMBER          = 'Number';          // 整数
    const FLOAT           = 'Float';           // 浮点数
    const STRING          = 'String';          // 字符串
    const UNKNOWN         = 'Unknown';         // 未知
}