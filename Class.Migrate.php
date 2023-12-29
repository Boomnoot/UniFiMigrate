<?php

class Migrate {
    // Properties
    public $id;
    public $Olinkurl;
    public $OUserName;
    public $OPassword;
    public $Och;
    public $Nch;
    public $Nlinkurl;
    public $NUserName;
    public $NPassword;
    public $SiteName;
    
    public function __construct($SiteName, $idOld, $idNew) {
        $this->SiteName = $SiteName;
        self::Set_OldController($idOld);
        self::Set_NewController($idNew);
    }
    public function Set_OldController($id) {
        include 'connect.php';
        $q1 = $dbu->query("SELECT * From Controllers Where ControllerID = '".$id."'");
        $r1 = $q1->fetch(PDO::FETCH_ASSOC);	
        
        $this->Olinkurl = $r1['Link'];
        $this->OUserName = $r1['UserName'];
        $this->OPassword = $r1['Password'];
    }
    public function Set_NewController($id) {
        include 'connect.php';
        $q1 = $dbu->query("SELECT * From Controllers Where ControllerID = '".$id."'");
        $r1 = $q1->fetch(PDO::FETCH_ASSOC);	
        
        $this->Nlinkurl = $r1['Link'];
        $this->NUserName = $r1['UserName'];
        $this->NPassword = $r1['Password'];
    }
    function set_Oldlogin() {
		$BaseUrl = trim($this->Olinkurl);
		$UserName = $this->OUserName;
		$Password = $this->OPassword;
	
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE); 
        curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLOPT_URL, $BaseUrl . '/api/login');
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ch, CURLOPT_COOKIESESSION, true);
		curl_setopt($ch, CURLOPT_COOKIEJAR, 'unifises');  //could be empty, but cause problems on some hosts
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(['username' => $UserName, 'password' => $Password]));
		$content = curl_exec($ch);
		return $ch;
    }
    function set_Newlogin() {
		$BaseUrl = trim($this->Nlinkurl);
		$UserName = $this->NUserName;
		$Password = $this->NPassword;
	
		$ch2 = curl_init();
		curl_setopt($ch2, CURLOPT_SSL_VERIFYPEER, FALSE); 
        curl_setopt($ch2, CURLOPT_HEADER, 1);
        curl_setopt($ch2, CURLOPT_URL, $BaseUrl . '/api/login');
		curl_setopt($ch2, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ch2, CURLOPT_COOKIESESSION, true);
		curl_setopt($ch2, CURLOPT_COOKIEJAR, 'unifises');  //could be empty, but cause problems on some hosts
        curl_setopt($ch2, CURLOPT_POSTFIELDS, json_encode(['username' => $UserName, 'password' => $Password]));
		$content = curl_exec($ch2);
		return $ch2;
    }
    function GetActionOld($Endpoint) {
        $ch = self::set_Oldlogin();
        curl_setopt($ch, CURLOPT_URL, $Endpoint);
        curl_setopt($ch, CURLOPT_POST, false);
        $content = curl_exec($ch);
        return ProcesReturn($ch, $content);
    }
    
    function PostAction($EndPoint, $InputJson) {
        $ch = self::set_Oldlogin();
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE); 
        curl_setopt($ch, CURLOPT_URL, $EndPoint);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $InputJson);
        $content = curl_exec($ch);
        return ProcesReturn($ch, $content);
    }
    function PostActionTarget($EndPoint, $InputJson) {
        $ch = self::set_Newlogin();
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE); 
        curl_setopt($ch, CURLOPT_URL, $EndPoint);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $InputJson);
        $content = curl_exec($ch);
        return ProcesReturnCL($ch, $content);
    }
    
    function ExportSite() {
        $InputExport = json_encode(array(
                'cmd' => 'export-site'
            ));
        $Dlink = self::PostAction($this->Olinkurl."/api/s/".$this->SiteName."/cmd/backup", $InputExport);   
        return $this->Olinkurl.$Dlink[0]['url'];
    }  
    
    function DownloadUNF($file_url) {
        $ch = self::set_Oldlogin();
        $file_name = basename($file_url); 
        $save_to = 'du/'.$file_name;

        curl_setopt($ch, CURLOPT_POST, 0); 
        curl_setopt($ch,CURLOPT_URL,$file_url); 
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
        curl_setopt($ch, CURLOPT_HEADER, false);
        $file_content = curl_exec($ch);
        $error = curl_error($ch);
        curl_close($ch);

        $downloaded_file = fopen($save_to, 'w');
        fwrite($downloaded_file, $file_content);
        fclose($downloaded_file);
        return $file_name;
    }
    function UploadFile($File) {
        $ch = self::set_Newlogin();
        curl_setopt_array($ch, array(
          CURLOPT_URL => $this->Nlinkurl.'/upload/backup',
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => "",
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 0,
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => "POST",
          CURLOPT_POSTFIELDS => array('filename'=> new CURLFILE('<path>/UnifiMigrate/du/'.$File)), // '/var/www/t00r.nl/unifi/UnifiMigrate/up.unf'
          CURLOPT_HTTPHEADER => array(
            "X-Requested-With: XMLHttpRequest",
            "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/86.0.4240.198 Safari/537.36",
            "Accept: */*"
          ),
        ));
        $content = curl_exec($ch);
        $json = ProcesReturnCL($ch, $content);
        curl_close($ch);
        return $json;
    }
    function ImportSite($BackupID, $SiteDesc) {
        $InputImport = json_encode(array(
                'backup_id' => $BackupID, 
                'cmd' => 'import-site',
                'site_desc' => $SiteDesc
            ));
        return self::PostActionTarget($this->Nlinkurl."/api/s/default/cmd/backup", $InputImport);
    }
    function SetInform3($mac) {
        $InputJson = json_encode(array(
            'cmd' => 'migrate',
            'mac' => $mac,
            'inform_url' => $this->Nlinkurl.':8080/inform'
        ));
        self::PostAction($this->Olinkurl."/api/s/".$this->SiteName."/cmd/devmgr", $InputJson);
    }
    function MigrateDevices() {
        $Devices = self::GetActionOld($this->Olinkurl."/api/s/".$this->SiteName."/stat/device");
        foreach($Devices as $Device) {
            self::SetInform3($Device['mac']);
            $dev .= $Device['mac'];
        }
        return $dev;
    }
}

