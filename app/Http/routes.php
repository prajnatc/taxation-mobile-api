<?php
/**
 * Routes
 * @category Route
 * @author ThinkPace
 */
/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
 */

$api = app('Dingo\Api\Routing\Router');

$api->version('v1', ['namespace' => 'App\Http\Controllers\Api\V1'], function ($api) use ($app) {

 $api->get('get-tax-type', [
            'as' => 'user.tax-type',
            'uses' => 'ItemController@getTaxtType',
        ]);

 $api->post('insert-item-master', [
            'as' => 'user.insert.item-master',
            'uses' => 'ItemController@insertItemMaster',
        ]);

 $api->get('get-item-list', [
            'as' => 'user.item-list',
            'uses' => 'ItemController@getItemList',
        ]);

 $api->get('get-item-list/{slug}', [
            'as' => 'user.item-list',
            'uses' => 'ItemController@getItemListOnSearch',
        ]);

 $api->post('update-item-master', [
            'as' => 'user.update.item-master',
            'uses' => 'ItemController@updateItemMaster',
        ]);

 $api->delete('delete-item-master/{id}', [
            'as' => 'user.delete.item-master',
            'uses' => 'ItemController@deleteItemMaster',
        ]);
 $api->get('item-category-list', [
            'as' => 'user.item-category-list',
            'uses' => 'ItemController@getItemCategory',
        ]);
  $api->get('item-unit-type-list', [
            'as' => 'user.item-unit-type-list-list',
            'uses' => 'ItemController@getItemUnitType',
        ]);

//Vendor
$api->get('get-vendor-list', [
            'as' => 'user.vendor-list',
            'uses' => 'ItemController@getVendorList',
        ]);

$api->get('get-vendor-list-active', [
            'as' => 'user.vendor-list',
            'uses' => 'ItemController@getVendorListActive',
        ]);

$api->get('get-vendor-list/{slug}', [
            'as' => 'user.vendor-list',
            'uses' => 'ItemController@getVendorListOnSearch',
        ]);

$api->post('insert-vendor-master', [
            'as' => 'user.insert.vendor-master',
            'uses' => 'ItemController@insertVendorMaster',
        ]);

$api->post('update-vendor-master', [
            'as' => 'user.update.vendor-master',
            'uses' => 'ItemController@updateVendorMaster',
        ]);

$api->delete('delete-vendor-master/{id}', [
            'as' => 'user.delete.vendor-master',
            'uses' => 'ItemController@deleteVendorMaster',
        ]);

//Receive Item
$api->post('insert-received-item', [
            'as' => 'user.insert.received-item',
            'uses' => 'ItemController@insertReceivedItem',
        ]);

$api->get('get-item-received-list', [
            'as' => 'user.get-item-received-list',
            'uses' => 'ItemController@getReceivedItemList',
        ]);

$api->get('get-item-received-list/{slug}', [
            'as' => 'user.get-item-received-list',
            'uses' => 'ItemController@getReceivedItemListOnSearch',
        ]);
 
$api->get('get-item-stock', [
            'as' => 'user.get-item-stock',
            'uses' => 'ItemController@getItemStock',
        ]);

$api->get('get-item-stock/{slug}', [
            'as' => 'user.get-item-stock',
            'uses' => 'ItemController@getItemStockOnSearch',
        ]);

$api->get('get-tax-report', [
            'as' => 'user.get-tax-report',
            'uses' => 'ItemController@getTaxReport',
        ]);

$api->get('get-tax-report/{slug}', [
            'as' => 'user.get-tax-report',
            'uses' => 'ItemController@getTaxReportOnSearch',
        ]);
});






$app->get('/', function () use ($app) {
     return $app->version();
});


$app->get('/testpusher', 'ExampleController@testpusher');


 $app->get('/doc', function () use ($app) {
   $path = storage_path() . "/app/apidoc.json";

   return file_get_contents($path);
 });

$app->post('auth/signin', 'Auth\AuthController@signin');

$api = app('Dingo\Api\Routing\Router');

