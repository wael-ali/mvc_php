<?php

namespace app\Entity;

class User
{
    /**
     * @DB:Col(type:int, primary_key:true)
     */
    private $id;
    /**
     * @DB:Col(type:string, length:255, nullable:false)
     */
    private $firstname;
    /**
     * @DB:Col(type:string, length:255, nullable:false)
     */
    private $lastname;
    /**
     * @DB:Col(type:string, length:255, nullable:false)
     */
    private $email;
    /**
     * @DB:Col(type:date, nullable:false)
     */
    private $reg_date;
    /**
     * @DB:Col(type:date, nullable:true)
     */
    private $lastUpdate;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id): void
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getFirstname()
    {
        return $this->firstname;
    }

    /**
     * @param mixed $firstname
     */
    public function setFirstname($firstname): void
    {
        $this->firstname = $firstname;
    }

    /**
     * @return mixed
     */
    public function getLastname()
    {
        return $this->lastname;
    }

    /**
     * @param mixed $lastname
     */
    public function setLastname($lastname): void
    {
        $this->lastname = $lastname;
    }

    /**
     * @return mixed
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param mixed $email
     */
    public function setEmail($email): void
    {
        $this->email = $email;
    }

    /**
     * @return mixed
     */
    public function getRegDate()
    {
        return $this->reg_date;
    }

    /**
     * @param mixed $reg_date
     */
    public function setRegDate($reg_date): void
    {
        $this->reg_date = $reg_date;
    }

    /**
     * @return mixed
     */
    public function getLastUpdate()
    {
        return $this->lastUpdate;
    }

    /**
     * @param mixed $lastUpdate
     */
    public function setLastUpdate($lastUpdate): void
    {
        $this->lastUpdate = $lastUpdate;
    }

    public function fillObjFromArray(array $userData)
    {
         foreach ($userData as $key => $value){
             if (property_exists(__CLASS__, $key)){
                 $this->$key = $value;
             }
         }
    }

    public function getName()
    {
        return $this->firstname.' '.$this->lastname;
    }
    public function __toString()
    {
        return $this->getName();
    }

}