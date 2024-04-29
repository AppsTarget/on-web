<?php

use Illuminate\Http\Request;

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
Route::post('/login',                   'AppApiController@login');
Route::get('/mostrar-anamnese/{id}',  'AppApiController@mostrar_resposta_anamnese');
Route::get('/mostrar-iec/{id}',       'AppApiController@mostrar_resposta_iec');
Route::get('/mostrar-contrato/{id}/{antigo}',       'AppApiController@mostrar_contrato');

Route::get('/getCalendarDetails',       'AppApiController@getCalendarDetails'); 
Route::post('/saveHealth',         'AppApiController@saveHealth'); 
Route::post('/getCalendar',       'AppApiController@getCalendar'); 
Route::get('/getMedicalRecord',       'AppApiController@getMedicalRecord'); 
Route::get('/getContratos',   'AppApiController@getContratos');
Route::get('/teste',        'AppApiController@teste');
Route::get('/getLogin',       'AppApiController@getLogin'); 
Route::get('/getHealthResume',       'AppApiController@getHealthResume'); 
Route::get('/getActivities',       'AppApiController@getActivities'); 

Route::get('/planosPorPessoa',            'AppApiController@planos_por_pessoa'); 
Route::get('/modalidadesPorPlano',        'AppApiController@modalidades_por_plano'); 
Route::get('/profissionaisPorModalidade', 'AppApiController@profissionais_por_modalidade'); 
Route::get('/listarHorarios',             'AppApiController@listarHorarios'); 
Route::get('/agendar',                    'AppApiController@agendar'); 
Route::get('/confirmar',                  'AppApiController@confirmar'); 

Route::get('/emp',       'AppApiController@emp'); 

Route::get('/getIECs',       'AppApiController@getIECs'); 
Route::get('/getAnamneses',       'AppApiController@getAnamneses'); 
Route::get('/getDataPerson',       'AppApiController@getDataPerson'); 

Route::get('/getDocs',                    'AppApiController@getDocs'); 
Route::get ('/baixar-anexo/{id}',               'AppApiController@baixarDoc');
Route::get('/deleteAnexo',                     'AppApiController@deleteAnexo');


Route::post('/getAvatar',          'AppApiController@getAvatar'); 
Route::post('/pdfIEC',             'AppApiController@pdfIEC'); 
Route::post('/pdfAnamnese',        'AppApiController@pdfAnamnese'); 
Route::post('/postSigin',          'AppApiController@postSigin'); 
Route::post('/deleteSigin',        'AppApiController@deleteSigin'); 
Route::post('/refreshIecStatus',   'AppApiController@refreshIecStatus'); 
Route::post('/sendIecOrder',       'AppApiController@sendIecOrder'); 
Route::post('/sendEmailPassword',  'AppApiController@sendEmailPassword'); 

Route::get('/getGrafico/{op}/{iPersonID}',      'AppApiController@getGrafico');
Route::get('/gerar-grafico1/{op}/{iPersonID}', 'AppApiController@gerar_grafico_1'); 
Route::get('/gerar-grafico2/{op}/{iPersonID}', 'AppApiController@gerar_grafico_2'); 

Route::get('/export-pdf', 'AppApiController@exportarPDF');

Route::get('/sendEmailPassword/json/', 'AppApiController@recoverAccount');
Route::get('/sendEmailPassword',        'AppApiController@recoverAccount2');

Route::get('/recoverPassword_anoeui234b1oc8b30b82310b13413ur3d13', 'AppApiController@return_view_recover');
Route::get('/recriarSenha',                                        'AppApiController@savePassApp');

Route::get('/tela-de-sucesso',        'AppApiController@tela_de_sucesso');

Route::post('/salvar-confirmacao-agendamento',    'AppApiController@salvar_confirmacao_agendamento');

Route::post('/setImagePerson',     'AppApiController@setImagePerson');
Route::post('/uploadDocs',         'AppApiController@uploadDocs');


// WEBHOOK ZAP SIGN \\
Route::post('/webhook-zapsign',          'WebhookApiController@webhook');



// WEBHOOK ZENVIA \\
Route::post('/webhook-zenvia',  'WebhookApiController@zenvia');
Route::get('/enviar-mensagem',  'WebhookApiController@enviar_mensagem');


Route::get('/teste-segundoplano',      'AppApiController@testSegundoPlano');