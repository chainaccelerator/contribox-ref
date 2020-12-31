<?php
declare(strict_types = 1);
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

class Html {

    public static function confGen(string $t, stdClass $c):bool {
    
        $d = 'rendered';

        if(is_dir($d) === false) mkdir( $d);

        $f =  $d.DIRECTORY_SEPARATOR.$t.'.json';
        $c = json_encode($c, JSON_PRETTY_PRINT);

        file_put_contents($f, $c);

        echo '<a href="'.$f.'" target="_blank">'.$t.'</a><br>';

        return true;
    }
}
class Hash {

    public $paramList;
    
    public function __construct(string $name, stdClass $def) {

        $this->paramList = new stdClass();
        $this->paramList->left = hash('sha256', $name);
        $this->paramList->right = hash('sha256', json_encode($def));
        $this->paramList->hash = hash('sha256', $this->paramList->left.$this->paramList->right);
        $this->paramList->data = '';
    }
}
class Type {
    
    public static $conf;
    public static $list;

    public $name = '';
    public $description = '';
    public $paramList;

    public function __construct(string $name, stdClass $def, string $description = '') {

        $this->name = $name;
        $this->description = $description;
        $this->paramList = $def;
        $hash = new Hash($name, $def);
        $this->paramList->hash = $hash->paramList;
        $this->paramList->state = "mock";
    }
    public static function confGen(stdClass $conf):bool {

        Type::$list = new stdClass();

        $alias = array();
        $alias = $conf->alias;

        foreach($conf->type as $name => $def) {

            if($name === 'hash') Type::$list->$name = new Hash("", $def);
            else {

                $obj = new Type($name, $def);

                foreach($obj->paramList as $k => $v) {

                    $d = $k;                     
                    $d = str_replace('List', '', $d);

                    if(strstr($k, 'Hash') !== false ) $d = 'hash';

                    if(isset($alias->$d) === true) $d = $alias->$d;

                    if(strstr($k, 'List') !== false ) {

                        if(isset(Type::$list->$d) === true) $obj->paramList->$k[0] = Type::$list->$d->paramList;
                    }
                    else {

                        if(isset(Type::$list->$d) === true) $obj->paramList->$k = Type::$list->$d->paramList;
                    }
                }
                switch ($name) {
                    
                    case 'timestamp':

                            $conf->signature->timeout = $obj->paramList;
                            break;
                    case 'xPub':

                            foreach($conf->signature->userRoleList as $k => $v) {
                                
                                $conf->signature->userRoleList->$k->name = $k;
                                $conf->signature->userRoleList->$k->hash = Type::$list->hash->paramList;
                                $conf->signature->userRoleList->$k->xPubList = array();
                                $conf->signature->userRoleList->$k->xPubList[0] = $obj->paramList;
                                $conf->signature->userRoleList->$k->xPubYesList = array();
                                $conf->signature->userRoleList->$k->xPubYesList[0] = $obj->paramList;
                                $conf->signature->userRoleList->$k->xPubNoList = array();
                                $conf->signature->userRoleList->$k->xPubNoList[0] = $obj->paramList;
                            }
                        break;
                    case 'proof':
                            
                            $obj->paramList->userRole = $conf->signature->userRoleList->default;
                            $obj->paramList->groupRole = $conf->signature->groupRoleList->default;
                            $obj->paramList->actionRole = $conf->signature->roleActionList->default;
                        break;
                    case 'boarding':

                            $obj->paramList->userRole = $conf->signature->userRoleList->default;
                            $obj->paramList->groupRole = $conf->signature->groupRoleList->default;
                            $obj->paramList->actionRole = $conf->signature->roleActionList->default;
                        break;
                    case 'template':
                            
                            $obj->paramList->userRole = $conf->signature->userRoleList->default;
                            $obj->paramList->groupRole = $conf->signature->groupRoleList->default;
                            $obj->paramList->actionRole = $conf->signature->roleActionList->default;
                        break;
                    case 'cosig':

                            $obj->paramList->xPubS = Type::$list->xPub->paramList;
                            $obj->paramList->txId = Type::$list->txId->paramList;
                            $obj->paramList->txIssueAsset = Type::$list->txId->paramList;
                            $obj->paramList->txUtxo = Type::$list->utxo->paramList;
                            $obj->paramList->script = Type::$list->script->paramList;
                        break;
                    case 'sig':
                    
                            /*
                            $obj->paramList->voutSigList = array();
                            $obj->paramList->voutSigList[0] = Type::$list->cosig->paramList;                            
                            */
                        break;
                    case 'response':

                            $obj->paramList->resource = new stdClass();
                        break;
                }
                Type::$list->$name = $obj;
            }
        }    
        Html::confGen(get_called_class(), Type::$list);

        return true;
    }
}

