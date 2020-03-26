<?php


namespace app\Service;


class DbConnection
{
    const CONFIGRATION_KEYS = [
        'host',
        'username',
        'password',
        'dbname',
    ];
    private $host = null;
    private $username = null;
    private $password = null;
    private $dbname = null;
    private $dbConnection = null;

    public function __construct(array $dbConfigration)
    {
        $this->setConfigrationsFromArray($dbConfigration);
    }

    public function setConfigrationsFromArray(array $configrationsArray)
    {
        foreach($configrationsArray as $key => $value){
            if (in_array($key, self::CONFIGRATION_KEYS)){
                $this->$key = $value;
            }else{
                throw new \Exception($key.' is not a valid key for database configration array');
            }
        }
        $this->generateDbConnection();
    }

    /**
     * @return mixed
     */
    public function getHost()
    {
        return $this->host;
    }

    /**
     * @param mixed $host
     */
    public function setHost($host)
    {
        $this->host = $host;
    }

    /**
     * @return mixed
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @param mixed $username
     */
    public function setUsername($username)
    {
        $this->username = $username;
    }

    /**
     * @return mixed
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @param mixed $password
     */
    public function setPassword($password)
    {
        $this->password = $password;
    }

    /**
     * @return mixed
     */
    public function getDbname()
    {
        return $this->dbname;
    }

    /**
     * @param mixed $dbname
     */
    public function setDbname($dbname)
    {
        $this->dbname = $dbname;
    }

    /**
     * @return null
     */
    public function getDbConnection()
    {
        return $this->dbConnection;
    }

    /**
     * @param null $dbConnection
     */
    private function generateDbConnection()
    {
        $mysqli = new \mysqli(
            $this->getHost(),
            $this->getUsername(),
            $this->getPassword(),
            $this->getDbname()
        );

        if ($mysqli -> connect_errno) {
            echo "Failed to connect to MySQL: " . $mysqli -> connect_error;
            exit();
        }
        $this->dbConnection = $mysqli;
    }


}