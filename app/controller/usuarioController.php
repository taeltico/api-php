<?php

class UsuarioController
{
    public function getAll($ativo = 1)
    {
        try {
            $dao = new DAO;
            $sql = "SELECT * from usuario where ativo = :ativo";
            $conn = $dao->conecta();
            $stman = $conn->prepare($sql);
            //$stman = $dao->conecta()->prepare($sql);
            $stman->bindParam(":ativo", $ativo);
            $stman->execute();
            $result = $stman->fetchAll();
            return $result;
        } catch (Exception $e) {
            throw new Exception("Erro ao listar os usuarios: " . $e->getMessage());
        }
    }

    public function get($id)
    {
        try {
            $sql = "SELECT * from usuario where id = :id and ativo <> 0";
            $dao = new DAO;
            $stman = $dao->conecta()->prepare($sql);
            $stman->bindParam(":id", $id);
            $stman->execute();
            $result = $stman->fetchALL();
            return $result;
        } catch (Exception $e) {
            throw new Exception("Erro ao pegar o usuario: " . $e->getMessage());
        }
    }

    public function add(Usuario $user)
    {
        try {
            $sql = "INSERT INTO usuario 
                    (id, nome, senha, data_nasc, email, foto_perfil, tel, cpf, ativo) 
                    VALUES
                    (null, :nome, md5(:senha), :data_nasc, :email, :foto_perfil, :tel, :cpf, :ativo)";
            //$senhaCryp = md5($user->senha);
            $senhaCryp = crypt($user->senha, '$5$rounds=5000$' . $user->email . '$');
            $dataBanco = $this->formatDateBD($user->data_nasc);

            $dao = new DAO;
            $stman = $dao->conecta()->prepare($sql);
            $stman->bindParam(":nome", $user->nome);
            $stman->bindParam(":senha", $senhaCryp);
            $stman->bindParam(":data_nasc", $dataBanco);
            $stman->bindParam(":email", $user->email);
            $stman->bindParam(":foto_perfil", $user->foto_perfil);
            $stman->bindParam(":tel", $user->tel);
            $stman->bindParam(":cpf", $user->cpf);
            $stman->bindParam(":ativo", $user->ativo);
            return $stman->execute();
        } catch (Exception $e) {
            throw new Exception("Erro ao cadastra o usuario: " . $e->getMessage());
        }
    }

    public function update(Usuario $user)
    {
        try {
            $sql = "UPDATE  usuario 
                    SET nome = :nome,
                    senha = md5(:senha),
                    data_nasc = :data_nasc,
                    email = :email,
                    foto_perfil = :foto_perfil,
                    tel = :tel,
                    cpf = :cpf, 
                    ativo = :ativo
                    WHERE usuario.id = :id";
            //$senhaCryp = md5($user->senha);
            $senhaCryp = crypt($user->senha, '$5$rounds=5000$' . $user->email . '$');
            $dataBanco = $this->formatDateBD($user->data_nasc);

            $dao = new DAO;
            $stman = $dao->conecta()->prepare($sql);
            $stman->bindParam(":id", $user->id);
            $stman->bindParam(":nome", $user->nome);
            $stman->bindParam(":senha", $senhaCryp);
            $stman->bindParam(":data_nasc", $dataBanco);
            $stman->bindParam(":email", $user->email);
            $stman->bindParam(":foto_perfil", $user->foto_perfil);
            $stman->bindParam(":tel", $user->tel);
            $stman->bindParam(":cpf", $user->cpf);
            $stman->bindParam(":ativo", $user->ativo);
            return $stman->execute();
        } catch (Exception $e) {
            throw new Exception("Erro ao atualizado o usuario: " . $e->getMessage());
        }
    }


    public function delete($id)
    {
        try {
            //$sql = "DELETE FROM usuario WHERE id = :id";
            $sql = "UPDATE usuario Set ativo = 0 Where id = :id";
            $dao = new DAO;
            $stman = $dao->conecta()->prepare($sql);
            $stman->bindParam(":id", $id);
            return $stman->execute();
        } catch (PDOException $pe) {
            throw new Exception("Erro ao apagar o usuario: " . $pe->getMessage());
        } catch (Exception $e) {
            throw new Exception("Erro ao acessar a base de dados: " . $e->getMessage());
        }
    }


    public function logon($usuario, $pass)
    {
        try {
            $sql = "SELECT id, nome, email, foto_perfil 
                From usuario 
                Where email = :email and senha = md5(:senha)";
            $senhaCryp = crypt($pass, '$5$rounds=5000$' . $usuario . '$');
            $dao = new DAO;
            $stman = $dao->conecta()->prepare($sql);
            $stman->bindParam(":email", $usuario);
            $stman->bindParam(":senha", $senhaCryp);
            $stman->execute();
            $user = $stman->fetchALL();
            if (count($user) > 0) {
                //var_dump($user);
                $user["token"] = generateJWT($user[0]);
            }
            return $user;
        } catch (PDOException $pe) {
            throw new Exception("Erro ao busca acesso ao usuario: " . $pe->getMessage());
        } catch (Exception $e) {
            throw new Exception("Erro ao acessar a base de dados: " . $e->getMessage());
        }
    }


    private  function  formatDateBD($date)
    { // Entrada: DD/MM/YYYY -> YYYY/MM/DD
        $partDate = explode("/", $date);
        return ($partDate[2] . "-" . $partDate[1] . "-" . $partDate[0]);
    }
}
