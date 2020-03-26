<?php
date_default_timezone_set("ASIA/Jakarta");

// Token & API Telegram
$akses_token = '1133346190:AAFzuIX54Vnoa4g4Nna-sodpHz3Xs6E16Us';
$usernamebot = 'script000kiddies000_corona_bot';
$api = 'https://api.telegram.org/bot' . $akses_token;

function hapus($string){
  $string = str_replace(' ', '', $string);
  return $string;
}

function info_indonesia(){
  $data = file_get_contents('https://api.kawalcorona.com/indonesia/');
  $data = json_decode($data, True);
  $hasil  = "positif corona : ".$data[0]['positif']."\n";
  $hasil .= "pasien sembuh : ".$data[0]['sembuh']."\n";
  $hasil .= "pasien meninggal : ".$data[0]['meninggal'];
  return $hasil;
}

function info_prov($provinsi){
  $data = file_get_contents('https://api.kawalcorona.com/indonesia/provinsi/');
  $data = json_decode($data, true);
  foreach($data as $row){
    $prov      = $row['attributes']['Provinsi'];
    $positif   = $row['attributes']['Kasus_Posi'];
    $sembuh    = $row['attributes']['Kasus_Semb'];
    $meninggal = $row['attributes']['Kasus_Meni'];
    $pattern   = '/'.$provinsi.'/i';

    if(preg_match(hapus($pattern),hapus($prov))){
      $hasil = "provinsi : $prov \npositif : $positif \nsembuh : $sembuh \nmeninggal : $meninggal";
      return $hasil;
    }
  }
}


// Cek Status BOT
$message= json_decode(file_get_contents('php://input'), TRUE);
$chat_id = $message['message']['chat']['id'];
$fromid  = $message['message']["from"]["id"];
$text    = $message['message']['text'];
$username = $message['message']['from']['username'];

//variable nampung nama user 
isset($message['message']['from']['last_name']) 
        ? $namakedua = $message['message']['from']['last_name'] 
        : $namakedua = '';   
$namauser = $message['message']["from"]["first_name"]. ' ' .$namakedua;

//ambi id orang lain dari pesan yang di reply

$idorang         = $message['message']['reply_to_message']['from']['id'];
$usernameorang   = $message['message']['reply_to_message']['from']['username'];

//buat hapus kelebihan spasi
$message = preg_replace('/\s\s+/', ' ', $text);

//buat membagi pesan menjadi 3 bagian
$command = explode(' ',$message,3);
//ambil bagian pesan yang pertama
switch($command[0]) {
        case '/info':
          $hasil = info_indonesia();
          sendMessage($chat_id,$hasil);
        break;

        case '/provinsi':
          $provinsi = $command[1];
          $hasil    = info_prov($provinsi);
          sendMessage($chat_id,$hasil);
        break;

        case '/whoami':
          $hasil="saya bot";
          sendMessage($chat_id,$hasil);
        break;
}

function sendMessage($chat_id, $message) {
  file_get_contents($GLOBALS['api'] . '/sendMessage?chat_id=' . $chat_id . '&text=' . urlencode($message) . '&parse_mode=html');
}
?>
