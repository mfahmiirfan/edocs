<?php
defined('BASEPATH') or exit('No direct script access allowed');

/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
|
| Typically there is a one-to-one relationship between a URL string
| and its corresponding controller class/method. The segments in a
| URL normally follow this pattern:
|
|	example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|	https://codeigniter.com/userguide3/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There are three reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router which controller/method to use if those
| provided in the URL cannot be matched to a valid route.
|
|	$route['translate_uri_dashes'] = FALSE;
|
| This is not exactly a route, but allows you to automatically route
| controller and method names that contain dashes. '-' isn't a valid
| class or method name character, so it requires translation.
| When you set this option to TRUE, it will replace ALL dashes in the
| controller and method URI segments.
|
| Examples:	my-controller/index	-> my_controller/index
|		my-controller/my-method	-> my_controller/my_method
*/
$route['default_controller'] = 'welcome';
$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;

$route['users']['GET'] = 'UserController/index';
$route['users/get-paginated']['GET'] = 'UserController/getPaginated';
$route['users/login']['POST'] = 'UserController/login';
$route['users/logout']['POST'] = 'UserController/logout';
$route['users/(:any)']['GET'] = 'UserController/show/$1';
$route['users/(:any)/show-data']['GET'] = 'UserController/showData/$1';
$route['users']['POST'] = 'UserController/store';
$route['users/(:any)']['POST'] = 'UserController/update/$1';
$route['users/(:any)']['DELETE'] = 'UserController/delete/$1';

$route['access/check']['POST'] = 'AccessController/check';

$route['roles']['GET'] = 'RoleController/index';

$route['email/send-notification-email']['POST'] = 'EmailController/SendNotificationEmail';

$route['integrated-report/initiate']['GET'] = 'IntegratedReportController/initiateIndex';
$route['integrated-report/export']['POST'] = 'IntegratedReportController/export';
$route['integrated-report']['GET'] = 'IntegratedReportController/index';
$route['integrated-report/get-paginated']['GET'] = 'IntegratedReportController/getPaginated';
$route['integrated-report/(:any)']['GET'] = 'IntegratedReportController/show/$1';
$route['integrated-report']['POST'] = 'IntegratedReportController/store';
$route['integrated-report/(:any)']['POST'] = 'IntegratedReportController/update/$1';
$route['integrated-report/(:any)']['DELETE'] = 'IntegratedReportController/delete/$1';
$route['integrated-report/(:any)/download']['POST'] = 'IntegratedReportController/download/$1';
$route['integrated-report/(:any)/preview']['POST'] = 'IntegratedReportController/preview/$1';

$route['department']['GET'] = 'DepartmentController/index';

$route['menu']['GET'] = 'MenuController/index';

$route['documents/get-expired-soon']['GET'] = 'DocumentController/getExpiredSoon';
$route['documents/get-expired-soon-paginated']['POST'] = 'DocumentController/getExpiredSoonPaginated';
$route['documents/search']['POST'] = 'DocumentController/search';

$route['activity-log/get-paginated']['GET'] = 'ActivityLogController/getPaginated';
$route['activity-log/export']['POST'] = 'ActivityLogController/export';


$route['form/initiate']['GET'] = 'FormController/initiateIndex';
$route['form/export']['POST'] = 'FormController/export';
$route['form']['GET'] = 'FormController/index';
$route['form/get-paginated']['GET'] = 'FormController/getPaginated';
$route['form/(:any)']['GET'] = 'FormController/show/$1';
$route['form']['POST'] = 'FormController/store';
$route['form/(:any)']['POST'] = 'FormController/update/$1';
$route['form/(:any)']['DELETE'] = 'FormController/delete/$1';
$route['form/(:any)/download']['POST'] = 'FormController/download/$1';
$route['form/(:any)/preview']['POST'] = 'FormController/preview/$1';

$route['hira-asdam/initiate']['GET'] = 'HiraAsdamController/initiateIndex';
$route['hira-asdam/export']['POST'] = 'HiraAsdamController/export';
$route['hira-asdam']['GET'] = 'HiraAsdamController/index';
$route['hira-asdam/get-paginated']['GET'] = 'HiraAsdamController/getPaginated';
$route['hira-asdam/(:any)']['GET'] = 'HiraAsdamController/show/$1';
$route['hira-asdam']['POST'] = 'HiraAsdamController/store';
$route['hira-asdam/(:any)']['POST'] = 'HiraAsdamController/update/$1';
$route['hira-asdam/(:any)']['DELETE'] = 'HiraAsdamController/delete/$1';
$route['hira-asdam/(:any)/download']['POST'] = 'HiraAsdamController/download/$1';
$route['hira-asdam/(:any)/preview']['POST'] = 'HiraAsdamController/preview/$1';

