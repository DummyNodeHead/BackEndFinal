<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::namespace('Api')->group(function (){
    
    /*Route::get('/delete/{ID}','CourseController@delete');
    Route::get('/query/{ID}','CourseController@query');
    Route::get('/insert/{NAME}/{DESCRIPTION}/{CREDITS}/{MAX}','CourseController@insert');
    Route::get('/update/{ID}/{choose}/{updated}','CourseController@update');*/
    Route::get('/users','CourseController@index')->middleware(\App\Http\Middleware\CrossHttp::class);  //查询
    Route::get('/show','CourseController@show')->middleware(\App\Http\Middleware\CrossHttp::class);  //查询
    Route::post('/add','CourseController@store')->middleware(\App\Http\Middleware\CrossHttp::class);  //查询    //新增
    Route::get('/delete','CourseController@destory')->middleware(\App\Http\Middleware\CrossHttp::class);  //查询
    Route::post('/update','CourseController@update')->middleware(\App\Http\Middleware\CrossHttp::class);  //查询

});

Route::apiResource('/login','LoginController')->middleware(\App\Http\Middleware\CrossHttp::class);
Route::apiResource('/findpsw','PassforgotController')->middleware(\App\Http\Middleware\CrossHttp::class);
Route::apiResource('/systemlog','SystemlogController')->middleware(\App\Http\Middleware\CrossHttp::class);
Route::apiResource('updatepsw','UpdatepswController')->middleware(\App\Http\Middleware\CrossHttp::class);
Route::apiResource('getinformation','GetinforController')->middleware(\App\Http\Middleware\CrossHttp::class);

use \App\Http\Middleware\CrossHttp;
use \App\Http\Middleware\EnableCrossRequest;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
Route::apiResource('adduser', AddUserController::class)->middleware(CrossHttp::class);
Route::apiResource('deleteuser', DeleteUserController::class)->middleware(CrossHttp::class);
Route::apiResource('find', FindUserController::class)->middleware(CrossHttp::class);
Route::apiResource('change', ChangeUserController::class)->middleware(CrossHttp::class);
Route::apiResource('test', TestController::class)->middleware(CrossHttp::class);

//Route::get('/users','UserController@index')->name('users.index');

Route::apiResource('changeStu','ChangerStuInformController')->middleware(CrossHttp::class);
Route::apiResource('changeTea','ChangerTeaInformController')->middleware(CrossHttp::class);
Route::apiResource('changeMan','ChangeManInformController')->middleware(CrossHttp::class);
Route::apiResource('BatchInput','UploadController')->middleware(CrossHttp::class);
Route::apiResource('UpPhotoTea','UpPhotoTeaController')->middleware(CrossHttp::class);
Route::apiResource('UpPhotoStu','UpPhotoStuController')->middleware(CrossHttp::class);
Route::apiResource('UpPhotoMan','UpPhotoManController')->middleware(CrossHttp::class);




use App\Http\Controllers\AssignmentController;
use App\Http\Controllers\QuizController;
use App\Http\Controllers\SourceController;
use App\Http\Controllers\TSourceController;
use App\Http\Controllers\CourseController;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
//get
Route::get('/Assignment',[AssignmentController::class,'showAssignment']);
Route::get('/HWanalysis',[AssignmentController::class,'showAssignmentScore']);
Route::get('/THWanalysis',[AssignmentController::class,'showStudentAssignmentScore']);
Route::get('/HWMarking',[AssignmentController::class,'showStudentAssignment']);
Route::get('/getAssignmentID',[AssignmentController::class,'getAssignmentID']);

Route::get('/Quiz',[QuizController::class, 'showQuiz']);
Route::get('/Grade',[QuizController::class, 'showGrade']);
Route::get('/Studentanalysis',[QuizController::class, 'showStudentAnalysisScore']);
Route::get('/Quizanalysis',[QuizController::class, 'showQuizAnalysisScore']);
Route::get('/TQuiz',[QuizController::class, 'showStudentQuiz']);
//Route::get('/Analysischarts',[QuizController::class,'showAnalysisChart']);
Route::get('/AllCourse',[CourseController::class, 'showAllCourse']);
Route::get('/CourseResource',[TSourceController::class,'showCourseResource']);
Route::get('/TCourseResource',[TSourceController::class,'showTeacherCourseResource']);

Route::get('/PersonalResource',[SourceController::class,'showPersonalResource']);
Route::get('/TPersonalResource',[SourceController::class,'showTeacherPersonalResource']);

//post 
Route::post('/addAssignment',[AssignmentController::class, 'store']);

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/searchname', function() {
    $ret = (new App\Http\Controllers\CourseController)->search_by_name($_GET['name']);
    return $ret;
});

Route::get('/searchid', function() {
    $ret = (new App\Http\Controllers\CourseController)->search_by_id($_GET['id']);
    return $ret;
});


Route::get('/searchteacher', function() {
    $ret = (new App\Http\Controllers\CourseController)->search_by_teacher($_GET['t']);
    return $ret;
});



Route::post('/changetime', 'TimeManagementController@update()');

Route::get('/searchcourseById/{id}',function ($id){
    $ret = (new App\Http\Controllers\ViewResult)->viewResult($id);
    return $ret;
});

Route::get('/searchStuById/{id}',function($id){
    $ret = (new App\Http\Controllers\ViewStudent)->viewStudent($id);
    return $ret;
});

Route::get('/getAllCourse',function(){
    $ret = (new App\Http\Controllers\CourseController)->getAll();
    return $ret;
});

Route::get('/getDistinctCourse',function(){
    $ret = (new App\Http\Controllers\CourseController)->getDistinct();
    return $ret;
});

Route::get('/chooseCourse', function() {
    $ret = (new App\Http\Controllers\CourseController)->chooseCourse($_GET['stu'], $_GET['cid']);
    return $ret;
});

Route::get('/choosePlan', function() {
    $ret = (new App\Http\Controllers\CourseController)->choosePlan($_GET['stu'], $_GET['cid']);
    return $ret;
});

Route::get('/getPlanByID', function() {
    $ret = (new App\Http\Controllers\CourseController)->getPlanByID($_GET['id']);
    return $ret;
});

Route::get('/delCourseinPlan', function() {
    $ret = (new App\Http\Controllers\CourseController)->delCourseinPlan($_GET['stu'], $_GET['cid']);
    return $ret;
});

Route::get('/delCourse', function() {
    $ret = (new App\Http\Controllers\CourseController)->delCourse($_GET['stu'], $_GET['cid']);
    return $ret;
});

Route::get('/managerChooseCourse', function() {
    $ret = (new App\Http\Controllers\CourseController)->managerChooseCourse($_GET['stu'], $_GET['cid']);
    return $ret;
});

Route::get('/managerState', function() {
    $ret = (new App\Http\Controllers\TimeManagementController)->set_state($_GET['state']);
    return $ret;
});

Route::get('/getManageState', function() {
    $ret = (new App\Http\Controllers\TimeManagementController)->get_state();
    return $ret[0]->state;
});