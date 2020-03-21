<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Manage panel authentication
|--------------------------------------------------------------------------
|
| Here is routes for login and logout.
|
*/

Route::name('manage_login')
    ->get('manage/login', 'Manage\ManageLoginController@showLoginForm')
    ->middleware(['manage', 'manage.not.authenticated']);

Route::name('manage_login')
    ->post('manage/login', 'Manage\ManageLoginController@login')
    ->middleware(['manage', 'manage.not.authenticated']);

Route::name('manage_logout')
    ->post('manage/logout', 'Manage\ManageLoginController@logout')
    ->middleware(['manage', 'auth:admin,manager']);

/*
|--------------------------------------------------------------------------
| Routes for asset loading to manage side.
|--------------------------------------------------------------------------
|
| assets are separated by accessibility.
| System assets available only if you passes `auth:manage`, otherwise public assets accessible any time.
| Same as for modules assets.
| Paths for assets are:
| core/assets/system
| core/assets/public
| modules/ModuleName/assets/system
| modules/ModuleName/assets/public
|
*/

Route::name('manage_assets_system')
    ->get('manage/assets/system/{asset}', 'Assets\AssetsController@getSystemAsset')
    ->where('asset', '.+')
    ->middleware(['manage', 'auth:admin,manager']);

Route::name('manage_assets_public')
    ->get('manage/assets/public/{font}', 'Assets\AssetsController@getPublicAsset')
    ->where('font', '.+')
    ->middleware('manage');

Route::name('manage_assets_module_system')
    ->get('manage/assets/module/{module}/system/{asset}', 'Assets\AssetsController@getModuleSystemAsset')
    ->where('asset', '.+')
    ->middleware(['manage', 'auth:admin,manager']);

Route::name('manage_assets_module_public')
    ->get('manage/assets/module/{module}/public/{asset}', 'Assets\AssetsController@getModulePublicAsset')
    ->where('asset', '.+')
    ->middleware('manage');

Route::name('manage_assets_storage')
    ->get('manage/assets/storage/{asset}', 'Assets\AssetsController@getStorageAsset')
    ->where('asset', '.+')
    ->middleware(['manage', 'auth:admin,manager']);

Route::name('manage_assets_temp')
    ->get('manage/assets/temp/{asset}', 'Assets\AssetsController@getTempAsset')
    ->where('asset', '.+')
    ->middleware(['manage', 'auth:admin,manager']);

Route::name('manage_assets_temp_upload')
    ->post('manage/assets/temp', 'Assets\AssetsController@postTempAsset')
    ->middleware(['manage', 'auth:admin,manager']);

/*
|--------------------------------------------------------------------------
| Api for manage side.
|--------------------------------------------------------------------------
|
| These routes are protected by jwt authenticator.
|
*/

Route::name('manage_api')
    ->prefix('manage/api/')
    ->any('{route?}', 'Api\ManageApiRouter@handleRequest')
    ->where('route', '[\/\w\.-]*')
    ->middleware(['manage_api', 'auth:admin,admin_api,manager,manager_api']);

/*
|--------------------------------------------------------------------------
| Manage panel
|--------------------------------------------------------------------------
|
| Here is routes for manage panel.
|
*/

Route::name('manage_panel')
     ->get('manage/{route?}', 'Manage\ManagePanelController@loadManagePanel')
     ->where('route', '[\/\w\.-]*')
     ->middleware(['manage', 'auth:admin,manager']);

