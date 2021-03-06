<?php
/**
 * Created by JetBrains PhpStorm.
 * User: artur
 * Date: 7/12/13
 * Time: 3:03 PM
 * To change this template use File | Settings | File Templates.
 */


/**
 * TODO
 * this class needs to be refactored
 */
class Word_Table {

    const GET_WORD_LIMIT = "20";
    static $connection = null;

    function __construct()
    {
        $this->init();
    }

    /**
     * gets the word upon request
     * @param $offset
     * @param null $limit
     * @return array
     */
    public function getWord($offset , $limit = null){

        $query = "SELECT word FROM  `word_list` LIMIT " . $offset . " , " . (($limit == null) ? self::GET_WORD_LIMIT : $limit);

        $response = mysqli_query(self::$connection ,$query) or die (mysqli_error(self::$connection));

        $result = array();
        while ($row = $response->fetch_assoc()) {
            $result[] = $row['word'];
        }
        return $result;
    }

    public function getWords($offset , $limit = null){

        $query = "SELECT id,word FROM  `rhymes` LIMIT " . $offset . " , " . (($limit == null) ? self::GET_WORD_LIMIT : $limit);

        $response = mysqli_query(self::$connection ,$query) or die (mysqli_error(self::$connection));

        $result = array();
        while ($row = $response->fetch_assoc()) {
            $result[] = $row;
        }
        return $result;
    }

    public function insertRhyme($word,$str_json){
        $sql = sprintf(
            "INSERT INTO synonyms (word_id,json) VALUES ('%s','%s')",
            mysql_escape_string($word),
            mysql_escape_string($str_json)
        );
        $response = mysqli_query(self::$connection ,$sql) or die (mysqli_error(self::$connection));
    }

    public function insertSynonym($word_id,$str_json){
        $sql = sprintf(
            "INSERT INTO synonyms (word_id,json) VALUES ('%s','%s')",
            mysql_escape_string($word_id),
            mysql_escape_string($str_json)
        );
        $response = mysqli_query(self::$connection ,$sql) or die (mysqli_error(self::$connection));
    }

    /**
     * gets the appropriate rhyme to the word
     * @param $word
     * @return array
     */
    public function getWordInfo($word){
        $query = sprintf(
            "SELECT rhyme, synonym FROM word_list WHERE word = '%s'",
            mysql_escape_string($word)
        );
        $response = mysqli_query(self::$connection ,$query) or die (mysqli_error(self::$connection));
        //var_dump((array)$response);
        return $response->fetch_assoc();
    }

    /**
     * gets the appropriate rhyme to the word
     * @param $info
     * @internal param $word
     * @return array
     */
    public function setWordInfo($info){
        $sql = sprintf(
            "INSERT INTO word_list (word,rhyme, synonym) VALUES ('%s','%s', '%s')",
            mysql_escape_string($info["word"]),
            mysql_escape_string($info["rhyme"]),
            mysql_escape_string($info["synonym"])
        );
        $response = mysqli_query(self::$connection ,$sql) or die (mysqli_error(self::$connection));
        echo $response;
    }

    /**
     * gets the appropriate Synonym to the word
     * @param $word
     * @return array
     */
    public function getSynonym($word){
        $query = sprintf(
            "SELECT json FROM rhymes WHERE word = '%s'",
            mysql_escape_string($word)
        );

        $response = mysqli_query(self::$connection ,$query) or die (mysqli_error(self::$connection));
        return $response->fetch_assoc();
    }

    /**
     * init the connection
     */
    function init(){
        $config = $this->getConfigFile(/*PUBLIC_DIR.*/'./lib/database.json');

        // Create connection
        $connection = mysqli_connect($config["host"],$config["username"],$config["password"],$config["dbname"]);
        self::$connection = $connection;

    }

    public static function getConfigFile($file)
    {
        $data = str_replace("\\", "\\\\", file_get_contents($file));
        $json = json_decode($data, TRUE);

        return $json;
    }
}