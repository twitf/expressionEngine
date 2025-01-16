<?php

namespace Twitf\ExpressionEngine\Enum;

enum EnumDfaState: int
{
    const S_RESET             = 0;   // 初始状态
    const S_OPERATOR          = 1;   // 运算符状态
    const S_DOUBLE_OPERATOR   = 2;   // 双字符运算符状态
    const S_SYMBOL            = 3;   // 符号状态
    const S_WHITESPACE        = 4;   // 空格状态
    const S_LINEFEED          = 5;   // 换行状态
    const S_IDENTIFIER        = 6;   // 标识符状态
    const S_NUMBER            = 7;   // 整数状态
    const S_FLOAT             = 8;   // 浮点数状态
    const S_STRING            = 9;   // 字符串状态
    const S_SINGLE_COMMENT    = 11;    // 单行注释状态
    const S_MULTI_COMMENT     = 12;     // 多行注释状态
}