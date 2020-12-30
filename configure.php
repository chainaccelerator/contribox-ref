<?php

class Html(){

    public static function confGen($t){
    
        $f = $t.'.json';
        $c = json_encode($c, JSON_PRETTY_PRINT, 10);

        file_put_contents($f, $t::$conf);

        echo '<a href="'.$f.'" target="_blank">'.$t.'</a><br>';
    }
}

class Hash {

    public $paramList;
    
    public function __construct($name, $def) {

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

    public $paramList;
    public $name = '';
    public $description = '';

    public function __construct($name, $def, $description = ''){

        $this->name = $name;
        $this->description = $description;
        $this->paramList = $def;
        $hash = new Hash($name, $def);
        $this->paramList->hash = $hash->paramList;
        $this->paramList->state = "mock";
    }
    public static function confGen(){

        Type::$list = new stdClass();
        $conf = json_decode(file_get_contents("conf.json"));

        foreach($conf->type as $name => $def) {

            if($name === 'hash') Type::$list->$name = new Hash("", $def);
            else {

                $obj = new Type($name, $def);

                switch ($name) {

                    case 'password':                
                        
                        break;
                    case 'timestamp':

                            $conf->signature->timeout = $obj->paramList;
                            break;
                    case 'key':
                
                        break;
                    case 'blindingKey':
                
                        break;
                    case 'xPubRange':
                        
                        break;
                    case 'xPubRange':
                        
                        break;
                    case 'pubKeyEncrypted':
    
                        break;
                    case 'rangeEncrypted':

                        break;
                    case 'xPub':

                            $obj->paramList->xPubRange = Type::$list->xPubRange->paramList;
                            $obj->paramList->rangeEncrypted = Type::$list->rangeEncrypted->paramList;
                            $obj->paramList->pubKeyEncrypted = Type::$list->xPubEncrypted->paramList;
                            $obj->paramList->encrypted = Type::$list->pubKeyEncrypted->paramList;

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
                    case 'jsonData':
                            
                        break;
                    case 'stateReason':
                            
                        break;
                    case 'keyVal':
                            $obj->paramList->val = Type::$list->jsonData->paramList;
                        break;
                    case 'project':
                            
                        break;
                    case 'licence':
                            
                        break;
                        case 'tag':
                                
                        break;
                    case 'proof':

                            $obj->paramList->xPub = Type::$list->xPub->paramList;
                            $obj->paramList->project = Type::$list->project->paramList;
                            $obj->paramList->licence = Type::$list->licence->paramList;
                            $obj->paramList->licenceChange = Type::$list->licence->paramList;
                            $obj->paramList->userRole = $conf->signature->userRoleList->default;
                            $obj->paramList->groupRole = $conf->signature->groupRoleList->default;
                            $obj->paramList->actionRole = $conf->signature->roleActionList->default;
                            $obj->paramList->descriptionPublicList[0] = Type::$list->keyVal->paramList;
                            $obj->paramList->environnementList[0] = Type::$list->keyVal->paramList;
                            $obj->paramList->qualityList[0] = Type::$list->keyVal->paramList;
                            $obj->paramList->contributeList[0] = Type::$list->keyVal->paramList;
                            $obj->paramList->originList[0] = Type::$list->hash->paramList;
                            $obj->paramList->parentList[0] = Type::$list->hash->paramList;
                            $obj->paramList->previousList[0] = Type::$list->hash->paramList;
                            $obj->paramList->leftList[0] = Type::$list->hash->paramList;
                            $obj->paramList->ndaList[0] = Type::$list->keyVal->paramList;
                            $obj->paramList->condifidentialDataList[0] = Type::$list->keyVal->paramList;
                            $obj->paramList->metaDataList[0] = Type::$list->keyVal->paramList;
                            $obj->paramList->officerList[0] = Type::$list->keyVal->paramList;
                            $obj->paramList->ediList[0] = Type::$list->keyVal->paramList;
                            $obj->paramList->certificateList[0] = Type::$list->keyVal->paramList;
                            $obj->paramList->exportControlList[0] = Type::$list->keyVal->paramList;
                            $obj->paramList->keyValueList[0] = Type::$list->keyVal->paramList;
                            $obj->paramList->forList[0] = Type::$list->xPub->paramList;
                            $obj->paramList->toList[0] = Type::$list->xPub->paramList;
                            $obj->paramList->tagList[0] = Type::$list->tag;
                        break;
                    case 'boarding':

                            $obj->paramList->xPubList = Type::$list->xPub->paramList;
                            $obj->paramList->userRole = $conf->signature->userRoleList->default;
                            $obj->paramList->groupRole = $conf->signature->groupRoleList->default;
                            $obj->paramList->actionRole = $conf->signature->roleActionList->default;
                        break;
                        case 'script':
                                
                            break;
                    case 'template':
                            
                            $obj->paramList->contributionHash = Type::$list->hash->paramList;
                            $obj->paramList->script = Type::$list->script->paramList;
                            $obj->paramList->userRole = $conf->signature->userRoleList->default;
                            $obj->paramList->groupRole = $conf->signature->groupRoleList->default;
                            $obj->paramList->actionRole = $conf->signature->roleActionList->default;
                        break;
                    case 'utxoData':
                            
                        break;
                    case 'txId':
                            
                        break;
                    case 'utxo':

                            $obj->paramList->txId = Type::$list->txId->paramList;
                            $obj->paramList->utxoData = Type::$list->utxoData->paramList;
                            $obj->paramList->script = Type::$list->script->paramList;
                        break;
                    case 'pubKey':
                            
                        break;
                    case 'sigData':
                            
                        break;
                    case 'cosig':

                            $obj->paramList->sigData = Type::$list->sigData->paramList;
                            $obj->paramList->pubKey = Type::$list->pubKey->paramList;
                            $obj->paramList->xPub = Type::$list->xPub->paramList;
                            $obj->paramList->xPubS = Type::$list->xPub->paramList;
                            $obj->paramList->txId = Type::$list->txId->paramList;
                            $obj->paramList->txIssueAsset = Type::$list->txId->paramList;
                            $obj->paramList->txUtxo = Type::$list->utxo->paramList;
                            $obj->paramList->script = Type::$list->script->paramList;
                        break;
                    case 'sig':

                            $obj->paramList->sigData = Type::$list->sigData->paramList;
                            $obj->paramList->pubKey = Type::$list->pubKey->paramList;
                            $obj->paramList->xPub = Type::$list->xPub->paramList;
                            $obj->paramList->xPubS = Type::$list->xPub->paramList;
                            $obj->paramList->txId = Type::$list->txId->paramList;
                            $obj->paramList->txIssueAsset = Type::$list->txId->paramList;
                            $obj->paramList->txUtxo = Type::$list->utxo->paramList;
                            $obj->paramList->script = Type::$list->script->paramList;                    
                            $obj->paramList->voutSigList = array();
                            $obj->paramList->voutSigList[0] = Type::$list->cosig->paramList;                            
                            $obj->paramList->pubKeyEncrypted = Type::$list->pubKeyEncrypted->paramList;
                            $obj->paramList->pubKeyS = Type::$list->pubKey->paramList;
                            $obj->paramList->pubKeySEncrypted = Type::$list->pubKeyEncrypted->paramList;
                            $obj->paramList->xPubHash = Type::$list->hash->paramList;
                            $obj->paramList->xPubEncrypted = Type::$list->xPubEncrypted->paramList;
                            $obj->paramList->XPubSHash = Type::$list->hash->paramList;
                            $obj->paramList->xPubSEncrypted = Type::$list->xPubEncrypted->paramList;
                        break;
                    case 'issueAsset':

                            $obj->paramList->xPub = Type::$list->xPub->paramList;
                            $obj->paramList->rangeEncrypted = Type::$list->rangeEncrypted->paramList;
                        break;
                    case 'blindingKeyEncrypted':

                        break;
                    case 'contribution':

                            $obj->paramList->xPubHash = Type::$list->hash->paramList;
                            $obj->paramList->proof = Type::$list->proof->paramList;
                            $obj->paramList->templateHash = Type::$list->hash->paramList;
                            $obj->paramList->blindKeyEncryptedList[0] = Type::$list->blindingKeyEncrypted->paramList;
                            $obj->paramList->boardingList[0] = Type::$list->boarding->paramList;
                            $obj->paramList->sig = Type::$list->sig->paramList;                
                        break;
                    case 'keyEncrypted':

                            $obj->paramList->key = Type::$list->key->paramList;
                        break;
                    case 'keyShared':

                            $obj->paramList->xPubSList[0] = Type::$list->xPub->paramList;
                            $obj->paramList->keyEncryptedList[0] = Type::$list->keyEncrypted->paramList;
                        break;
                    case 'ipv4':
                            
                        break;
                    case 'nodeURI':

                            $obj->paramList->ipv4 = Type::$list->ipv4->paramList;
                        break;
                    case 'otp':

                            $obj->paramList->createDate = Type::$list->timestamp->paramList;
                            $obj->paramList->expireDate = Type::$list->timestamp->paramList;
                            $obj->paramList->pubKey = Type::$list->pubKey->paramList;
                            $obj->paramList->pubId = Type::$list->txId->paramList;
                        break;
                    case 'metaData':

                            $obj->paramList->timeout = Type::$list->timestamp->paramList;
                            $obj->paramList->ipv4List[0] = Type::$list->ipv4->paramList;
                            $obj->paramList->pubKey = Type::$list->pubKey->paramList;
                            $obj->paramList->sig = Type::$list->sig->paramList;
                            $obj->paramList->PowHash = Type::$list->hash->paramList;
                            $obj->paramList->otp = Type::$list->otp->paramList;
                        break;
                    case 'trace':
                            $obj->paramList->proofMerkleTree = Type::$list->hash->paramList;
                            $obj->paramList->sigYesList[0] = Type::$list->xPub->paramList;
                            $obj->paramList->sigNoList[0] = Type::$list->xPub->paramList;
                            $obj->paramList->transctionHash = Type::$list->hash->paramList;
                            $obj->paramList->gitCommitHash = Type::$list->hash->paramList;
                        break;
                    case 'type':

                        break;
                    case 'reason':

                        break;
                    case 'paramList':
                        
                        break;
                    case 'method':
                    
                        break;
                    case 'result':
                        
                        break;
                    case 'request':

                            $obj->paramList->methodHash = Type::$list->hash->paramList;
                            $obj->paramList->metaData = Type::$list->metaData->paramList;
                            $obj->paramList->method = Type::$list->method->paramList;
                            $obj->paramList->paramList = Type::$list->paramList->paramList;
                            $obj->paramList->reason = Type::$list->reason->paramList;
                        break;
                    case 'response':

                            $obj->paramList->methodHash = Type::$list->hash->paramList;
                            $obj->paramList->metaData = Type::$list->metaData->paramList;
                            $obj->paramList->result = Type::$list->result->paramList;
                            $obj->paramList->reason = Type::$list->reason->paramList;
                        break;
                    default:
                        
                        break;
                }
                Type::$list->$name = $obj;
            }
        }    
        $c = new stdClass();

        foreach(Type::$list as $k => $v) {

            $c->$k = $v->paramList;
        }        
        self::$conf = $c;
        Html::confGen($t);
    }
}

class Method {

