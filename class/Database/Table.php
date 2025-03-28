<?php
namespace Database;

class Table {
   private $table;
   private $dbIdx;

   public function _construct($tableName, $dbIdx=0) {
      $this->table = $tableName;
      $this->dbIdx = $dbIdx;
   }

   public function getAll() {
      $sql = Sql::select($this->table);
      $stm = $sql->prepare($this->dbIdx);
      $stm->setFetchMode(\PDO::FETCH_OBJ);
      $stm->execute();
      foreach ($stm as $row) {
         yield $row;
      }
   }

   public function getById($id) {
      $sql = Sql::select($this->table)->where(['id', '=', $id]);
      $stm = $sql->prepare($this->dbIdx);
      $stm->execute();
      $obj = $stm->fetch(\PDO::FETCH_OBJ);
      return $obj;
   }

   public function deleteById($id) {
      $sql = Sql::delete($this->table)->where(['id', '=', $id]);
      $stm = $sql->prepare($this->dbIdx);
      return $stm->execute();
   }

   public function insertDb($dataField) {
      $sql = Sql::insert($this->table)->setFieldValue($dataField);
      $stm = $sql->prepare($this->dbIdx);
      if ($stm->execute())
         return db($this->dbIdx)->lastInsertId();
      return false;
   }

   public function updateDb($dataField) {
      $sql = Sql::update($this->table)->setFieldValue($dataField);
      $stm = $sql->prepare($this->dbIdx);
      if ($stm->execute())
         return true;
      return false;
   }
}