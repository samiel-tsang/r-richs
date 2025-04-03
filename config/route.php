<?php
use Routing\Route;

/* API Function */
//Route::add('GET', '/api/card/{id}', 'Controller\api@cardUrl', 'api.cardUrl');


// main page //
Route::add('GET', '/api/mainPage/getCalendarDateByMonth/{month}', 'Controller\mainPage@getCalendarDateByMonth', 'mainPage.getCalendarDateByMonth');

// system setting//
Route::add('POST', '/api/systemSetting/update', 'Controller\systemSetting@update', 'systemSetting.update');

// document //
Route::add('GET', '/api/document/detail/{id}', 'Controller\documentHelper@detail', 'document.detail');
Route::add('DELETE', '/api/document/{id}', 'Controller\documentHelper@delete', 'document.delete');

// System User //
Route::add('POST', '/api/user/removeDoc/{id}', 'Controller\user@removeDoc', 'user.removeDoc');
Route::add('GET', '/api/user/formAdd', 'Controller\user@userForm', 'user.userFormAdd'); // in page form
Route::add('GET', '/api/user/formEdit/{id}', 'Controller\user@userForm', 'user.userFormEdit'); // in page form
Route::add('POST', '/api/user/forget', 'Controller\user@forget', 'user.forget'); // send change password email
Route::add('POST', '/api/user/resetpassword/{id}', 'Controller\user@resetpassword', 'user.resetpassword');
Route::add('POST', '/api/user/login', 'Controller\user@login', 'user.login');
Route::add('GET', '/api/user/logout', 'Controller\user@logout', 'user.logout');
Route::add('POST', '/api/user/search', 'Controller\user@search', 'user.search');
Route::add('POST', '/api/user/register', 'Controller\user@register', 'user.register');
Route::add('POST', '/api/user', 'Controller\user@add', 'user.add');
Route::add('POST', '/api/user/{id}', 'Controller\user@edit', 'user.edit');
Route::add('DELETE', '/api/user/{id}', 'Controller\user@delete', 'user.delete');
Route::add('GET', '/api/user/detail/{id}', 'Controller\user@detail', 'user.detail');

// client //
Route::add('GET', '/api/client/detail/{id}', 'Controller\client@detail', 'client.detail');
Route::add('POST', '/api/client/removeDoc/{id}', 'Controller\client@removeDoc', 'client.removeDoc');
Route::add('GET', '/api/client/formAdd', 'Controller\client@clientForm', 'client.clientFormAdd'); // in page form
Route::add('GET', '/api/client/formEdit/{id}', 'Controller\client@clientForm', 'client.clientFormEdit'); // in page form
Route::add('POST', '/api/client', 'Controller\client@add', 'client.add');
Route::add('POST', '/api/client/{id}', 'Controller\client@edit', 'client.edit');
Route::add('DELETE', '/api/client/{id}', 'Controller\client@delete', 'client.delete');

// zoning //
Route::add('POST', '/api/zoning/removeDoc/{id}', 'Controller\zoning@removeDoc', 'zoning.removeDoc');
Route::add('GET', '/api/zoning/formAdd', 'Controller\zoning@zoningForm', 'zoning.zoningFormAdd'); // in page form
Route::add('GET', '/api/zoning/formEdit/{id}', 'Controller\zoning@zoningForm', 'zoning.zoningFormEdit'); // in page form
Route::add('POST', '/api/zoning', 'Controller\zoning@add', 'zoning.add');
Route::add('POST', '/api/zoning/{id}', 'Controller\zoning@edit', 'zoning.edit');
Route::add('DELETE', '/api/zoning/{id}', 'Controller\zoning@delete', 'zoning.delete');
Route::add('GET', '/api/zoning/detail/{id}', 'Controller\zoning@detail', 'zoning.detail');

