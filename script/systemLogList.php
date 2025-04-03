<?php
include_once('../inc/global.php');
include_once("../config/route.php");
$user = unserialize($_SESSION['user']);
$today = Utility\WebSystem::displayDate(date("Y-m-d H:i:s"), 'Y-m-d');

$map = [
    'column_systemLogID' => 'systemLog.id',
    'column_userDisplayName' => 'user.displayName',
    'column_logTime' => 'systemLog.logTime',
    'column_module' => 'systemLog.module',
    'column_action' => 'systemLog.action',
    'column_description' => 'systemLog.description',
    'column_ip' => 'systemLog.ip',
    'column_function' => 'systemLog.id'
];

$draw = $_POST['draw'];
$row = $_POST['start'];
$rowperpage = $_POST['length']; // Rows display per page
$columnIndex = $_POST['order'][0]['column']; // Column index
$columnName = $_POST['columns'][$columnIndex]['data']; // Column name
$columnSortOrder = $_POST['order'][0]['dir']; // asc or desc
$searchValue = $_POST['search']['value']; // Search value
$searchQuery = " ";

$sqlAll = Database\Sql::select(['systemLog', 'systemLog'])->leftJoin(['user', 'user'], "systemLog.userID = user.id");

$sqlAll->setFieldValue('
    systemLog.id systemLogID, 
    systemLog.logTime logTime,
    systemLog.module module,
    systemLog.action action,
    systemLog.description description,
    systemLog.ip ip,
    user.displayName userDisplayName    
');

if($searchValue != ''){
    $searchValue = addslashes($searchValue);
    $sqlAll->whereOp("(systemLog.id LIKE '%".$searchValue."%' 
        OR systemLog.logTime LIKE '%".$searchValue."%'
         OR systemLog.module LIKE '%".$searchValue."%'
          OR systemLog.action LIKE '%".$searchValue."%'
           OR systemLog.description LIKE '%".$searchValue."%'
            OR systemLog.ip LIKE '%".$searchValue."%'
             OR user.displayName LIKE '%".$searchValue."%'
    )");
}

foreach($_POST['columns'] as $idx => $column){
    if(!empty($column['search']['value'])) {
        $idxSearchValue = substr($column['search']['value'], 1, -1);        
        $sqlAll->where([$map[$_POST['columns'][$idx]['data']], '=', '"'.strip_tags($idxSearchValue).'"']);
    }
}

//echo $sqlAll; 

$stmAll = $sqlAll->prepare();
$stmAll->execute();

if($stmAll->rowCount()==0 && $filter) {
    $sqlAll = Database\Sql::select(['systemLog', 'systemLog'])->leftJoin(['user', 'user'], "systemLog.userID = user.id");

    $sqlAll->setFieldValue('
        systemLog.id systemLogID, 
        systemLog.logTime logTime,
        systemLog.module module,
        systemLog.action action,
        systemLog.description description,
        systemLog.ip ip,
        user.displayName userDisplayName    
    ');
};

$sql = $sqlAll->order($map[$columnName],$columnSortOrder)->limit($rowperpage, $row);

$stm = $sql->prepare();
$stm->execute();

$returnArr = [];
$contentArr = [];
$lineCount = 0;
foreach($stm as $data){    

    $dataArr = [
        "column_systemLogID"=>$data['systemLogID'],
        "column_userDisplayName"=>$data['userDisplayName'],
        "column_logTime"=>$data['logTime'],   
        "column_module"=>$data['module'],  
        "column_action"=>$data['action'],  
        "column_description"=>$data['description'],  
        "column_ip"=>$data['ip'],    
        "column_function"=>"
            <div class='btn-group' role='group' aria-label=''>
                <div class='btn-group' role='group'>
                    <button id='btnGroupDrop".$data['systemLogID']."' type='button' class='btn btn-outline-dark dropdown-toggle' data-bs-toggle='dropdown' aria-expanded='false'>
                        ".L('Actions')."
                    </button>
                <ul class='dropdown-menu' aria-labelledby='btnGroupDrop".$data['systemLogID']."'>
                    <li>
                        <div class='d-grid'>
                            <button class='btn btn-md btn-outline-dark btnView' type='button' data-id='".$data['systemLogID']."'><i class='fas fa-sm fa-eye'></i> ".L('View')."</button>                           
                        </div>
                    </li>
                </ul>
                </div>
            </div>
        "
    ];    

    $lineCount++;
    $contentArr[] = $dataArr;
}

$returnArr['draw'] = intval($draw);
$returnArr["iTotalDisplayRecords"] = $stmAll->rowCount();
$returnArr["iTotalRecords"] = $stmAll->rowCount();
$returnArr["data"] = $contentArr;

echo json_encode($returnArr);
?>