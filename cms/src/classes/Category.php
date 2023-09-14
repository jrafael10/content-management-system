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







}
