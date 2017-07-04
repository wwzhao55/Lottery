<?php
ini_set('date.timezone','Asia/Shanghai');
if (!file_exists(dirname(__FILE__).'/config.php')) {
    $db_config_file = fopen(dirname(__FILE__).'/config.php', 'w');
    $text = "<?php\ndefine('DB_HOST', '');\ndefine('DB_PORT', '');\ndefine('DB_NAME','');\ndefine('DB_USER','');\ndefine('DB_PASSWORD','');";
    $result = fwrite($db_config_file, $text);
    fclose($db_config_file);
}

require dirname(__FILE__).'/config.php';
require dirname(__FILE__).'/medoo.php';
require dirname(__FILE__).'/function.php';
require dirname(__FILE__).'/upload.php';

class api
{

    public function get_db()
    {
        try {
            $db = new medoo([
                // 必须配置项
                'database_type' => 'mysql',
                'database_name' => DB_NAME,
                'server' => DB_HOST,
                'username' => DB_USER,
                'password' => DB_PASSWORD,
                'charset' => 'utf8',
                'port' => DB_PORT
            ]);
        } catch (Exception $e) {
            $db = null;
        }

        return $db;
    }

    public function get_upload()
    {
        $upload = new FileUpload();
        return $upload;
    }


}