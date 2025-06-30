<?php
class Routes
{
    public static function get($action, $method_name)
    {
        if($_SERVER['REQUEST_METHOD'] != 'GET') {
            return [$action => ['status' => false]];
        }
        return [
            $action => [
                'method_name' => $method_name,
                'status' => true,
                'inputs' => isset($_GET['input']) ? $_GET['input'] : "",
                'authUserId' => $_GET['authUserId'],
                'authRehabCenterId' => $_GET['authRehabCenterId']
                ]
            ];
    }

    public static function post($action, $method_name)
    {
        if($_SERVER['REQUEST_METHOD'] != 'POST') {
            return [$action => ['status' => false]];
        }
        $input =  json_decode(file_get_contents('php://input'), TRUE);
        return [
            $action => [
                'method_name' => $method_name,
                'status' => true,
                'inputs' => $input['input'],
                'authUserId' => $_GET['authUserId'],
                'authRehabCenterId' => $_GET['authRehabCenterId']
            ]
        ];
    }

    public static function put($action, $method_name)
    {
        if($_SERVER['REQUEST_METHOD'] != 'PUT') {
            return [$action => ['status' => false]];
        }
        $input =  json_decode(file_get_contents('php://input'), TRUE);
        return [
            $action => [
                'method_name' => $method_name,
                'status' => true,
                'inputs' => $input['input'],
                'authUserId' => $_GET['authUserId'],
                'authRehabCenterId' => $_GET['authRehabCenterId']
            ]
        ];
    }

    public static function delete($action, $method_name)
    {
        if($_SERVER['REQUEST_METHOD'] != "DELETE") {
            return [$action => ['status' => false]];
        }
        $input =  json_decode(file_get_contents('php://input'), TRUE);
        return [
            $action => [
                'method_name' => $method_name,
                'status' => true,
                'inputs' => $input['input'],
                'authUserId' => $_GET['authUserId'],
                'authRehabCenterId' => $_GET['authRehabCenterId']
            ]
        ];
    }

    public static function group($class_name, $routes){
        foreach($routes as $action => $data){
            $data['class_name'] = $class_name;
            $routes[$action] = $data;
        }
        return $routes;
    }
}