$route['monthly-audit/initiate']['GET'] = 'MonthlyAuditController/initiateIndex';
$route['monthly-audit/export']['POST'] = 'MonthlyAuditController/export';
$route['monthly-audit']['GET'] = 'MonthlyAuditController/index';
$route['monthly-audit/get-paginated']['GET'] = 'MonthlyAuditController/getPaginated';
$route['monthly-audit/(:any)']['GET'] = 'MonthlyAuditController/show/$1';
$route['monthly-audit']['POST'] = 'MonthlyAuditController/store';
$route['monthly-audit/(:any)']['POST'] = 'MonthlyAuditController/update/$1';
$route['monthly-audit/(:any)']['DELETE'] = 'MonthlyAuditController/delete/$1';
$route['monthly-audit/(:any)/download']['POST'] = 'MonthlyAuditController/download/$1';
$route['monthly-audit/(:any)/preview']['POST'] = 'MonthlyAuditController/preview/$1';

$route['permit/initiate']['GET'] = 'PermitController/initiateIndex';
$route['permit/export']['POST'] = 'PermitController/export';
$route['permit']['GET'] = 'PermitController/index';
$route['permit/get-paginated']['GET'] = 'PermitController/getPaginated';
$route['permit/(:any)']['GET'] = 'PermitController/show/$1';
$route['permit']['POST'] = 'PermitController/store';
$route['permit/(:any)']['POST'] = 'PermitController/update/$1';
$route['permit/(:any)']['DELETE'] = 'PermitController/delete/$1';
$route['permit/(:any)/download']['POST'] = 'PermitController/download/$1';
$route['permit/(:any)/preview']['POST'] = 'PermitController/preview/$1';

$route['policy/initiate']['GET'] = 'PolicyController/initiateIndex';
$route['policy/export']['POST'] = 'PolicyController/export';
$route['policy']['GET'] = 'PolicyController/index';
$route['policy/get-paginated']['GET'] = 'PolicyController/getPaginated';
$route['policy/(:any)']['GET'] = 'PolicyController/show/$1';
$route['policy']['POST'] = 'PolicyController/store';
$route['policy/(:any)']['POST'] = 'PolicyController/update/$1';
$route['policy/(:any)']['DELETE'] = 'PolicyController/delete/$1';
$route['policy/(:any)/download']['POST'] = 'PolicyController/download/$1';
$route['policy/(:any)/preview']['POST'] = 'PolicyController/preview/$1';

$route['procedure/initiate']['GET'] = 'ProcedureController/initiateIndex';
$route['procedure/export']['POST'] = 'ProcedureController/export';
$route['procedure']['GET'] = 'ProcedureController/index';
$route['procedure/get-paginated']['GET'] = 'ProcedureController/getPaginated';
$route['procedure/(:any)']['GET'] = 'ProcedureController/show/$1';
$route['procedure']['POST'] = 'ProcedureController/store';
$route['procedure/(:any)']['POST'] = 'ProcedureController/update/$1';
$route['procedure/(:any)']['DELETE'] = 'ProcedureController/delete/$1';
$route['procedure/(:any)/download']['POST'] = 'ProcedureController/download/$1';
$route['procedure/(:any)/preview']['POST'] = 'ProcedureController/preview/$1';

$route['score-card/initiate']['GET'] = 'ScoreCardController/initiateIndex';
$route['score-card/export']['POST'] = 'ScoreCardController/export';
$route['score-card']['GET'] = 'ScoreCardController/index';
$route['score-card/get-paginated']['GET'] = 'ScoreCardController/getPaginated';
$route['score-card/(:any)']['GET'] = 'ScoreCardController/show/$1';
$route['score-card']['POST'] = 'ScoreCardController/store';
$route['score-card/(:any)']['POST'] = 'ScoreCardController/update/$1';
$route['score-card/(:any)']['DELETE'] = 'ScoreCardController/delete/$1';
$route['score-card/(:any)/download']['POST'] = 'ScoreCardController/download/$1';
$route['score-card/(:any)/preview']['POST'] = 'ScoreCardController/preview/$1';