// v1 version API
// choose version add this in header    Accept:application/vnd.lumen.v1+json
$api->version('v1', ['namespace' => 'App\Http\Controllers\Api\V1'], function ($api) use ($app) {

    # Auth
    //sign in generate otp
    $api->post('auth/generate-otp', [
        'as' => 'auth.signin.otp',
        'uses' => 'AuthController@signinOtp',
        'middleware' =>'validate.app'
    ]);

    // signin
    $api->post('auth/signin', [
        'as' => 'auth.signin',
        'uses' => 'AuthController@signin',
    ]);
    
    // signup
    $api->post('auth/signup', [
        'as' => 'auth.signup',
        'uses' => 'AuthController@signup',
    ]);

    # User
    // user list
    $api->get('users', [
        'as' => 'users.index',
        'uses' => 'UserController@index',
    ]);
    // user detail
    $api->get('users/{id}', [
        'as' => 'users.show',
        'uses' => 'UserController@show',
    ]);

    # POST
    // post list
    $api->get('posts', [
        'as' => 'posts.index',
        'uses' => 'PostController@index',
    ]);
    // post detail
    $api->get('posts/{id}', [
        'as' => 'posts.show',
        'uses' => 'PostController@show',
    ]);

    # POST COMMENT
    // post comment list
    $api->get('posts/{postId}/comments', [
        'as' => 'posts.comments.index',
        'uses' => 'PostCommentController@index',
    ]);

    $api->get('clients', [
        'as' => 'admin.client.details',
        'uses' => 'AdminController@clients',
        'middleware' =>'validate.app'
    ]);

    $api->get('login-available-types', [
        'as' => 'admin.login.availableTypes',
        'uses' => 'AdminController@loginAvailableTypes',
        'middleware' =>'validate.app'
    ]);

    $api->post('import-parent-number', [
        'as' => 'admin.importStudent',
        'uses' => 'StudentController@importParentNumber',
        'middleware' =>'validate.app'
    ]);

    //send notification invoked by Web APP, authentication is not required.
    $api->post('send-notification', [
            'as' => 'admin.send-notification',
            'uses' => 'ParentController@sendNotification',
            'middleware' =>'validate.app'
    ]);

    // need authentication
    $api->group(['middleware' => ['api.auth','validate.app']], function ($api) {

        # AUTH
        // refresh jwt token
        $api->post('auth/token/refresh', [
            'as' => 'auth.token.refresh',
            'uses' => 'AuthController@refreshToken',
        ]);

        $api->get('auth/logout', [
            'as' => 'auth.logout',
            'uses' => 'AuthController@logout',
        ]);

        # USER
        // my detail
        $api->get('user', [
            'as' => 'user.show',
            'uses' => 'UserController@userShow',
        ]);
        // update my info
        $api->put('user', [
            'as' => 'user.update',
            'uses' => 'UserController@update',
        ]);
        // update my password
        $api->post('user/password', [
            'as' => 'user.password.update',
            'uses' => 'UserController@editPassword',
        ]);

        // update FCM Token for every login.
        $api->post('user-token', [
            'as' => 'user.update.mobiletoken',
            'uses' => 'UserController@updateMobileToken',
        ]);

        # POST
        // create a post
        $api->post('posts', [
            'as' => 'posts.store',
            'uses' => 'PostController@store',
        ]);
        // update a post
        $api->put('posts/{id}', [
            'as' => 'posts.update',
            'uses' => 'PostController@update',
        ]);

        // delete a post
        $api->delete('posts/{id}', [
            'as' => 'posts.destroy',
            'uses' => 'PostController@destroy',
        ]);

        # POST COMMENT
        // create a comment
        $api->post('posts/{postId}/comments', [
            'as' => 'posts.comments.store',
            'uses' => 'PostCommentController@store',
        ]);
        // delete a comment
        $api->delete('posts/{postId}/comments/{id}', [
            'as' => 'posts.comments.destroy',
            'uses' => 'PostCommentController@destroy',
        ]);


        $api->get('parent', [
            'as' => 'parent.show',
            'uses' => 'ParentController@parent',
        ]);

        //updated to post method
        $api->get('parent/{parentId}/students', [
            'as' => 'parent.students',
            'uses' => 'ParentController@students',
        ]);


        $api->get('parent/{parentId}/messages', [
            'as' => 'notification.parent.messsages',
            'uses' => 'NotificationController@parentMessages'
        ]);

        $api->get('parent/{parentId}/client/{clientId}/student/{studentId}/attendance-details', [
            'as' => 'parent.student.attendance.details',
            'uses' => 'StudentController@getStudentAttendanceDetails'
        ]);

        $api->get('parent/{parentId}/client/{clientId}/student/{studentId}/student-info', [
            'as' => 'parent.student.info.details',
            'uses' => 'StudentController@getStudentDetails'
        ]);

        $api->delete('parent/{parentId}/client/{clientId}/messages/{messageId}/archive', [
            'as' => 'notification.parent.archive.messsages',
            'uses' => 'NotificationController@archiveStudentMessageDetails'
        ]);

        $api->get('parent/{parentId}/client/{clientId}/student/{studentId}/attendance-chart-details', [
            'as' => 'parent.student.attendance.chart.details',
            'uses' => 'StudentController@getStudentAttendanceChartDetails'
        ]);

        $api->get('parent/{parentId}/client/{clientId}/student/{studentId}/attendance-date-details', [
            'as' => 'parent.student.attendance.date.details',
            'uses' => 'StudentController@getStudentAttendanceDateDetails'
        ]);

        // Course Time table
        $api->get('parent/{parentId}/client/{clientId}/student/{studentId}/course-time-table', [
            'as' => 'parent.student.course.time.details',
            'uses' => 'StudentController@getCourseTimeTable'
        ]);

        // Assignments
        $api->get('parent/{parentId}/client/{clientId}/student/{studentId}/assignments-details', [
            'as' => 'parent.student.course.assignment.details',
            'uses' => 'StudentController@getCourseAssignmentDetails'
        ]);

        // Notice Board
        $api->get('parent/{parentId}/client/{clientId}/student/{studentId}/notice-board-details', [
            'as' => 'parent.student.course.notice.board.details',
            'uses' => 'StudentController@getNoticeBoardDetails'
        ]);


        // Institutuion Calender
        $api->get('parent/{parentId}/client/{clientId}/student/{studentId}/institution-calender-details', [
            'as' => 'parent.student.course.institution.calender.details',
            'uses' => 'StudentController@getInstitutionCalenderDetails'
        ]);

        //Prajna
        // $api->post('parent/{parent_number}/updatefcmtoken', [
        //     'as' => 'parent.update.fcmtoken',
        //     'uses' => 'ParentController@updateFcmToken'
        // ]);
    });
});

// v2 version API
// add in header    Accept:application/vnd.lumen.v2+json
$api->version('v2', ['namespace' => 'App\Http\Controllers\Api\V2'], function ($api) use ($app) {
    $api->get('/', 'FooController@index');
});