    public static $conf;
    public static $list;

    public $name = '';
    public $description = '';
    public $request;
    public $response;

    public function __construct($name, $def, $type, $description = ''){

        $this->name = $name;
        $this->description = $description;
        $hash = new Hash($name, $def);

        $this->request = Type::$list->request->paramList;
        $this->request->methodHash = $hash->paramList;
        $this->request->method = $name;
        $this->request->reason->state = "mock";
        $this->request->reason->type = $type;

        $this->response = Type::$list->response->paramList;
        $this->response->methodHash = $hash->paramList;
        $this->response->reason->state = "mock";
        $this->response->reason->type = $type;
    }
    public static function confGenParamList($obj, $paramList, $attribute, $listName, $elementType){

        $obj->$way->$attribute->$listName[0] = new stdClass();
        $element = Type::$list->$elementType->paramList;

        foreach($paramList->$listName as $attributeName) {
                                        
            $obj->$way->$attribute->$listName[0]->$attributeName = $element->$attributeName;
        }
        return $obj;
    }
    public static function confGenParamListRequest($obj, $def, $listName){

        $attribute = 'paramList';
        $paramList = $def->paramList;

        return public static function confGenParam($obj, $paramList, $attribute, $listName, $elementType);
    }
    public static function confGenParamListResponse($obj, $def, $listName, $elementType){

        $attribute = 'resource';
        $paramList = $def->reason;

        return public static function confGenParam($obj, $paramList, $attribute, $listName, $elementType);
    }
    public static function confGenParam($obj, $paramList, $attribute, $listName){

        $obj->$way->$attribute->$listName = new stdClass();
        $element = Type::$list->$elementType->paramList;

        foreach($paramList->$listName as $attributeName) {
                                        
            $obj->$way->$attribute->$listName->$attributeName = $element->$attributeName;
        }
        return $obj;
    }
    public static function confGenParamRequest($obj, $def, $listName){

        $attribute = 'paramList';
        $paramList = $def->paramList;

        return public static function confGenParam($obj, $paramList, $attribute, $way, $listName);
    }
    public static function confGenParamResponse($obj, $def, $listName){

        $attribute = 'resource';
        $paramList = $def->reason;

        return public static function confGenParam($obj, $paramList, $attribute, $way, $listName);
    }

