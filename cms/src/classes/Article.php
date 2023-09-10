<?php

class Article
{
    protected $db;                  //Holds ref to Database object

    public function __construct(Database $db)
    {
        $this->db = $db;                //Store ref to Database object

    }

    public function get(int $id, bool $published =  true)
    {
        $sql = "SELECT a.id, a.title, a.summary, a.content, a.created, a.category_id, 
                       a.member_id, a.published,
                       c.name     AS category,
                       CONCAT(m.forename, ' ', m.surname) AS author,
                       i.id       AS image_id, 
                       i.file     AS image_file, 
                       i.alt      AS image_alt 
                  FROM article    AS a
                  JOIN category   AS c ON a.category_id = c.id
                  JOIN member     AS m ON a.member_id   = m.id
                  LEFT JOIN image AS i ON a.image_id    = i.id
                 WHERE a.id = :id ";                //SQL statement

        if($published) {                           //If must be published
            $sql .= "AND a.published = 1;";        //Add clause to SQL
        }
        return $this->db->runSQL($sql, [$id])->fetch(); // Return article

    }

    public function getAll($published = true, $category = null, $member = null, $limit = 10000): array
    {
        $arguments['category'] = $category;     //Category id
        $arguments['category1'] = $category;    //Category id
        $arguments['member'] = $member;     //Author id
        $arguments['member1'] = $member; //Author id
        $arguments['limit'] = $limit;   //Max articles to return

        $sql = "SELECT a.id, a.title, a.summary, a.created, a.category_id,
                       a.member_id, a.published,
                       c.name     AS category,
                       CONCAT(m.forename, ' ', m.surname) AS author,
                       i.file     AS image_file,
                       i.alt      AS image_alt
               FROM article  AS a
               JOIN category AS c ON a.category_id = c.id
               JOIN member   AS m ON a.member_id = m.id
               LEFT JOIN image AS i ON a.image_id = i.id
           
            WHERE (a.category_id = :category OR :category1 is null)
                    AND(a.member_id = :member OR :member1 is null) "; //SQL for article summary

        if($published) {                            //If must be published
            $sql .= "AND a.published = 1 ";         //Add clause to SQL
        }
        $sql .= "ORDER BY a.id DESC LIMIT :limit;";        //Add: max articles to return

        return $this->db->runSQL($sql, $arguments)->fetchAll(); // Return data

    }


}
