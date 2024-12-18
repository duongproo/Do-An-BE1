<?php   
require_once 'Database.php';
    class Category extends Database{
            public function getAllCatogories()
            {
                $sql=parent::$connection->prepare('SELECT * FROM categories');
                return parent::select($sql);
                
            }
            public function addCategory($name)
            {
                $sql=parent::$connection->prepare('INSERT INTO categories(name)VALUES(?)');
                $sql->bind_param('s',$name);
                return $sql->execute();
            }
            public function editCategory($id,$name)
            {
                $sql=parent::$connection->prepare('UPDATE categories SET name=? where id=?');
                $sql->bind_param('si',$name,$id);
                return $sql->execute();
            }
            public function deleteCategory($id)
            {
                $sql=parent::$connection->prepare('DELETE FROM categories Where id=?');
                $sql->bind_param('i',$id);
                return $sql->execute();
            }
            public function getCategotyById($id)
            {
                $sql=parent::$connection->prepare('SELECT * FROM categories Where id=?');
                $sql->bind_param('i',$id);
                return parent::select($sql)[0];
            }


    }


?>