<?php

namespace Tests\Lexer;

use PHPUnit\Framework\TestCase;
use Twitf\ExpressionEngine\Enum\EnumTokenType;
use Twitf\ExpressionEngine\Lexer\Exception\LexerException;
use Twitf\ExpressionEngine\Lexer\Lexer;
use Twitf\ExpressionEngine\Token\Token;

class LexerTest extends TestCase
{
    public function testTokenize(): void
    {
        $cases = [
            // 测试基本赋值和运算符
            [
                'input' => 'a = 1 + 2 * 3 / 4;',
                'expected' => [
                    [EnumTokenType::IDENTIFIER, 'a'],
                    [EnumTokenType::OPERATOR, '='],
                    [EnumTokenType::NUMBER, '1'],
                    [EnumTokenType::OPERATOR, '+'],
                    [EnumTokenType::NUMBER, '2'],
                    [EnumTokenType::OPERATOR, '*'],
                    [EnumTokenType::NUMBER, '3'],
                    [EnumTokenType::OPERATOR, '/'],
                    [EnumTokenType::NUMBER, '4'],
                    [EnumTokenType::SYMBOL, ';'],
                ]
            ],
            // 测试各种比较运算符
            [
                'input' => 'a > b && c < d || e >= f && g <= h;',
                'expected' => [
                    [EnumTokenType::IDENTIFIER, 'a'],
                    [EnumTokenType::OPERATOR, '>'],
                    [EnumTokenType::IDENTIFIER, 'b'],
                    [EnumTokenType::DOUBLE_OPERATOR, '&&'],
                    [EnumTokenType::IDENTIFIER, 'c'],
                    [EnumTokenType::OPERATOR, '<'],
                    [EnumTokenType::IDENTIFIER, 'd'],
                    [EnumTokenType::DOUBLE_OPERATOR, '||'],
                    [EnumTokenType::IDENTIFIER, 'e'],
                    [EnumTokenType::DOUBLE_OPERATOR, '>='],
                    [EnumTokenType::IDENTIFIER, 'f'],
                    [EnumTokenType::DOUBLE_OPERATOR, '&&'],
                    [EnumTokenType::IDENTIFIER, 'g'],
                    [EnumTokenType::DOUBLE_OPERATOR, '<='],
                    [EnumTokenType::IDENTIFIER, 'h'],
                    [EnumTokenType::SYMBOL, ';'],
                ]
            ],
            // 测试字符串和转义字符
            [
                'input' => 'str = "Hello\\\"World";',
                'expected' => [
                    [EnumTokenType::IDENTIFIER, 'str'],
                    [EnumTokenType::OPERATOR, '='],
                    [EnumTokenType::STRING, '"Hello\\"World"'],
                    [EnumTokenType::SYMBOL, ';'],
                ]
            ],
            // 测试中文标识符和注释
            [
                'input' => <<<'CODE'
                // 这是中文注释
                变量名 = "中文值"; /* 这也是注释 */
                CODE,
                'expected' => [
                    [EnumTokenType::IDENTIFIER, '变量名'],
                    [EnumTokenType::OPERATOR, '='],
                    [EnumTokenType::STRING, '"中文值"'],
                    [EnumTokenType::SYMBOL, ';'],
                ]
            ],
            // 综合性表达式测试
            [
                'input' => <<<'CODE'
                //这是单行注释
                c = true?5:6;
                d = false;
                a = "西安";
                b = "广东";
                /*
                    这是多行注释
                */
                if((任职记录.工作地==a && a!="") || c || d){
                    return c;
                }else if(任职记录.工作地==b){
                    return 30;
                }else{
                    return max(10,20);
                }
                CODE,
                'expected' => [
                    // c = true?5:6;
                    [EnumTokenType::IDENTIFIER, 'c'],
                    [EnumTokenType::OPERATOR, '='],
                    [EnumTokenType::KEYWORD, 'true'],
                    [EnumTokenType::SYMBOL, '?'],
                    [EnumTokenType::NUMBER, '5'],
                    [EnumTokenType::SYMBOL, ':'],
                    [EnumTokenType::NUMBER, '6'],
                    [EnumTokenType::SYMBOL, ';'],
                    
                    // d = false;
                    [EnumTokenType::IDENTIFIER, 'd'],
                    [EnumTokenType::OPERATOR, '='],
                    [EnumTokenType::KEYWORD, 'false'],
                    [EnumTokenType::SYMBOL, ';'],
                    
                    // a = "西安";
                    [EnumTokenType::IDENTIFIER, 'a'],
                    [EnumTokenType::OPERATOR, '='],
                    [EnumTokenType::STRING, '"西安"'],
                    [EnumTokenType::SYMBOL, ';'],
                    
                    // b = "广东";
                    [EnumTokenType::IDENTIFIER, 'b'],
                    [EnumTokenType::OPERATOR, '='],
                    [EnumTokenType::STRING, '"广东"'],
                    [EnumTokenType::SYMBOL, ';'],
                    
                    // if((任职记录.工作地==a && a!="") || c || d){
                    [EnumTokenType::KEYWORD, 'if'],
                    [EnumTokenType::SYMBOL, '('],
                    [EnumTokenType::SYMBOL, '('],
                    [EnumTokenType::IDENTIFIER, '任职记录'],
                    [EnumTokenType::SYMBOL, '.'],
                    [EnumTokenType::IDENTIFIER, '工作地'],
                    [EnumTokenType::DOUBLE_OPERATOR, '=='],
                    [EnumTokenType::IDENTIFIER, 'a'],
                    [EnumTokenType::DOUBLE_OPERATOR, '&&'],
                    [EnumTokenType::IDENTIFIER, 'a'],
                    [EnumTokenType::DOUBLE_OPERATOR, '!='],
                    [EnumTokenType::STRING, '""'],
                    [EnumTokenType::SYMBOL, ')'],
                    [EnumTokenType::DOUBLE_OPERATOR, '||'],
                    [EnumTokenType::IDENTIFIER, 'c'],
                    [EnumTokenType::DOUBLE_OPERATOR, '||'],
                    [EnumTokenType::IDENTIFIER, 'd'],
                    [EnumTokenType::SYMBOL, ')'],
                    [EnumTokenType::SYMBOL, '{'],
                    
                    // return c;
                    [EnumTokenType::KEYWORD, 'return'],
                    [EnumTokenType::IDENTIFIER, 'c'],
                    [EnumTokenType::SYMBOL, ';'],
                    
                    // }else if(任职记录.工作地==b){
                    [EnumTokenType::SYMBOL, '}'],
                    [EnumTokenType::KEYWORD, 'else'],
                    [EnumTokenType::KEYWORD, 'if'],
                    [EnumTokenType::SYMBOL, '('],
                    [EnumTokenType::IDENTIFIER, '任职记录'],
                    [EnumTokenType::SYMBOL, '.'],
                    [EnumTokenType::IDENTIFIER, '工作地'],
                    [EnumTokenType::DOUBLE_OPERATOR, '=='],
                    [EnumTokenType::IDENTIFIER, 'b'],
                    [EnumTokenType::SYMBOL, ')'],
                    [EnumTokenType::SYMBOL, '{'],
                    
                    // return 30;
                    [EnumTokenType::KEYWORD, 'return'],
                    [EnumTokenType::NUMBER, '30'],
                    [EnumTokenType::SYMBOL, ';'],
                    
                    // }else{
                    [EnumTokenType::SYMBOL, '}'],
                    [EnumTokenType::KEYWORD, 'else'],
                    [EnumTokenType::SYMBOL, '{'],
                    
                    // return max(10,20);
                    [EnumTokenType::KEYWORD, 'return'],
                    [EnumTokenType::IDENTIFIER, 'max'],
                    [EnumTokenType::SYMBOL, '('],
                    [EnumTokenType::NUMBER, '10'],
                    [EnumTokenType::SYMBOL, ','],
                    [EnumTokenType::NUMBER, '20'],
                    [EnumTokenType::SYMBOL, ')'],
                    [EnumTokenType::SYMBOL, ';'],
                    
                    // }
                    [EnumTokenType::SYMBOL, '}'],
                ]
            ],
            // 添加对 !== 运算符的测试
            [
                'input' => 'if (a !== "") { return true; }',
                'expected' => [
                    [EnumTokenType::KEYWORD, 'if'],
                    [EnumTokenType::SYMBOL, '('],
                    [EnumTokenType::IDENTIFIER, 'a'],
                    [EnumTokenType::DOUBLE_OPERATOR, '!=='],  // 测试严格不等于
                    [EnumTokenType::STRING, '""'],
                    [EnumTokenType::SYMBOL, ')'],
                    [EnumTokenType::SYMBOL, '{'],
                    [EnumTokenType::KEYWORD, 'return'],
                    [EnumTokenType::KEYWORD, 'true'],
                    [EnumTokenType::SYMBOL, ';'],
                    [EnumTokenType::SYMBOL, '}'],
                ]
            ],
            // 测试所有比较运算符
            [
                'input' => 'a == b && c === d && e != f && g !== h',
                'expected' => [
                    [EnumTokenType::IDENTIFIER, 'a'],
                    [EnumTokenType::DOUBLE_OPERATOR, '=='],
                    [EnumTokenType::IDENTIFIER, 'b'],
                    [EnumTokenType::DOUBLE_OPERATOR, '&&'],
                    [EnumTokenType::IDENTIFIER, 'c'],
                    [EnumTokenType::DOUBLE_OPERATOR, '==='],
                    [EnumTokenType::IDENTIFIER, 'd'],
                    [EnumTokenType::DOUBLE_OPERATOR, '&&'],
                    [EnumTokenType::IDENTIFIER, 'e'],
                    [EnumTokenType::DOUBLE_OPERATOR, '!='],
                    [EnumTokenType::IDENTIFIER, 'f'],
                    [EnumTokenType::DOUBLE_OPERATOR, '&&'],
                    [EnumTokenType::IDENTIFIER, 'g'],
                    [EnumTokenType::DOUBLE_OPERATOR, '!=='],
                    [EnumTokenType::IDENTIFIER, 'h'],
                ]
            ],
                        // 添加对 !== 运算符的测试
                        [
                            'input' => 'if (a !== "") { return true; }',
                            'expected' => [
                                [EnumTokenType::KEYWORD, 'if'],
                                [EnumTokenType::SYMBOL, '('],
                                [EnumTokenType::IDENTIFIER, 'a'],
                                [EnumTokenType::DOUBLE_OPERATOR, '!=='],  // 测试严格不等于
                                [EnumTokenType::STRING, '""'],
                                [EnumTokenType::SYMBOL, ')'],
                                [EnumTokenType::SYMBOL, '{'],
                                [EnumTokenType::KEYWORD, 'return'],
                                [EnumTokenType::KEYWORD, 'true'],
                                [EnumTokenType::SYMBOL, ';'],
                                [EnumTokenType::SYMBOL, '}'],
                            ]
                        ],
                        // 测试所有比较运算符
                        [
                            'input' => 'a == b && c === d && e != f && g !== h',
                            'expected' => [
                                [EnumTokenType::IDENTIFIER, 'a'],
                                [EnumTokenType::DOUBLE_OPERATOR, '=='],
                                [EnumTokenType::IDENTIFIER, 'b'],
                                [EnumTokenType::DOUBLE_OPERATOR, '&&'],
                                [EnumTokenType::IDENTIFIER, 'c'],
                                [EnumTokenType::DOUBLE_OPERATOR, '==='],
                                [EnumTokenType::IDENTIFIER, 'd'],
                                [EnumTokenType::DOUBLE_OPERATOR, '&&'],
                                [EnumTokenType::IDENTIFIER, 'e'],
                                [EnumTokenType::DOUBLE_OPERATOR, '!='],
                                [EnumTokenType::IDENTIFIER, 'f'],
                                [EnumTokenType::DOUBLE_OPERATOR, '&&'],
                                [EnumTokenType::IDENTIFIER, 'g'],
                                [EnumTokenType::DOUBLE_OPERATOR, '!=='],
                                [EnumTokenType::IDENTIFIER, 'h'],
                            ]
                        ],
        ];

        foreach ($cases as $i => $case) {
            $lexer = new Lexer($case['input']);
            $tokens = $lexer->tokenize();

            // 添加调试输出
            echo "\nTokens:\n";
            foreach ($tokens as $token) {
                echo sprintf(
                    "Type: %-12s Value: %-12s Line: %d\n",
                    $token->type,
                    $token->value,
                    $token->line
                );
            }
            
            try {
                $this->assertTokenSequence(
                    $tokens,
                    $case['expected'],
                    "测试用例 #{$i} 失败: {$case['input']}"
                );
            } catch (\Exception $e) {
                $this->fail(sprintf(
                    "测试用例 #%d 失败:\n输入: %s\n错误: %s",
                    $i,
                    $case['input'],
                    $e->getMessage()
                ));
            }
        }
    }

