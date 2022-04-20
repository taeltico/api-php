-- Definição dos Dados (DDL)
CREATE table usuario ( 
    id int AUTO_INCREMENT not null PRIMARY KEY,
    nome varchar(100) not null,
    senha varchar(255) not null,
    data_nasc Date null,
    email varchar(100) not null,
    fotoPerfil varchar(255) null,
    tel varchar(50) null,
    cpf varchar(12) not null
);

CREATE TABLE endereco (
    cep	varchar(8) PRIMARY KEY not null,
    logradouro	varchar(150),
    bairro	varchar(50),
    cidade	varchar(50),
    uf	varchar(2)
);

CREATE table usuario_endereco(
	numero	varchar(20),
	complemento	varchar(100),
	ehPrincipal	boolean,
	id_usuario	int null,
    FOREIGN KEY (id_usuario) REFERENCES usuario(id),
	cep	varchar(8) null,
    FOREIGN KEY (cep) REFERENCES endereco(cep)
);

ALTER TABLE usuario ADD ativo BOOLEAN NOT NULL;


-- Manipulação dos Dados (DML)
INSERT INTO usuario 
(id,nome,senha,data_nasc,email,fotoPerfil,tel,cpf,ativo) 
VALUES
(null, "Fabiano Moreira", "123@123", "1980-04-02", "fabianomoreira.net@gmail.com","","5555-55555","12312312344", true);

UPDATE usuario set Nome = "Carlos Roberto", Email = "cr@email.com" where id = 3;

SELECT * FROM usuario;

DELETE FROM usuario Where id = 2;