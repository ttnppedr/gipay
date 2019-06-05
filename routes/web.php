<?php

use App\User;
use GuzzleHttp\Client;
use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/setpassword', function () {
    return view('editpw');
});

Route::patch('/user/{user}', 'UserController@update');

Route::get('/admin-login', function () {
    return view('admin-login');
});

Route::get('/admin-index', function () {
    return view('admin-index');
});

Route::get('/admin-users', function () {
    return view('admin-users');
});

Route::get('/admin-orders', function () {
    return view('admin-orders');
});

Route::get('/join', function (Request $request) {
    $queryString = [
        'client_id' => env('CLIENT_ID'),
        'client_secret' => env('CLIENT_SECRET'),
        'code' => $request->get('code'),
    ];
    $url = 'https://slack.com/api/oauth.access';

    $client = new Client();
    $res = $client->request('GET', $url . '?' . http_build_query($queryString));

    $oauthAccess = json_decode($res->getBody(), true);
    $token = $oauthAccess['access_token'];

    $url = 'https://slack.com/api/users.identity?token=' . $token;
    $res = $client->request('GET', $url);

    $user = json_decode($res->getBody(), true);

    $user = User::create([
        'slack_id' => $user['user']['id'],
        'name' => $user['user']['name'],
        'email' => $user['user']['email'],
        'avatar' => $user['user']['image_512'],
        'blocked' => true,
    ]);

    return redirect('setpassword')->with('user_id', $user->id);
});