// RNTPC //
Route::add('GET', '/api/rntpc/getMeetingDateByMonth/{month}', 'Controller\rntpc@getMeetingDateByMonth', 'rntpc.getMeetingDateByMonth');
Route::add('POST', '/api/rntpc/removeDoc/{id}', 'Controller\rntpc@removeDoc', 'rntpc.removeDoc');
Route::add('GET', '/api/rntpc/formAdd', 'Controller\rntpc@rntpcForm', 'rntpc.rntpcFormAdd'); // in page form
Route::add('GET', '/api/rntpc/formEdit/{id}', 'Controller\rntpc@rntpcForm', 'rntpc.rntpcFormEdit'); // in page form
Route::add('POST', '/api/rntpc', 'Controller\rntpc@add', 'rntpc.add');
Route::add('POST', '/api/rntpc/{id}', 'Controller\rntpc@edit', 'rntpc.edit');
Route::add('DELETE', '/api/rntpc/{id}', 'Controller\rntpc@delete', 'rntpc.delete');
Route::add('GET', '/api/rntpc/detail/{id}', 'Controller\rntpc@detail', 'rntpc.detail');

// DBM //
Route::add('GET', '/api/dbm/getMeetingDateByMonth/{month}', 'Controller\dbm@getMeetingDateByMonth', 'dbm.getMeetingDateByMonth');
Route::add('POST', '/api/dbm/removeDoc/{id}', 'Controller\dbm@removeDoc', 'dbm.removeDoc');
Route::add('GET', '/api/dbm/formAdd', 'Controller\dbm@dbmForm', 'dbm.dbmFormAdd'); // in page form
Route::add('GET', '/api/dbm/formEdit/{id}', 'Controller\dbm@dbmForm', 'dbm.dbmFormEdit'); // in page form
Route::add('POST', '/api/dbm', 'Controller\dbm@add', 'dbm.add');
Route::add('POST', '/api/dbm/{id}', 'Controller\dbm@edit', 'dbm.edit');
Route::add('DELETE', '/api/dbm/{id}', 'Controller\dbm@delete', 'dbm.delete');
Route::add('GET', '/api/dbm/detail/{id}', 'Controller\dbm@detail', 'dbm.detail');

// TPB //
Route::add('POST', '/api/tpb/getConditionDateByMonth/{month}', 'Controller\tpb@getConditionDateByMonth', 'tpb.getConditionDateByMonth');
Route::add('POST', '/api/tpb/removeDoc/{id}', 'Controller\tpb@removeDoc', 'tpb.removeDoc');
//Route::add('GET', '/api/tpb/formAdd', 'Controller\tpb@tpbForm', 'tpb.tpbFormAdd'); // in page form old
//Route::add('GET', '/api/tpb/formEdit/{id}', 'Controller\tpb@tpbForm', 'tpb.tpbFormEdit'); // in page form old
Route::add('GET', '/api/tpb/formAdd', 'Controller\tpb@tpbFormAdd', 'tpb.tpbFormAdd'); // in page form
Route::add('GET', '/api/tpb/formEdit/{id}', 'Controller\tpb@tpbFormEdit', 'tpb.tpbFormEdit'); // in page form
Route::add('POST', '/api/tpb', 'Controller\tpb@add', 'tpb.add');
Route::add('POST', '/api/tpb/{id}', 'Controller\tpb@edit', 'tpb.edit');
Route::add('DELETE', '/api/tpb/{id}', 'Controller\tpb@delete', 'tpb.delete');
Route::add('GET', '/api/tpb/detail/{id}', 'Controller\tpb@detail', 'tpb.detail'); // in page form
Route::add('GET', '/api/tpb/getStatusCount', 'Controller\tpb@getStatusCount', 'tpb.getStatusCount');

Route::add('POST', '/api/tpb/receiveEdit/{id}', 'Controller\tpb@receiveEdit', 'tpb.receiveEdit');
Route::add('POST', '/api/tpb/decisionEdit/{id}', 'Controller\tpb@decisionEdit', 'tpb.decisionEdit');
Route::add('POST', '/api/tpb/conditionEdit/{id}', 'Controller\tpb@conditionEdit', 'tpb.conditionEdit');
Route::add('POST', '/api/tpb/EOTEdit/{id}', 'Controller\tpb@EOTEdit', 'tpb.EOTEdit');

