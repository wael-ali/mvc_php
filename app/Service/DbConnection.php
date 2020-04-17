<?php


namespace app\Service;


class DbConnection
{
    const CONFIGRATION_KEYS = [
        'host',
        'username',
        'password',
        'dbname',
        'database'
    ];
    const DATABASE_URL = 'DATABASE_URL';
    private $host = null;
    private $username = null;
    private $password = null;
    private $dbname = null;
    private $database = null;
    private $dbConnection = null;

    public function __construct()
    {
        $this->initiateConfigs();
    }

    public function initiateConfigs()
    {
        if (!isset($GLOBALS[self::DATABASE_URL])){
            throw new \Exception('No Database configration is found, please set the DATABASE_URL variable in .env');
        }

        $db_url = $GLOBALS[self::DATABASE_URL];
        $temp = explode("://", $db_url);
        if (count($temp) != 2){
            $exceptionMsg = 'Something wrong with '.self::DATABASE_URL.' varable in .env!!, around "://".';
            throw new \Exception($exceptionMsg);
        }
        // mysql, postgress ...
        $this->database = $temp[0];
        $temp = $temp[1];
        $temp = explode('@', $temp);
        if (count($temp) != 2){
            $exceptionMsg = 'Something wrong with '.self::DATABASE_URL.' varable in .env!!, around "@".';
            throw new \Exception($exceptionMsg);
        }
        $user_pwd = $temp[0];
        $user_pwd = explode(':', $user_pwd);
        if (count($user_pwd) != 2){
            $exceptionMsg = 'Something wrong with '.self::DATABASE_URL.' varable in .env!!, around ":" between username:password.';
            throw new \Exception($exceptionMsg);
        }
        $this->username = $user_pwd[0];
        $this->password = $user_pwd[1];
        $host_dbn = $temp[1];
        $host_dbn = explode('/', $host_dbn);
        if (count($host_dbn) != 2){
            $exceptionMsg = 'Something wrong with '.self::DATABASE_URL.' varable in .env!!, around "/" between host/dbname.';
            throw new \Exception($exceptionMsg);
        }
        $this->host = $host_dbn[0];
        if ($host_dbn[1] == ''){
            $exceptionMsg = 'Something wrong with '.self::DATABASE_URL.' varable in .env!!, around "/" between host/dbname; no dbname is found..';
            throw new \Exception($exceptionMsg);
        }
        $this->dbname = (explode('?',$host_dbn[1]))[0];

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
        if ($this->database == 'mysql'){
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
            return 1;
        }
        throw new \Exception($this->database.' databases till now not supported, please use mysql database.');
    }

    /**
     * @return null
     */
    public function getDatabase()
    {
        return $this->database;
    }

    /**
     * @param null $database
     */
    public function setDatabase($database)
    {
        $this->database = $database;
    }


}