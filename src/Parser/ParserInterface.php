<?php

declare(strict_types=1);

namespace Twitf\ExpressionEngine\Parser;

use Twitf\ExpressionEngine\Parser\AST\NodeInterface;
use Twitf\ExpressionEngine\Token\TokenStream;

interface ParserInterface
{
    /**
     * 解析token流，生成AST
     * @param TokenStream $tokens Token流
     * @return NodeInterface AST根节点
     */
    public function parse(TokenStream $tokens): NodeInterface;
}