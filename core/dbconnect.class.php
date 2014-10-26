<?

Class dbconnect {

    public $con;

    function __construct() {

        $this -> connect_mysql();

    }

    function connect_mysql() {

        $con = mysql_connect(MYSQL_SERVER, MYSQL_USER, MYSQL_PASS);

        if (!$con)
            die(retornaErro('<p>Erro conectando ao banco de dados.</p>'));

        if ($con)
            mysql_select_db(MYSQL_DATABASE);

        $this -> con = $con;
    }

    function consulta($query) {

        $result = mysql_query($query);
        if (!$result) {
            erro('<p>Erro BD:"' . mysql_errno($this -> con) . ": " . mysql_error($this -> con) . '"</p><br><br><p>' . $query . '</p>');
            die();
        }
        return $result;
    }

    function close() {

        mysql_close($this -> con);
    }

}
?>