    /**
     * 辅助方法：验证token序列
     * @param Token[] $actual 实际的token序列
     * @param array $expected 期望的token序列 [[type, value], ...]
     */
    private function assertTokenSequence(array $actual, array $expected): void
    {
        $actualCount = count($actual);
        $expectedCount = count($expected);
        
        if ($actualCount !== $expectedCount) {
            $this->fail(sprintf(
                "Token数量不匹配: 期望 %d 个，实际 %d 个",
                $expectedCount,
                $actualCount
            ));
        }

        foreach ($actual as $i => $token) {
            $this->assertEquals(
                $expected[$i][0],
                $token->type,
                sprintf("第%d个Token类型不匹配", $i)
            );
            $this->assertEquals(
                $expected[$i][1],
                $token->value,
                sprintf("第%d个Token值不匹配", $i)
            );
        }
    }

    /**
     * 测试非法字符
     */
    public function testInvalidChar(): void
    {
        $invalidInputs = [
            'a @ b',     // 非法字符 @
            'x # y',     // 非法字符 #
            'foo $ bar', // 非法字符 $
        ];
        
        foreach ($invalidInputs as $input) {
            $this->expectException(LexerException::class);
            $this->expectExceptionMessage('非法字符');
            $lexer = new Lexer($input);
            $lexer->tokenize();
        }
    }

