<?php
class Usuario
{
    public $id;
    public $nome;
    public $senha;
    public $data_nasc;
    public $email;
    public $foto_perfil;
    public $tel;
    public $cpf;
    public $ativo;

    public function popo($dadosUser)
    {
        //Popular o objeto usuario ($dadosUser para $user)
        $this->id = $dadosUser->id; //"id": null,
        $this->nome = $dadosUser->nome;   // "nome": "Silvia Cristina",
        $this->email = $dadosUser->email; // "email": "sc@email.com",
        $this->senha = $dadosUser->senha; // "senha": "123@123",
        $this->data_nasc = $dadosUser->data_nasc; // "data_nasc": "12/05/2010",
        $this->foto_perfil = $dadosUser->foto_perfil; // "foto_perfil": "",
        $this->tel = $dadosUser->tel;   // "tel": "5555-666666",
        $this->cpf = $dadosUser->cpf;   // "cpf": "12312312344",
        $this->ativo = $dadosUser->ativo; // "ativo": 1            
    }
}