class Method {

    public static $conf;
    public static $list;

    public $name = '';
    public $description = ''; 
    public $request;
    public $response;

    public function __construct(string $name, stdClass $def, string $type, string $description = ''){

        $this->name = $name;
        $this->description = $description;
        $hash = new Hash($name, $def);

        $this->request = Type::$list->request->paramList;
        $this->request->methodHash = $hash->paramList;
        $this->request->method = $name;
        $this->request->paramList = new stdClass();
        $this->request->reason->state = "mock";
        $this->request->reason->type = $type;

        $this->response = Type::$list->response->paramList;
        $this->response->methodHash = $hash->paramList;
        $this->response->reason->state = "mock";
        $this->response->reason->type = $type;
    }
    public static function confGenParamList(Method $obj, string $way, array $paramList, string $attribute, string $listName, string $alias): Method {

        $obj->$way->$attribute->$listName[0] = new stdClass();
        $element = Type::$list->$alias->paramList;

        foreach($paramList as $attributeName) {
                                        
            $obj->$way->$attribute->$listName[0]->$attributeName = $element->$attributeName;
        }
        return $obj;
    }
    public static function confGenParam(Method $obj, string $way, array $paramList, string $attribute, string $listName, string $alias): Method {

        $obj->$way->$attribute->$listName = new stdClass();
        $element = Type::$list->$alias->paramList;

        foreach($paramList as $attributeName) {
                                        
            $obj->$way->$attribute->$listName->$attributeName = $element->$attributeName;
        }
        return $obj;
    }
    public static function aliasSet(string $paramType, stdClass  $conf): string{

        $alias = $conf->rpc->alias;
        $a = $paramType;

        if(strstr($paramType, 'Hash') !== false ) $a = 'hash';

        if(isset($alias->$paramType) === true)  $a = $alias->$paramType;

        $a = str_replace('List', '', $a);

        return $a;
    }
    public static function funcSet(string $paramType): string {

        $func = 'confGenParam';

        if(strstr($paramType, 'List') !== false) $func = 'confGenParamList';

        return $func;
    }
    public static function confGen(stdClass $conf):bool {

        Method::$list = new stdClass();

        foreach($conf->rpc->method as $type => $method) {

            switch ($type ) {
                case 'authentification':
                    $t = '';
                    break;
                case 'key':
                    $t = 'share|backup|lock';
                    break;
                case 'boarding':
                    $t = 'default';
                    break;
                case 'contribution':
                    $t = 'default';
                    break;
                case 'public':
                    $t = 'default';
                    break;
                case 'peer':
                    $t = 'default';
                    break;
            }
            foreach($method as $name => $def) {

                $obj = new Method($name, $def, $t);

                foreach($def->paramList as $paramType => $attributeList) {

                    $a = self::aliasSet($paramType, $conf);
                    $func = self::funcSet($paramType);                 
                    $obj = Method::$func($obj, 'request', $attributeList, 'paramList', $paramType, $a);
                }
                foreach($def->reason as $paramType => $attributeList) {
                 
                    $a = self::aliasSet($paramType, $conf);
                    $func = self::funcSet($paramType);
                    $obj = Method::$func($obj, 'response', $attributeList, 'resource', $paramType, $a);
                }
                Method::$list->$name = $obj;
            }
        }
        Html::confGen(get_called_class(), Method::$list);

        return true;
    }
}    
$conf = json_decode(file_get_contents("conf.json"));

Type::confGen($conf);
Method::confGen($conf);