    /**
     * 测试未闭合的字符串
     */
    public function testUnclosedString(): void
    {
        $unclosedStrings = [
            'a = "未闭合',
            'str = "包含\n换行',
            'msg = "有转义但未闭合\\"',
        ];
        
        foreach ($unclosedStrings as $input) {
            $this->expectException(LexerException::class);
            $this->expectExceptionMessage('未闭合的字符串');
            $lexer = new Lexer($input);
            $lexer->tokenize();
        }
    }

    /**
     * 测试未闭合的多行注释
     */
    public function testUnclosedMultiComment(): void
    {
        $this->expectException(LexerException::class);
        $this->expectExceptionMessage('未闭合的多行注释');

        $lexer = new Lexer('/* 未闭合的注释');
        $lexer->tokenize();
    }

    /**
     * 测试嵌套结构
     */
    public function testNestedStructures(): void
    {
        $input = <<<'CODE'
        if(a && (b || c)){
            if(d){
                return e;
            }
        }
        CODE;
        
        $lexer = new Lexer($input);
        $tokens = $lexer->tokenize();
        
        // 验证括号匹配和嵌套结构
        $this->assertGreaterThan(0, count($tokens));
        $this->assertContainsTokenSequence($tokens, [
            [EnumTokenType::KEYWORD, 'if'],
            [EnumTokenType::SYMBOL, '('],
        ]);
    }

    /**
     * 辅助方法：验证token序列中包含指定的子序列
     */
    private function assertContainsTokenSequence(array $tokens, array $sequence): void
    {
        $tokenValues = array_map(function($token) {
            return [$token->type, $token->value];
        }, $tokens);
        
        $found = false;
        for ($i = 0; $i <= count($tokenValues) - count($sequence); $i++) {
            if (array_slice($tokenValues, $i, count($sequence)) === $sequence) {
                $found = true;
                break;
            }
        }
        
        $this->assertTrue($found, '未找到预期的Token序列');
    }
}