Route::add('POST', '/api/tpb/applicantEdit/{id}', 'Controller\tpb@applicantEdit', 'tpb.applicantEdit');
Route::add('POST', '/api/tpb/applicationEdit/{id}', 'Controller\tpb@applicationEdit', 'tpb.applicationEdit');
Route::add('POST', '/api/tpb/submissionEdit/{id}', 'Controller\tpb@submissionEdit', 'tpb.submissionEdit');

// EOT //
Route::add('POST', '/api/eot', 'Controller\tpb@eotAdd', 'tpb.eotAdd');
Route::add('POST', '/api/eot/removeDoc/{id}', 'Controller\tpb@removeEOTDoc', 'tpb.removeEOTDoc');
Route::add('GET', '/api/eot/formAdd/{id}', 'Controller\tpb@eotFormAdd', 'tpb.eotFormAdd'); // in page form
Route::add('GET', '/api/eot/formEdit/{id}', 'Controller\tpb@eotFormEdit', 'tpb.eotFormEdit'); // in page form
Route::add('POST', '/api/eot/{id}', 'Controller\tpb@eotEdit', 'tpb.eotEdit');
Route::add('DELETE', '/api/eot/{id}', 'Controller\tpb@eotDelete', 'tpb.eotDelete');
Route::add('GET', '/api/eot/detail/{id}', 'Controller\tpb@eotDetail', 'tpb.eotDetail');

// Condition //
Route::add('GET', '/api/condition/formAdd/{id}', 'Controller\tpb@conditionFormAdd', 'tpb.conditionFormAdd'); // in page form
Route::add('GET', '/api/condition/formEdit/{id}', 'Controller\tpb@conditionFormEdit', 'tpb.conditionFormEdit'); // in page form
Route::add('POST', '/api/condition', 'Controller\tpb@conditionAdd', 'tpb.conditionFormAdd');
Route::add('POST', '/api/condition/{id}', 'Controller\tpb@conditionEdit', 'tpb.conditionEdit');
Route::add('DELETE', '/api/condition/{id}', 'Controller\tpb@conditionDelete', 'tpb.conditionDelete');
Route::add('GET', '/api/condition/detail/{id}', 'Controller\tpb@conditionDetail', 'tpb.conditionDetail');

// TPB single function //
/*
Route::add('GET', '/api/tpb/followUpFormEdit/{id}', 'Controller\tpb@followUpTpbForm', 'tpb.followUpTpbFormEdit'); // in page form
Route::add('POST', '/api/tpb/followUp/{id}', 'Controller\tpb@followUpEdit', 'tpb.followUpEdit');
Route::add('GET', '/api/tpb/receiveFormEdit/{id}', 'Controller\tpb@receiveTpbForm', 'tpb.receiveTpbFormEdit'); // in page form
Route::add('POST', '/api/tpb/receive/{id}', 'Controller\tpb@receiveEdit', 'tpb.receiveEdit');
Route::add('GET', '/api/tpb/decisionFormEdit/{id}', 'Controller\tpb@decisionTpbForm', 'tpb.decisionTpbFormEdit'); // in page form
Route::add('POST', '/api/tpb/decision/{id}', 'Controller\tpb@decisionEdit', 'tpb.decisionEdit');
*/

// Task //
Route::add('POST', '/api/task/done/{id}', 'Controller\task@done', 'task.done');
Route::add('POST', '/api/task/removeDoc/{id}', 'Controller\task@removeDoc', 'task.removeDoc');
Route::add('GET', '/api/task/formAdd', 'Controller\task@taskForm', 'task.taskFormAdd'); // in page form
Route::add('GET', '/api/task/formEdit/{id}', 'Controller\task@taskForm', 'task.taskFormEdit'); // in page form
Route::add('POST', '/api/task', 'Controller\task@add', 'task.add');
Route::add('POST', '/api/task/{id}', 'Controller\task@edit', 'task.edit');
Route::add('DELETE', '/api/task/{id}', 'Controller\task@delete', 'task.delete');
Route::add('GET', '/api/task/detail/{id}', 'Controller\task@detail', 'task.detail');

