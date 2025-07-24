<?php
include '../core/config.php';
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: *");
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Content-Type: application/json');

$response = array();
if (isset($_GET['action'])) {
    $action = $_GET['action'];

    $registered_actions = array_merge(
        Routes::group('Users', array_merge(
            Routes::post('add_user', 'add'),
            Routes::get('show_user', 'show'),
            Routes::get('show_filtered_users', 'show_filtered_users'),
            Routes::get('view_user', 'view'),
            Routes::put('edit_user', 'edit'),
            Routes::put('edit_supervisor_code', 'editSupservisorCode'),
            Routes::put('edit_admin_code', 'editAdminCode'),
            Routes::put('edit_userInfo', 'editUserInfo'),
            Routes::put('edit_userPassword', 'editUserPassword'),
            Routes::get('check_user_pass', 'checkUserPass'),
            Routes::post('update_user_status', 'edit_status'),
            Routes::post('update_on_route_status', 'edit_on_route_status'),
            Routes::post('update_on_route_status_dsp', 'edit_on_route_status_dsp'),
            Routes::post('reset_user_password', 'reset_password'),
            Routes::post('update_user_loginstatus', 'edit_loginstatus'),
            Routes::post('edit_user_password', 'edit_password'),
            Routes::delete('delete_user', 'remove'),
            Routes::post('login_user', 'login'),
            Routes::post('login_mobile', 'login_mobile'),
            Routes::post('register_mobile', 'register_mobile'),
            Routes::post('verify_token', 'verify_token'),
            Routes::post('get_user', 'get_user'),
            Routes::post('update_user_mobile', 'update_user'),
            Routes::post('update_user_login_status_logout', 'update_user_login_status_logout'),
            Routes::get('logout_user', 'logout'),
            Routes::get('preset_user', 'preset'),
            Routes::get('schema_user', 'schema')
        )),
        Routes::group('RehabCenters', array_merge(
            Routes::get('show_rehab_centers', 'show'),
            Routes::get('show_public_rehab_centers', 'show_public'),
            Routes::get('show_detail_mobile', 'show_rehab_center_account'),
            Routes::post('delete_services', 'remove'),
            Routes::post('register_rehab_center', 'register')
            
        )),
        Routes::group('Services', array_merge(
            Routes::get('show_services', 'show'),
            Routes::post('delete_services', 'remove'),
            Routes::post('add_services', 'add'),
            Routes::post('update_services', 'edit')
            
        )),
        Routes::group('RehabGallery', array_merge(
            Routes::get('show_rahab_gallery', 'show'),
            Routes::post('delete_rehab_gallery', 'remove'),
            Routes::post('add_rehab_gallery', 'add'),
            Routes::post('update_rahab_gallery', 'edit')
            
        ))
        
    );

    if (array_key_exists($action, $registered_actions)) {
        if ($registered_actions[$action]['status']) {
            $ClassName = new $registered_actions[$action]['class_name'];
            $method = $registered_actions[$action]['method_name'];

            $ClassName->inputs = $registered_actions[$action]['inputs'];
            $ClassName->authUserId = $registered_actions[$action]['authUserId'];
            $ClassName->authRehabCenterId = $registered_actions[$action]['authRehabCenterId'];
            $response['data'] = $ClassName->$method();
            $response['status'] = $ClassName->response;
            $response['message'] = "OK";
        } else {
            $response['data'] = [];
            $response['status'] = "error";
            $response['message'] = "Bad Request";
        }
    } else {
        $response['data'] = [];
        $response['status'] = "error";
        $response['message'] = 404;
    }
} else {
    $response['status'] = "error";
    $response['message'] = 403;
}

echo json_encode($response);
