<?php

namespace Application\DTO;

class RespostaServico
{
    private bool $sucesso;
    private string $mensagem;
    private array $dados;
    
    private function __construct(bool $sucesso, string $mensagem, array $dados = [])
    {
        $this->sucesso = $sucesso;
        $this->mensagem = $mensagem;
        $this->dados = $dados;
    }
    
    public static function sucesso(string $mensagem, array $dados = []): self
    {
        return new self(true, $mensagem, $dados);
    }
    
    public static function erro(string $mensagem): self
    {
        return new self(false, $mensagem);
    }
    
    public function toArray(): array
    {
        $resposta = [
            'sucesso' => $this->sucesso,
            'mensagem' => $this->mensagem,
        ];
        
        return array_merge($resposta, $this->dados);
    }
    
    public function ehSucesso(): bool
    {
        return $this->sucesso;
    }
}