// STT //
Route::add('POST', '/api/stt/mailingLog', 'Controller\stt@mailingLogAdd', 'stt.mailingLogAdd');
Route::add('POST', '/api/stt/mailingLog/{id}', 'Controller\stt@mailingLogEdit', 'stt.mailingLogEdit');
Route::add('DELETE', '/api/stt/mailingLog/{id}', 'Controller\stt@mailingLogDelete', 'stt.mailingLogDelete');
Route::add('POST', '/api/stt/removeDoc/{id}', 'Controller\stt@removeDoc', 'stt.removeDoc');
Route::add('GET', '/api/stt/formAdd', 'Controller\stt@sttForm', 'stt.sttFormAdd'); // in page form
Route::add('POST', '/api/stt/formAdd', 'Controller\stt@sttForm', 'stt.sttFormAdd'); // in page form
Route::add('GET', '/api/stt/formEdit/{id}', 'Controller\stt@sttForm', 'stt.sttFormEdit'); // in page form
Route::add('POST', '/api/stt/formEdit/{id}', 'Controller\stt@sttForm', 'stt.sttFormEdit'); // in page form
Route::add('POST', '/api/stt', 'Controller\stt@add', 'stt.add');
Route::add('POST', '/api/stt/{id}', 'Controller\stt@edit', 'stt.edit');
Route::add('DELETE', '/api/stt/{id}', 'Controller\stt@delete', 'stt.delete');
Route::add('GET', '/api/stt/mailingLogFormAdd/{id}', 'Controller\stt@mailingLogFormAdd', 'stt.mailingLogFormAdd');
Route::add('GET', '/api/stt/mailingLogFormEdit/{id}', 'Controller\stt@mailingLogFormEdit', 'stt.mailingLogFormEdit');
Route::add('GET', '/api/stt/detail/{id}', 'Controller\stt@detail', 'stt.detail');
Route::add('GET', '/api/stt/mailingLog/detail/{id}', 'Controller\stt@mailingLogDetail', 'stt.mailingLogDetail');

// STW //
Route::add('POST', '/api/stw/mailingLog', 'Controller\stw@mailingLogAdd', 'stw.mailingLogAdd');
Route::add('POST', '/api/stw/mailingLog/{id}', 'Controller\stw@mailingLogEdit', 'sstwtt.mailingLogEdit');
Route::add('DELETE', '/api/stw/mailingLog/{id}', 'Controller\stw@mailingLogDelete', 'stw.mailingLogDelete');
Route::add('POST', '/api/stw/removeDoc/{id}', 'Controller\stw@removeDoc', 'stw.removeDoc');
Route::add('GET', '/api/stw/formAdd', 'Controller\stw@stwForm', 'stw.stwFormAdd'); // in page form
Route::add('POST', '/api/stw/formAdd', 'Controller\stw@stwForm', 'stw.stwFormAdd'); // in page form
Route::add('GET', '/api/stw/formEdit/{id}', 'Controller\stw@stwForm', 'stw.stwFormEdit'); // in page form
Route::add('POST', '/api/stw/formEdit/{id}', 'Controller\stw@stwForm', 'stw.stwFormEdit'); // in page form
Route::add('POST', '/api/stw', 'Controller\stw@add', 'stw.add');
Route::add('POST', '/api/stw/{id}', 'Controller\stw@edit', 'stw.edit');
Route::add('DELETE', '/api/stw/{id}', 'Controller\stw@delete', 'stw.delete');
Route::add('GET', '/api/stw/mailingLogFormAdd/{id}', 'Controller\stw@mailingLogFormAdd', 'stw.mailingLogFormAdd');
Route::add('GET', '/api/stw/mailingLogFormEdit/{id}', 'Controller\stw@mailingLogFormEdit', 'stw.mailingLogFormEdit');
Route::add('GET', '/api/stw/detail/{id}', 'Controller\stw@detail', 'stw.detail');
Route::add('GET', '/api/stw/mailingLog/detail/{id}', 'Controller\stw@mailingLogDetail', 'stw.mailingLogDetail');

