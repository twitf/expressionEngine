<?php

namespace Twitf\ExpressionEngine\Enum;

enum EnumToken: string
{
    // 单字符运算符
    const PLUS     = '+';
    const MINUS    = '-';
    const MULTIPLY = '*';
    const DIVIDE   = '/';
    const MODULO   = '%';
    const GT       = '>';
    const LT       = '<';
    const ASSIGN   = '=';
    const NOT      = '!';
    const BIT_AND  = '&';
    const BIT_OR   = '|';
    const BIT_NOT = '~';
    const BIT_XOR = '^';

    // 双字符运算符
    const GE  = ">=";
    const LE  = "<=";
    const EQ  = "==";
    const SEQ = "===";
    const NE  = "!=";
    const NEQ = "!==";
    const AND = "&&";
    const OR  = "||";

    // 符号
    const LPAREN    = '(';
    const RPAREN    = ')';
    const LBRACE    = '{';
    const RBRACE    = '}';
    const COMMA     = ',';
    const SEMICOLON = ';';
    const QUESTION  = '?';
    const COLON     = ':';
    const DOT       = '.';

    // 关键字
    const IF        = 'if';
    const ELSEIF    = 'elseif';
    const ELSE      = 'else';
    const RETURN    = 'return';
    const TRUE      = 'true';
    const FALSE     = 'false';
    const NULL      = 'null';

    // 空白字符
    const SPACE     = ' ';
    const TAB       = "\t";
    const FORM_FEED = "\f";

    // 换行符
    const LF = "\n";
    const CR = "\r";

    public static function getOperators(): array
    {
        return [
            EnumToken::PLUS,     // '+'
            EnumToken::MINUS,    // '-'
            EnumToken::MULTIPLY, // '*'
            EnumToken::DIVIDE,   // '/'
            EnumToken::MODULO,   // '%'
            EnumToken::GT,       // '>'
            EnumToken::LT,       // '<'
            EnumToken::ASSIGN,   // '='
            EnumToken::NOT,      // '!'
            EnumToken::BIT_AND,  // '&'
            EnumToken::BIT_OR,   // '|'
            EnumToken::BIT_NOT,   // '~'
            EnumToken::BIT_XOR,   // '^'
        ];
    }

    public static function getDoubleOperators(): array
    {
        return [
            EnumToken::GE,   // '>='
            EnumToken::LE,   // '<='
            EnumToken::EQ,   // '=='
            EnumToken::SEQ,  // '==='
            EnumToken::NE,   // '!='
            EnumToken::NEQ,  // '!=='
            EnumToken::AND,  // '&&'
            EnumToken::OR,   // '||'
        ];
    }

    public static function getSymbols(): array
    {
        return [
            EnumToken::LPAREN,    // '('
            EnumToken::RPAREN,    // ')'
            EnumToken::LBRACE,    // '{'
            EnumToken::RBRACE,    // '}'
            EnumToken::COMMA,     // ','
            EnumToken::SEMICOLON, // ';'
            EnumToken::QUESTION,  // '?'
            EnumToken::COLON,     // ':'
            EnumToken::DOT,       // '.'
        ];
    }

    public static function getKeywords(): array
    {
        return [
            EnumToken::IF,     // 'if'
            EnumToken::ELSEIF, // 'elseif'
            EnumToken::ELSE,   // 'else'
            EnumToken::RETURN, // 'return'
            EnumToken::TRUE,   // 'true'
            EnumToken::FALSE,  // 'false'
            EnumToken::NULL,   // 'null'
        ];
    }

    /**
     * 获取空白字符集合
     */
    public static function getWhitespace(): array
    {
        return [
            EnumToken::SPACE,      // 空格
            EnumToken::TAB,        // 制表符
            EnumToken::FORM_FEED   // 换页
        ];
    }

    /**
     * 获取换行符集合
     */
    public static function getLineFeed(): array
    {
        return [
            EnumToken::LF,   // 换行 \n
            EnumToken::CR    // 回车 \r
        ];
    }
}