class DeleteSite {
    public $SiteName;
    public $Olinkurl;
    public $OPassword;
    
    public function __construct($SiteName, $idOld) {
        $this->SiteName = $SiteName;
        self::Set_OldController($idOld);
    }
    public function Set_OldController($id) {
        include 'connect.php';
        $q1 = $dbu->query("SELECT * From Controllers Where ControllerID = '".$id."'");
        $r1 = $q1->fetch(PDO::FETCH_ASSOC);	
        
        $this->Olinkurl = $r1['Link'];
        $this->OUserName = $r1['UserName'];
        $this->OPassword = $r1['Password'];
    }
    
    function set_Oldlogin() {
		$BaseUrl = trim($this->Olinkurl);
		$UserName = $this->OUserName;
		$Password = $this->OPassword;
	
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE); 
        curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLOPT_URL, $BaseUrl . '/api/login');
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ch, CURLOPT_COOKIESESSION, true);
		curl_setopt($ch, CURLOPT_COOKIEJAR, 'unifises');  //could be empty, but cause problems on some hosts
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(['username' => $UserName, 'password' => $Password]));
		$content = curl_exec($ch);
		return $ch;
    }
    
    function GetActionOld($Endpoint) {
        $ch = self::set_Oldlogin();
        curl_setopt($ch, CURLOPT_URL, $Endpoint);
        curl_setopt($ch, CURLOPT_POST, false);
        $content = curl_exec($ch);
        return ProcesReturn($ch, $content);
    }
    
    function GetSiteID() {
        $Sites = self::GetActionOld($this->Olinkurl.'/api/self/sites');
        foreach($Sites as $Site) {
            if($Site['name']==$this->SiteName) {
                return $Site['_id'];   
            }
        }
    }

    function PostAction($EndPoint, $InputJson) {
        $ch = self::set_Oldlogin();
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE); 
        curl_setopt($ch, CURLOPT_URL, $EndPoint);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $InputJson);
        $content = curl_exec($ch);
        return ProcesReturn($ch, $content);
    }
    
    function RemoveSite() {
        $SiteID = self::GetSiteID();
        $InputJson = json_encode(array(
            'cmd' => 'delete-site',
            'site' => $SiteID
        ));
        return self::PostAction($this->Olinkurl."/api/s/default/cmd/sitemgr", $InputJson);
    }
}

?>









