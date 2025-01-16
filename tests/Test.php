<?php

namespace Tests;

use PHPUnit\Framework\TestCase;
use Twitf\ExpressionEngine\Lexer\Lexer;
use Twitf\ExpressionEngine\Parser\Context\Context;
use Twitf\ExpressionEngine\Parser\Parser;
use Twitf\ExpressionEngine\Token\TokenStream;

class Test extends TestCase
{
    private Parser  $parser;
    private Context $context;

    protected function setUp(): void
    {
        $this->parser  = new Parser();
        $this->context = new Context();

        // 注册测试函数
        $this->context->registerFunction('max', function (array $args) {
            return max($args);
        });
    }

    /**
     * @dataProvider expressionProvider
     */
    public function testExpressions(string $code, $expectedResult): void
    {
        $this->context->setVariable('任职记录', ['工作地' => '西安1']);
        $lexer  = new Lexer($code);
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

        $ast    = $this->parser->parse(new TokenStream($tokens));
        $result = $ast->evaluate($this->context);
        $this->assertEquals($expectedResult, $result);
    }

    public static function expressionProvider(): array
    {
        return [
            '复杂条件分支' => [
                <<<CODE
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
                5  // 期望结果：c的值是5
            ],
        ];
    }
}