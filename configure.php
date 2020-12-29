<?php

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

    public static $list;

    public $paramList;

    public function __construct($name, $def){

        $this->paramList = $def;
        $hash = new Hash($name, $def);
        $this->paramList->hash = $hash->paramList;
        $this->paramList->state = "mock";
    }
}

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
            case 'xPub':
                    $obj->paramList->xPubRange = Type::$list->xPubRange->paramList;

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
                break;
            case 'rangeEncrypted':

                break;
            case 'issueAsset':
                    $obj->paramList->xPub = Type::$list->xPub->paramList;
                    $obj->paramList->rangeEncrypted = Type::$list->rangeEncrypted->paramList;
                break;
            case 'blindingKeyEncrypted':

                break;
            case 'contribution':
                    $obj->paramList->xPub = Type::$list->xPub->paramList;
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
$proto_file = 'prototypes.json';
$proto_content = json_encode($c, JSON_PRETTY_PRINT, 10);

file_put_contents('prototypes.json', $proto_content);

echo '<a href="'.$proto_file.'" target="_blank">Prototypes</a><br>';
