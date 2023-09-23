<?php

class Category
{
    protected $db;              //Holds ref to Database object

    public function __construct(Database $db)
    {
        $this->db = $db;        //Add ref to Database object
    }

    //Get individual category
    public function get(int $id)
    {
        $sql = "SELECT id, name, description, navigation
                    FROM category
                WHERE id = :id;";
        return $this->db->runSQL($sql, [$id])->fetch();

    }

    // Get all categories
    public function getAll():   array
    {
        $sql = "SELECT id, name, navigation
                FROM category;";                        //SQL to get all categories
        return $this->db->runSQL($sql)->fetchAll();     //Return all categories


    }

    //ADMIN METHODS
    // Get number of categories
    public function count(): int
    {
        $sql = "SELECT COUNT(id) FROM category;";  //SQL to count categories
        return $this->db->runSQL($sql)->fetchColumn();
    }

    //Create new category
    public function create(array $category): bool
    {
        try{
            $sql = "INSERT INTO category(name, description, navigation)
                    VALUES (:name, :description, :navigation);";        //SQL to add new category
            $this->db->runSQL($sql, $category);         //Add new category
            return true;                                //If worked, return true
        } catch (PDOException $e){                      //If an exception was thrown
            if($e->errorInfo[1] === 1062) {             //If error indicates duplicate entry
                return false;                           //Return false to indicate duplicate name
            } else {                                    //Otherwise
                throw $e;                               //Re-throw exception
            }
        }
    }

    // Update existing category
    public function update(array $category): bool
    {
        try {                                                   //Try to update category
            $sql = "UPDATE category 
                    SET name= :name, description = :description, navigation = :navigation
                    WHERE id = :id;";                           //SQL to update category
            $this->db->runSQL($sql, $category);                 //Update category
            return true;                                        //If worked return true
        } catch(PDOException $e) {                              //If an exception was thrown
            if($e->errorInfo[1] === 1062){                      //If error indicates duplicate entry
                return false;                                   //Return false to indicate duplicate entry
            } else {                                            //Otherwise
                throw $e;                                       //Re-throw exception
            }
        }
    }
}
