<?php
//error_reporting(E_ALL);
//ini_set('display_errors', 1);
include_once('../inc/global.php');
include_once("../config/route.php");
$user = unserialize($_SESSION['user']);
$today = Utility\WebSystem::displayDate(date("Y-m-d H:i:s"), 'Y-m-d');

$map = [
    'column_roleID' => 'role.id',
    'column_roleName' => 'role.name',
    'column_rolePermission' => 'GROUP_CONCAT(" ", navItems.itemName)',
    'column_roleStatus' => 'status.name',
    'column_function' => 'role.id'
];

$draw = $_POST['draw'];
$row = $_POST['start'];
$rowperpage = $_POST['length']; // Rows display per page
$columnIndex = $_POST['order'][0]['column']; // Column index
$columnName = $_POST['columns'][$columnIndex]['data']; // Column name
$columnSortOrder = $_POST['order'][0]['dir']; // asc or desc
$searchValue = $_POST['search']['value']; // Search value
$searchQuery = " ";

$sqlAll = Database\Sql::select(['role', 'role'])
->leftJoin(['status', 'status'], "role.status = status.id")
->leftJoin(['rolePermission', 'permission'], "role.id = permission.roleID")
->leftJoin(['navItems', 'navItems'], "navItems.id = permission.navItemID");

$sqlAll->setFieldValue('
    role.id roleID, 
    role.name roleName,  
    status.name statusName
');

if($searchValue != ''){
    $searchValue = addslashes($searchValue);
    $sqlAll->whereOp("(role.id LIKE '%".$searchValue."%' 
        OR role.name LIKE '%".$searchValue."%'
        OR status.name LIKE '%".$searchValue."%' 
        OR navItems.itemName LIKE '%".$searchValue."%' 
    )");
}

$filter = false;
foreach($_POST['columns'] as $idx => $column){
    if(!empty($column['search']['value'])) {
        $filter = true;
        $idxSearchValue = substr($column['search']['value'], 1, -1);
        if($_POST['columns'][$idx]['data']=="column_roleStatus"){
           if($idxSearchValue=="有效") {
             $idxSearchValue="Enabled";
           }

           if($idxSearchValue=="無效") {
             $idxSearchValue="Disabled";
           }           
        } 

        $sqlAll->where([$map[$_POST['columns'][$idx]['data']], '=', '"'.strip_tags($idxSearchValue).'"']);
    }
}

$sqlAll->where(['1', 'GROUP BY', 'role.id']);

$stmAll = $sqlAll->prepare();
$stmAll->execute();

if($stmAll->rowCount()==0 && $filter) {
    $sqlAll = Database\Sql::select(['role', 'role'])->leftJoin(['status', 'status'], "role.status = status.id")->leftJoin(['rolePermission', 'permission'], "role.id = permission.roleID")->leftJoin(['navItems', 'navItems'], "navItems.id = permission.navItemID");
    
    $sqlAll->setFieldValue('
        role.id roleID, 
        role.name roleName,  
        status.name statusName                  
    ');    

    $sqlAll->where(['1', 'GROUP BY', 'role.id']);
};

$sql = $sqlAll->order($map[$columnName],$columnSortOrder)->limit($rowperpage, $row);

$stm = $sql->prepare();
$stm->execute();

$returnArr = [];
$contentArr = [];
$lineCount = 0;
foreach($stm as $data){

    $permissionList = Controller\role::findPermission($data['roleID']);
    $arrItemName = [];
    foreach($permissionList as $permission) {
        $arrItemName[] = Controller\role::findNavItem($permission['navItemID'])->itemName;
    }

    $dataArr = [
        "column_roleID"=>$data['roleID'],
        "column_roleName"=>$data['roleName'], 
        "column_rolePermission"=>implode(", ", $arrItemName), 
        "column_roleStatus"=>L($data['statusName']),  
        "column_function"=>"
            <div class='btn-group' role='group' aria-label=''>
                <div class='btn-group' role='group'>
                    <button id='btnGroupDrop".$data['roleID']."' type='button' class='btn btn-outline-dark dropdown-toggle' data-bs-toggle='dropdown' aria-expanded='false'>
                        ".L('Actions')."
                    </button>
                <ul class='dropdown-menu' aria-labelledby='btnGroupDrop".$data['roleID']."'>
                    <li>
                        <div class='d-grid'>
                            <button class='btn btn-md btn-outline-dark btnView' type='button' data-id='".$data['roleID']."'><i class='fas fa-sm fa-eye'></i> ".L('View')."</button>
                            <button class='btn btn-md btn-outline-dark btnEdit' type='button' data-id='".$data['roleID']."'><i class='fas fa-sm fa-edit'></i> ".L('Edit')."</button>
                            <button class='btn btn-md btn-outline-dark btnDel' type='button' data-id='".$data['roleID']."'><i class='fas fa-sm fa-trash-alt'></i> ".L('Delete')."</button>
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