// Client Type //
Route::add('POST', '/api/clientType/removeDoc/{id}', 'Controller\clientType@removeDoc', 'clientType.removeDoc');
Route::add('GET', '/api/clientType/formAdd', 'Controller\clientType@clientTypeForm', 'clientType.clientTypeFormAdd'); // in page form
Route::add('GET', '/api/clientType/formEdit/{id}', 'Controller\clientType@clientTypeForm', 'clientType.clientTypeFormEdit'); // in page form
Route::add('POST', '/api/clientType', 'Controller\clientType@add', 'clientType.add');
Route::add('POST', '/api/clientType/{id}', 'Controller\clientType@edit', 'clientType.edit');
Route::add('DELETE', '/api/clientType/{id}', 'Controller\clientType@delete', 'clientType.delete');
Route::add('GET', '/api/clientType/detail/{id}', 'Controller\clientType@detail', 'clientType.detail');

// Submission Mode //
Route::add('POST', '/api/submissionMode/removeDoc/{id}', 'Controller\submissionMode@removeDoc', 'submissionMode.removeDoc');
Route::add('GET', '/api/submissionMode/formAdd', 'Controller\submissionMode@submissionModeForm', 'submissionMode.submissionModeFormAdd'); // in page form
Route::add('GET', '/api/submissionMode/formEdit/{id}', 'Controller\submissionMode@submissionModeForm', 'submissionMode.submissionModeFormEdit'); // in page form
Route::add('POST', '/api/submissionMode', 'Controller\submissionMode@add', 'submissionMode.add');
Route::add('POST', '/api/submissionMode/{id}', 'Controller\submissionMode@edit', 'submissionMode.edit');
Route::add('DELETE', '/api/submissionMode/{id}', 'Controller\submissionMode@delete', 'submissionMode.delete');
Route::add('GET', '/api/submissionMode/detail/{id}', 'Controller\submissionMode@detail', 'submissionMode.detail');

// role //
Route::add('POST', '/api/role/removeDoc/{id}', 'Controller\role@removeDoc', 'role.removeDoc');
Route::add('GET', '/api/role/formAdd', 'Controller\role@roleForm', 'role.roleFormAdd'); // in page form
Route::add('GET', '/api/role/formEdit/{id}', 'Controller\role@roleForm', 'role.roleFormEdit'); // in page form
Route::add('POST', '/api/role', 'Controller\role@add', 'role.add');
Route::add('POST', '/api/role/{id}', 'Controller\role@edit', 'role.edit');
Route::add('DELETE', '/api/role/{id}', 'Controller\role@delete', 'role.delete');
Route::add('GET', '/api/role/detail/{id}', 'Controller\role@detail', 'role.detail');

// email template
Route::add('GET', '/api/emailTemplate/variableList', 'Controller\emailTemplate@variableList', 'emailTemplate.variableList');
Route::add('GET', '/api/emailTemplate/formAdd', 'Controller\emailTemplate@emailTemplateForm', 'emailTemplate.emailTemplateAdd'); // in page form
Route::add('GET', '/api/emailTemplate/formEdit/{id}', 'Controller\emailTemplate@emailTemplateForm', 'emailTemplate.emailTemplateEdit'); // in page form
Route::add('POST', '/api/emailTemplate', 'Controller\emailTemplate@add', 'emailTemplate.add');
Route::add('POST', '/api/emailTemplate/{id}', 'Controller\emailTemplate@edit', 'emailTemplate.edit');
Route::add('DELETE', '/api/emailTemplate/{id}', 'Controller\emailTemplate@delete', 'remailTemplateole.delete');
Route::add('GET', '/api/emailTemplate/detail/{id}', 'Controller\emailTemplate@detail', 'emailTemplate.detail');

