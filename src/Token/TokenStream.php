<?php

namespace Twitf\ExpressionEngine\Token;

class TokenStream
{
    private array $tokens;
    private int $position = 0;

    public function __construct(array $tokens)
    {
        $this->tokens = $tokens;
    }

    public function next(): ?Token
    {
        if ($this->position >= count($this->tokens)) {
            return null;
        }
        return $this->tokens[$this->position++];
    }

    public function peek(int $ahead = 0): ?Token
    {
        $position = $this->position + $ahead;
        if ($position >= count($this->tokens)) {
            return null;
        }
        return $this->tokens[$position];
    }

    public function eof(): bool
    {
        return $this->position >= count($this->tokens);
    }

    public function getCurrentLine(): int
    {
        if ($this->position >= count($this->tokens)) {
            return $this->tokens[count($this->tokens) - 1]->line ?? 0;
        }
        return $this->tokens[$this->position]->line ?? 0;
    }

    public function reset(): void
    {
        $this->position = 0;
    }

    public function expect(string $type, string $value = null): ?Token
    {
        $token = $this->peek();
        if (!$token || $token->type !== $type || ($value !== null && $token->value !== $value)) {
            return null;
        }
        return $this->next();
    }

    public function match(string $type, string $value = null): bool
    {
        return $this->expect($type, $value) !== null;
    }
}