    public static function confGen(){

        Method::$list = new stdClass();

        foreach($conf->method as $type => $method) {

            switch ($type ) {
                case 'authentification':
                    $t = ''
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
            }
            foreach($method as $name => $def) {

                $obj = new Method($name, $def, $t);

                switch ($name) {

                    case 'authOtpFinalGet':                
                                
                        break;
                    case 'authResponseGet':                
                        
                        break;
                    case 'keyShare':                
                        
                        $obj->request->reason->step = 'todo';

                        $obj = Method::confGenParamRequest($obj, $def, 'pubKeyEncrypted');
                        $obj = Method::confGenParamListRequest($obj, $def, 'pubKeySEncryptedList', 'pubKeyEncrypted');
                        $obj = Method::confGenParamListRequest($obj, $def, 'keyEncryptedList', 'keyEncrypted');
                        
                        $obj = Method::confGenParamResponse($obj, $def, 'requestHash');
                        break;
                    case 'keyShareGet':                

                        $obj = Method::confGenParamListRequest($obj, $def, 'pubKeySEncryptedList', 'pubKeyEncrypted');
                        
                        $obj = Method::confGenParamListResponse($obj, $def, 'requestHashList', 'requestHash');
                        $obj = Method::confGenParamListResponse($obj, $def, 'pubKeyEncryptedList', 'pubKeyEncrypted');
                        $obj = Method::confGenParamListResponse($obj, $def, 'keyEncryptedList', 'keyEncrypted');
                        break;
                    case 'keyShareConfirm':                
                        
                        $obj = Method::confGenParamRequest($obj, $def, 'requestHash');
                        $obj = Method::confGenParamListRequest($obj, $def, 'pubKeySEncryptedList', 'pubKeyEncrypted');
                        $obj = Method::confGenParamRequest($obj, $def, 'sigList', 'sig');
                        break;
                    case 'keyShareConfirmGet':                

                        $obj = Method::confGenParamRequest($obj, $def, 'requestHash');

                        $obj = Method::confGenParamListResponse($obj, $def, 'sigList', 'sig');                        
                        break;
                    case 'boardingTemplateGet':                
                        
                        $obj = Method::confGenParamRequest($obj, $def, 'project');
                        $obj = Method::confGenParamRequest($obj, $def, 'licence');
                        $obj = Method::confGenParamRequest($obj, $def, 'userRole');
                        $obj = Method::confGenParamRequest($obj, $def, 'groupRole');
                        $obj = Method::confGenParamRequest($obj, $def, 'actionRole');

                        $obj = Method::confGenParamListResponse($obj, $def, 'contributionList', 'contribution');
                        $obj = Method::confGenParamListResponse($obj, $def, 'templateList', 'template');
                        break;
                    case 'boarding':
                        
                        $obj = Method::confGenParamRequest($obj, $def, 'pubKeyEncrypted');
                        $obj = Method::confGenParamRequest($obj, $def, 'contribution');
                        $obj = Method::confGenParamRequest($obj, $def, 'template');

                        $obj = Method::confGenParamResponse($obj, $def, 'requestHash');
                        break;
                    case 'boardingGet':            
                        
                        $obj = Method::confGenParamListRequest($obj, $def, 'pubKeySEncryptedList', 'pubKeyEncrypted');
                        
                        $obj = Method::confGenParamListResponse($obj, $def, 'requestHashList', 'requestHash');
                        $obj = Method::confGenParamListResponse($obj, $def, 'pubKeyEncryptedList', 'pubKeyEncrypted');
                        $obj = Method::confGenParamListResponse($obj, $def, 'contributionList', 'contribution');
                        $obj = Method::confGenParamListResponse($obj, $def, 'templateList', 'template');                        
                        break;
                    case 'boardingBroadcast':                
                        
                        $obj = Method::confGenParamRequest($obj, $def, 'requestHash');
                        $obj = Method::confGenParamListRequest($obj, $def, 'pubKeyEncryptedList', 'pubKeyEncrypted');
                        $obj = Method::confGenParamListRequest($obj, $def, 'sigList', 'sig');
                        break;
                    case 'boardingBroadcastGet':
                        
                        $obj = Method::confGenParamRequest($obj, $def, 'requestHash');
                        
                        $obj = Method::confGenParamListResponse($obj, $def, 'pubKeyEncryptedList', 'pubKeyEncrypted');
                        $obj = Method::confGenParamListResponse($obj, $def, 'sigList', 'sig');                        
                        break;
                    case 'contribution':                
                        
                        break;
                    case 'contributionGet':                
                        
                        break;
                    case 'contributionConfirm':                
                        
                        break;
                    case 'contributionConfirmGet':                
                        
                        break;
                    case 'contributionBroadcast':                
                        
                        break;
                    case 'contributionBroadcastGet':                
                        
                        break;
                    case 'publicPeerListGet':                
                        
                        break;
                    case 'publicSDKGet':                
                        
                        break;
                    case 'publicArchiveGet':                
                        
                        break;
                    case 'publicDidGet':                
                        
                        break;
                    case 'peerCheck':                
                        
                        break;
                    default:
                        
                        break;
                }
                Type::$list->$name = $obj;
            }
        }

        $c = new stdClass();

        foreach(Method::$list as $k => $v) {

            $c->$k = $v->paramList;
        }
        self::$conf = $c;
        Html::confGen($t);
    }
}    
Type::confGen();
Method::confGen();


