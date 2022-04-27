<?php

include_once "./config/config.php";
include_once "./config/jwt.php";

require_once "./app/services/DAO.php";
require_once "./app/models/usuario.php";
require_once "./app/controller/usuarioController.php";

//phpinfo();

try {
    //Variavel para os resultados
    $result = null;
    $httpCod = null;
    $auth = null;

    $method = isset($_SERVER["REQUEST_METHOD"]) ? $_SERVER["REQUEST_METHOD"] : null;

    header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/json; charset=UTF-8");

    if ($method == "OPTIONS") {
        //Cabeçalho comum da aplicação    
        header("Access-Control-Allow-Methods: POST, GET, PUT, OPTIONS");
        header("Access-Control-Allow-Headers: *");
        header("Access-Control-Max-Age: 3600"); //1hora == 3600 seg;
        header("Access-Control-Allow-Credentials: true");
    }

    if ($method != null && $method != "OPTIONS") {
        //Validação de rotas
        $url = explode("/", $_SERVER["REQUEST_URI"]);
        array_shift($url);
        array_shift($url);

        $auth = authentic($method, $url);
        //$auth = "userAll";

        //Verifica validação do usuario
        if ($auth != null && $url[count($url) - 1] == "logon") {
            $httpCod = 200;
            $result = $auth;
        } else if ($auth != null) {
            $httpCod = 200;
            $result = route($method, $url);
        } else {
            $httpCod = 401;
            $result = "Usuario sem autorização";
        }

        //A resposta se não existir errose se existirem dados
        //header('Content-Type: application/json;charset=utf-8');
        http_response_code($httpCod);
        echo json_encode(array("result" => $result));
    } else {
        //throw new Exception();
    };
} catch (Exception $e) {
    http_response_code(404);
    echo json_encode(array("result" => "Pagina não encontrada!"));
}


function route($method, $url)
{
    $result = null;
    //Rotas Autenticadas
    switch ($method) {

            //Leituras
        case "GET": {
                switch ($url[0]) {
                    case "usuario":
                        switch ($url[1]) {
                            case "get": {
                                    if (!isset($url[2])) throw new Exception();
                                    $userController = new usuarioController;
                                    $result = $userController->get($url[2]);
                                }
                                break;

                            case "list": {
                                    $userController = new usuarioController;
                                    $result = $userController->getAll();
                                }
                                break;

                            case "listnot": {
                                    $userController = new usuarioController;
                                    $result = $userController->getAll(0);
                                }
                                break;

                            default:
                                throw new Exception();
                                break;
                        }
                        break;

                    case "produto":
                        break;

                    default:
                        throw new Exception();
                        break;
                }
            }
            break;

            //Cadastro
        case "POST": {
                switch ($url[0]) {
                    case "usuario":
                        switch ($url[1]) {
                            case 'add':
                            case 'update':
                                $dadosUser = json_decode(file_get_contents('php://input')); //tranformar JSON do body em Objetos
                                $userController = new usuarioController;
                                $user = new Usuario;
                                $user->popo($dadosUser);
                                if ($user->id != null) { // Se tem id Update se não Add
                                    $result = $userController->update($user);
                                } else {
                                    $result = $userController->add($user);
                                }
                                break;
                            default:
                                throw new Exception();
                                break;
                        }
                        break;

                    default:
                        throw new Exception();
                        break;
                }
            }
            break;

            //Alteração
        case "PUT": {
                switch ($url[0]) {
                    case "usuario":
                        switch ($url[1]) {
                            case 'update':
                                $dadosUser = json_decode(file_get_contents('php://input')); //tranformar JSON do body em Objetos
                                $userController = new usuarioController;
                                $user = new Usuario;
                                $user->popo($dadosUser);
                                $result = $userController->update($user);
                                break;
                            default:
                                throw new Exception();
                                break;
                        }
                        break;

                    default:
                        throw new Exception();
                        break;
                }
            }
            break;

            //Delete
        case "DELETE": {
                switch ($url[0]) {
                    case "usuario":
                        switch ($url[1]) {
                            case 'delete':
                                if (!isset($url[2])) throw new Exception();
                                $userController = new usuarioController;
                                $result = $userController->delete($url[2]);
                                break;
                            default:
                                throw new Exception();
                                break;
                        }
                        break;

                    default:
                        throw new Exception();
                        break;
                }
            }
            break;
    }
    return $result;
}


function authentic($method, $url)
{
    $result = null;

    //Cria um session
    if (!session_start()) {
        session_start();
    };

    //Autenticação
    $token = isset($_SERVER["HTTP_AUTHORIZATION"]) ? $_SERVER["HTTP_AUTHORIZATION"] : null;
    $auth = isset($_SESSION[$token]) ? $_SESSION[$token] : null;

    //Rotas Não Autenticadas
    if ($method == "POST" && $auth == null) {
        switch ($url[0]) {
            case "usuario":
                switch ($url[1]) {
                    case 'logon':
                        $dadosUser = json_decode(file_get_contents('php://input')); //tranformar JSON do body em Objetos
                        $userController = new usuarioController;
                        $result = $userController->logon($dadosUser->usuario, $dadosUser->senha);
                        $token = isset($result->token) ? $result->token : $token;
                        break;
                    default:
                        //throw new Exception();
                        $result = null;
                        break;
                }
                break;

            default:
                //throw new Exception();
                $result = null;
                break;
        }
    }
    $auth = $token != null ? validJWT($token) : null;

    if ($token != null && $auth != null) {
        $_SESSION[$token] = json_decode($auth);
        $result = isset($result) ? $result : json_decode($auth);
    }

    return $result;
}
