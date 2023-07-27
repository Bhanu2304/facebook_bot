<?php 
require_once '../dialdee/vendor/autoload.php';
require("meta_bot_fun.php");
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ERROR);

$conn = mysqli_connect("192.168.10.23","root","Mas@1234") or die("can't connect with dialdee");
mysqli_select_db($conn,"db_voyage")or die("cannot select DB dialdesk on Dialdeee");

$conn_local = mysqli_connect("172.12.10.22","root","dial@mas123") or die("can't connect with ticket system");
mysqli_select_db($conn_local,"db_dialdesk")or die("cannot select local DB dialdesk on Dialdeee");

$dd = mysqli_connect("192.168.10.12","root","dial@mas123") or die("can't connect with dialdesk system");
mysqli_select_db($dd,"db_dialdesk")or die("cannot select DB dialdesk");




writeToLog("json=>".file_get_contents('php://input'));

$json = file_get_contents('php://input');
$req_json = addslashes($json);
$input = json_decode($json, true);

if(isset($input)) 
{

    if(empty($input))
        exit();

    else
    {
        $sender = $input['entry'][0]['messaging'][0]['sender']['id'];
        #writeToLog("sender = $sender");
        // Get the returned message
        $message = $input['entry'][0]['messaging'][0]['message']['text'];
        #writeToLog("message = $message");

        $wa_id = $sender;
        $from = $sender;
        foreach ($input['entry'][0]['messaging'] as $event){
            // Check for quick reply events
            if(isset($event['message']['quick_reply']))
            {
                $payload = $event['message']['quick_reply']['payload'];
                writeToLog("payload = $payload");

                $select_current_radio = "select * from dialdee_bot_master_meta where parent_id='$payload' limit 1";
                $rsc_current_radio = mysqli_query($conn,$select_current_radio);
                $current_det = mysqli_fetch_assoc($rsc_current_radio);
               
                writeToLog("select qry = $select_current_radio");
            }
        }
    }

    if(empty($wa_id))
    {
        exit;
    }

    $session_id = date('Y-m-d');
    
    
    $select_ph_det = "SELECT * FROM `dialdee_fbconfigmaster` WHERE  app_id='706160150966404' LIMIT 1";
    $rsc_ph_det = mysqli_query($conn,$select_ph_det);
    $ph_det = mysqli_fetch_assoc($rsc_ph_det);
    $client_id = $ph_det['client_id']; 
    $phone_id = $ph_det['id']; 
    $app_id = $ph_det['page_id'];
    $api_key = $ph_det['page_access_token'];
    #writeToLog("api_query=>".$select_ph_det);
    $bot_enable = $ph_det['bot_enable'];
    $bot_exist = false;
    if($bot_enable)
    {
        $check_bot_exist = "select count(1) cnt from dialdee_bot_master_meta where phoneno_id='$phone_id' limit 1";
        $bot_exist_rsc = mysqli_query($conn,$check_bot_exist);
        $bot_exist_det = mysqli_fetch_assoc($bot_exist_rsc);
        $bot_exist_cnt = $bot_exist_det['cnt'];
        if($bot_exist_cnt>2)
        {
            $bot_exist = true;
        }
    }

    $get_msg_from = false;
    $msg = addslashes($message);

    
    $createdate = date('Y-m-d H:i:s');


    $qry_check = "SELECT * FROM chat_customer WHERE client_id='$client_id' AND session_id='$wa_id'  
        AND case_status='open' AND DATE(created_at)=CURDATE() order by id desc LIMIT 1";
     $rsc_exist_customer = mysqli_query($conn,$qry_check);
     $customer_det = mysqli_fetch_assoc($rsc_exist_customer);


     $data = get_name($wa_id,$api_key);

     #writeToLog("name data".$data);

     $username = $data['first_name'] .' '. $data['last_name'];
     #writeToLog("name".$username);

     if(empty($customer_det))
     {
         $interaction_type = 'Facebook';
         if($bot_exist)
         {
            $interaction_type = 'bot';
         }
          $qry_new_customer = "INSERT INTO chat_customer SET fbid='$app_id',client_id='$client_id',customer_no='$wa_id',customer_name='$username',session_id='$wa_id',category='enquiry',
        interaction_type='$interaction_type',SUBJECT='Facebook',case_status='open',api_key='$api_key',created_at=NOW(),created_by=''";
        $rsc_new_customer = mysqli_query($conn,$qry_new_customer);
        $chatId = mysqli_insert_id($conn);
        writeToLog("chat_insert_query=>".$qry_new_customer);
        //$phone = substr($wa_id,-10);

         
        $qry_check = "SELECT * FROM chat_customer WHERE id='$chatId'";
        $rsc_exist_customer = mysqli_query($conn,$qry_check);
        $customer_det = mysqli_fetch_assoc($rsc_exist_customer);

        #writeToLog("checkquery=>".$qry_check);
        
     }

    if($customer_det['interaction_type']!='bot')
    {

        $curl = curl_init();

        curl_setopt_array($curl, array(
		
            CURLOPT_URL => 'http://172.12.10.30/api/save_new_msg/',
            //CURLOPT_URL => 'http://192.168.10.23/api/save_new_msg/',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            //    CURLOPT_POSTFIELDS =>'{"api_key":"ZrnkLYOVlsB0ylrmgYZhtCygAK","clientid":"301","customer_no":"'.$from.'","msg":"'.$text.'","customer_name":"'.$name.'","session_id":"'.$session_id.'"}',
            CURLOPT_POSTFIELDS =>'{"clientid":"'.$client_id.'","api_key":"'.$api_key.'","content_type":"'.$type.'","customer_no":"'.$from.'","msg":"'.$msg.'","customer_name":"'.$username.'","session_id":"'.$session_id.'"}',

            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json'
                
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);

    
        writeToLog("save_msg_resp".$response);
    
    }else{

        $chatId = $customer_det['id'];
        $last_msg_Id = $customer_det['last_msg_id'];
        $last_option_id = $customer_det['last_option_id'];
        $phase = $customer_det['phase'];
        $select_last_msg_det = "select * from chat_customermsg where id='$last_msg_Id' limit 1";
        $rsc_last_msg_det = mysqli_query($conn,$select_last_msg_det);
        $last_msg_det = mysqli_fetch_assoc($rsc_last_msg_det);

        $update_new_customer = "update chat_customer set last_msg_id='$last_msg_insertid',updated_at=now(),updated_by='0' where id='$chatId'";
        $rsc_new_customer = mysqli_query($conn,$update_new_customer);
        
        
        $select_option_det = "select * from dialdee_bot_master_meta where client_id='$client_id' and id='$payload' limit 1";
        $rsc_option_det = mysqli_query($conn,$select_option_det);
        $option_det =  mysqli_fetch_assoc($rsc_option_det);
        writeToLog("option qry ".$select_option_det);
        
        
        
        $select_last_option_det = "select * from dialdee_bot_master_meta where client_id='$client_id' and id='$last_option_id' limit 1";
        $rsc_last_option_det = mysqli_query($conn,$select_last_option_det);
        $last_option_det =  mysqli_fetch_assoc($rsc_last_option_det);
        writeToLog("last option qry ".$select_last_option_det);

        if($get_msg_from && !empty($payload))
        {
            $msg = $option_det['OptionName'];
        }
        else if(!empty($explode))
        {
            $msg = $explode[count($explode)-1];
        }
        
        $qry_new_msg = "INSERT INTO chat_customermsg SET client_id='$client_id',contact_id='$chatId',msg_id='$message_id',option_id='$option_id',
            last_option_id='$payload',optionType='{$last_option_det['OptionType']}',msg='$msg',content_type='text',sender_type='customer',
            created_at=NOW(),is_bot_msg='1',is_bot_resp_send='0',mail='$req_json',created_by='0'"; 
        $rsc_new_msg = mysqli_query($conn,$qry_new_msg);
        $last_msg_insertid = mysqli_insert_id($conn);

        writeToLog("new message qry ".$qry_new_msg);
        
        $det = array();

        // if(empty($last_msg_det) && empty($payload))
        // {
        //     writeToLog("json ->".json_encode($last_msg_det));
           
        //     #print_r($det);exit;
        // }

        writeToLog("check data ->".json_encode($current_det));
        $option_id =  $current_det['id'];
        $OptionType = $current_det['OptionType'];
        $OptionName = $current_det['OptionName'];

        writeToLog("OptionType ->".$OptionType);

        switch($OptionType) {
            case 'view_products':
                #writeToLog("get deals");
                get_deals($conn,$client_id,$from,$api_key,$chatId,$app_id,$OptionType);
                //$det = send_large_option($from,$OptionName);

                break;
            case 'ordering_process':
                $msg = exchange_text_with_value($conn,$client_id,$chatId,$shopify,$OptionName);
                $det = send_large_option($from,$msg);
                $rec_data = make_recipient($from);
                send_msg($det,$api_key,$app_id,$rec_data);
                #writeToLog("check data ->ordering_process");
                break;

            case 'ticket_option':
                writeToLog("ticket ->".$OptionType);

                $phase = $current_det['phase1'];

                if($phase == 'Return')
                {
                    $ticket_type = "Return/Exchange/Cancellation/Refund";

                }else if($phase == 'Assistant')
                {
                    $ticket_type = "Assistant";
                }
                
                $det = ticket_create_request($conn,$client_id,$api_key,$from,$chatId,$conn_local,$current_det,$ticket_type);
                $rec_data = make_recipient($from);
                send_msg($det,$api_key,$app_id,$rec_data);
                writeToLog("ticket data".json_encode($det));
                break; 
                
            case 'feedback':
                $msg = exchange_text_with_value($conn,$client_id,$chatId,$shopify,$OptionName);
                $det = send_large_option($from,$msg);
                $rec_data = make_recipient($from);
                send_msg($det,$api_key,$app_id,$rec_data);
                break;

            case 'chat_to_agent':

                writeToLog("chat transfer".json_encode($current_det));
                chat_transfer($conn,$chatId);
                $det = send_large_option($from,"Our Executive Will Connect You soon.");
                $rec_data = make_recipient($from);
                send_msg($det,$api_key,$app_id,$rec_data);
                break;
                
            default:
                // Handle the default case if none of the known payloads match
                $det = welcome_msg($conn,$client_id,$from,$api_key,$chatId,$conn_local,$app_id);
                break;
                
        }
       

        $new_last_option_id = $det['last_option_id'];
        #writeToLog(" last option id-".$new_last_option_id);
        #print_r($det);exit;
        $data = $det['data'];
        $resp_msg = $det['resp_msg'];
        $resp_msg2 = $det['resp_msg2'];

        writeToLog('main data'. json_encode($data));

        $recipientdata = array();
        $recipientdata['id'] = $from;

        $recipient = json_encode($recipientdata);

        writeToLog('recipient data'. json_encode($recipientdata));

        

        if(!empty($data))
        {
            $curl = curl_init();
            $url = 'https://graph.facebook.com/'.$app_id.'/messages?access_token='.$api_key;
            writeToLog('url'. $url);

            curl_setopt_array($curl, array(
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS =>array("recipient"=>$recipient,'message'=>$data)
            ));

            $response = curl_exec($curl);

            curl_close($curl);
            //echo $response;
            $err = curl_error($curl);
            writeToLog("req".$data);
            writeToLog("msg_send_resp".$response);
        }

        #exit;

    }

}    



?>