// system setting
Route::add('POST', '/api/systemSetting', 'Controller\systemSetting@edit', 'systemSetting.edit');

// system log //
Route::add('GET', '/api/systemLog/detail/{id}', 'Controller\systemLog@detail', 'systemLog.detail');
Route::add('POST', '/api/systemLog', 'Controller\systemLog@add', 'systemLog.add');
/*
Route::add('POST', '/api/systemLog/removeDoc/{id}', 'Controller\systemLog@removeDoc', 'systemLog.removeDoc');
Route::add('GET', '/api/systemLog/formAdd', 'Controller\systemLog@systemLogForm', 'systemLog.systemLogFormAdd'); // in page form
Route::add('GET', '/api/systemLog/formEdit/{id}', 'Controller\systemLog@systemLogForm', 'systemLog.systemLogFormEdit'); // in page form
Route::add('POST', '/api/systemLog/{id}', 'Controller\systemLog@edit', 'systemLog.edit');
Route::add('DELETE', '/api/systemLog/{id}', 'Controller\systemLog@delete', 'systemLog.delete');
*/

/* Page Function */
Route::add('GET', '/', 'Controller\mainPage@login', 'page.login');
Route::add('GET', '/main', 'Controller\mainPage@main', 'page.dashboard');
Route::add('GET', '/qrcode/{code}', 'Controller\mainPage@genQRCode', 'page.genQRCode');

// user External //
Route::add('GET', '/user/forgetpassword', 'Controller\mainPage@forgetpassword', 'page.userForgetpassword');
Route::add('GET', '/user/changepassword', 'Controller\mainPage@changepassword', 'page.userChangepassword');


// user //
Route::add('GET', '/user', 'Controller\user@list', 'page.userList');
Route::add('GET', '/user/add', 'Controller\user@form', 'page.userAdd');
Route::add('GET', '/user/edit/{id}', 'Controller\user@form', 'page.userEdit');
Route::add('GET', '/user/search', 'Controller\user@searchform', 'page.userSearch');
Route::add('GET', '/user/{id}', 'Controller\user@info', 'page.userInfo');

// client //
Route::add('GET', '/client', 'Controller\client@list', 'page.clientList');

// zoning //
Route::add('GET', '/zoning', 'Controller\zoning@list', 'page.zoningList');

// rntpc //
Route::add('GET', '/rntpc', 'Controller\rntpc@list', 'page.rntpcList');

// dbm //
Route::add('GET', '/dbm', 'Controller\dbm@list', 'page.dbmList');

// tpb //
Route::add('GET', '/tpb', 'Controller\tpb@list', 'page.tpbList');

// EOT //
Route::add('GET', '/eot', 'Controller\eot@list', 'page.eotList');

// task //
Route::add('GET', '/task', 'Controller\task@list', 'page.taskList');

// task //
Route::add('GET', '/stt', 'Controller\stt@list', 'page.sttList');

// task //
Route::add('GET', '/stw', 'Controller\stw@list', 'page.stwList');

// client Type //
Route::add('GET', '/clientType', 'Controller\clientType@list', 'page.clientTypeList');

// submission mode //
Route::add('GET', '/submissionMode', 'Controller\submissionMode@list', 'page.submissionModeList');

// role //
Route::add('GET', '/role', 'Controller\role@list', 'page.roleList');

// email template //
Route::add('GET', '/emailTemplate', 'Controller\emailTemplate@list', 'page.emailTemplateList');

// system Setting //
Route::add('GET', '/systemSetting', 'Controller\systemSetting@list', 'page.systemSettingList');

// system Log //
Route::add('GET', '/systemLog', 'Controller\systemLog@list', 'page.systemLogList');

// test //
Route::add('GET', '/test', 'Controller\test@list', 'page.testList');