$route['work-instruction/initiate']['GET'] = 'WorkInstructionController/initiateIndex';
$route['work-instruction/export']['POST'] = 'WorkInstructionController/export';
$route['work-instruction']['GET'] = 'WorkInstructionController/index';
$route['work-instruction/get-paginated']['GET'] = 'WorkInstructionController/getPaginated';
$route['work-instruction/(:any)']['GET'] = 'WorkInstructionController/show/$1';
$route['work-instruction']['POST'] = 'WorkInstructionController/store';
$route['work-instruction/(:any)']['POST'] = 'WorkInstructionController/update/$1';
$route['work-instruction/(:any)']['DELETE'] = 'WorkInstructionController/delete/$1';
$route['work-instruction/(:any)/download']['POST'] = 'WorkInstructionController/download/$1';
$route['work-instruction/(:any)/preview']['POST'] = 'WorkInstructionController/preview/$1';

$route['certificate/initiate']['GET'] = 'CertificateController/initiateIndex';
$route['certificate/export']['POST'] = 'CertificateController/export';
$route['certificate']['GET'] = 'CertificateController/index';
$route['certificate/get-paginated']['GET'] = 'CertificateController/getPaginated';
$route['certificate/(:any)']['GET'] = 'CertificateController/show/$1';
$route['certificate']['POST'] = 'CertificateController/store';
$route['certificate/(:any)']['POST'] = 'CertificateController/update/$1';
$route['certificate/(:any)']['DELETE'] = 'CertificateController/delete/$1';
$route['certificate/(:any)/download']['POST'] = 'CertificateController/download/$1';
$route['certificate/(:any)/preview']['POST'] = 'CertificateController/preview/$1';
$route['cls/initiate']['GET'] = 'ClsController/initiateIndex';
$route['cls/export']['POST'] = 'ClsController/export';
$route['cls']['GET'] = 'ClsController/index';
$route['cls/get-paginated']['GET'] = 'ClsController/getPaginated';
$route['cls/(:any)']['GET'] = 'ClsController/show/$1';
$route['cls']['POST'] = 'ClsController/store';
$route['cls/(:any)']['POST'] = 'ClsController/update/$1';
$route['cls/(:any)']['DELETE'] = 'ClsController/delete/$1';
$route['cls/(:any)/download']['POST'] = 'ClsController/download/$1';
$route['cls/(:any)/preview']['POST'] = 'ClsController/preview/$1';
$route['coc/initiate']['GET'] = 'CocController/initiateIndex';
$route['coc/export']['POST'] = 'CocController/export';
$route['coc']['GET'] = 'CocController/index';
$route['coc/get-paginated']['GET'] = 'CocController/getPaginated';
$route['coc/(:any)']['GET'] = 'CocController/show/$1';
$route['coc']['POST'] = 'CocController/store';
$route['coc/(:any)']['POST'] = 'CocController/update/$1';
$route['coc/(:any)']['DELETE'] = 'CocController/delete/$1';
$route['coc/(:any)/download']['POST'] = 'CocController/download/$1';
$route['coc/(:any)/preview']['POST'] = 'CocController/preview/$1';
$route['management/initiate']['GET'] = 'ManagementController/initiateIndex';
$route['management/export']['POST'] = 'ManagementController/export';
$route['management']['GET'] = 'ManagementController/index';
$route['management/get-paginated']['GET'] = 'ManagementController/getPaginated';
$route['management/(:any)']['GET'] = 'ManagementController/show/$1';
$route['management']['POST'] = 'ManagementController/store';
$route['management/(:any)']['POST'] = 'ManagementController/update/$1';
$route['management/(:any)']['DELETE'] = 'ManagementController/delete/$1';
$route['management/(:any)/download']['POST'] = 'ManagementController/download/$1';
$route['management/(:any)/preview']['POST'] = 'ManagementController/preview/$1';
$route['dkpi/initiate']['GET'] = 'DkpiController/initiateIndex';
$route['dkpi/export']['POST'] = 'DkpiController/export';
$route['dkpi']['GET'] = 'DkpiController/index';
$route['dkpi/get-paginated']['GET'] = 'DkpiController/getPaginated';
$route['dkpi/(:any)']['GET'] = 'DkpiController/show/$1';
$route['dkpi']['POST'] = 'DkpiController/store';
$route['dkpi/(:any)']['POST'] = 'DkpiController/update/$1';
$route['dkpi/(:any)']['DELETE'] = 'DkpiController/delete/$1';
$route['dkpi/(:any)/download']['POST'] = 'DkpiController/download/$1';
$route['dkpi/(:any)/preview']['POST'] = 'DkpiController/preview/$1';
