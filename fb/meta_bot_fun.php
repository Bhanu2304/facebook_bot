<?php

function writeToLog($logmessage)
{

    $myfile = fopen("log", "a") or die("Unable to open file!");

    fwrite($myfile, $logmessage . PHP_EOL);

    fclose($myfile);

}

function get_name($wa_id,$api_key)
{

    $curl = curl_init();
    $url = 'https://graph.facebook.com/'.$wa_id.'?fields=first_name%2Clast_name%2Cprofile_pic&access_token='.$api_key.'';

    curl_setopt_array($curl, array(
    CURLOPT_URL => $url,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => '',
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 0,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => 'GET',
    ));

    $response = curl_exec($curl);
    $err = curl_error($curl);
    curl_close($curl);
    
    $JsonDe = json_decode($response,true);
    return $JsonDe;
}

function welcome_msg($conn,$client_id,$from,$api_key,$chatId,$conn_local,$app_id)
{
    $select_welcome_msg = "select * from dialdee_bot_master_meta where client_id='$client_id' and parent_id='0' limit 1";
    #writeToLog("qry-welcome-> ".$select_welcome_msg);
    $rsc_welcome_msg = mysqli_query($conn,$select_welcome_msg);
    $welcome_msg_det =  mysqli_fetch_assoc($rsc_welcome_msg);
    $welcome_msg = $welcome_msg_det['OptionName'];
    writeToLog("json ".json_encode($welcome_msg_det));
    $welcome_msg = exchange_text_with_value($conn,$client_id,$chatId,$shopify,$welcome_msg);
    
    writeToLog("welcome msg => $welcome_msg");
    $last_option_id = $option_id = $welcome_msg_det['id'];
    $text_type = $welcome_msg_det['OptionType'];

    if(strlen($welcome_msg)>18)     
    {
        $req_data = send_large_option($from,$welcome_msg);
        writeToLog("welcome msg1 => $req_data");

        $rec_data = make_recipient($from);

        send_msg($req_data,$api_key,$app_id,$rec_data);
        #$welcome_msg = "Main Menu";
        
    }
        
    $det = true;
    
    if($det==true)
    {
        
        $return = get_childs($conn,$client_id,$from,$api_key,$chatId,$last_option_id,$welcome_msg_det,$conn_local);
        #return array('last_option_id'=>$last_option_id,'data'=>$data,'resp_msg'=>$resp);
    }
            
    return array('last_option_id'=>$last_option_id,'data'=>$return['data'],'resp_msg'=>$return['resp']);
}

function exchange_text_with_value($conn,$client_id,$chatId,$shopify,$exchange_msg)
{
    $textfield_format = array('customer-name','brand-name','privacy-policy','refund-policy','shipping-policy','url');
    foreach($textfield_format as $format)
    {
        $pos = strpos($exchange_msg,$format);
        if($pos!==false)
        {
            if($format=='customer-name')
                {$value = get_customer_name($conn,$chatId);}
            else if($format=='brand-name')
                {$value = get_brand_name($conn,$client_id);}
            else if(!empty($shopify))
                {
                    $policy_det = $shopify->Policy->get();
                    foreach($policy_det as $policy)
                    {
                        if($policy['handle']==$format)
                        {
                            $value = $policy['url'];
                        }
                    }
                }
            else if($format=='url')   
            {
                {$value = get_url($conn,$client_id);}
            } 
        }
        $exchange_msg = str_replace("{"."$format"."}","$value",$exchange_msg);
    }
    return $exchange_msg;
}

function get_brand_name($conn,$client_id)
{
    $qry_company_name = "select * from registration_master where company_id='516' limit 1";
    writeToLog("brand query .$qry_company_name");
    $rsc_company_name = mysqli_query($conn,$qry_company_name);
    $comp_det = mysqli_fetch_assoc($rsc_company_name);
    return $comp_det['company_name'];
}

function get_childs($conn,$client_id,$from,$api_key,$chatId,$option_id,$option_det,$conn_local)
{
    
    if(strtolower($option_det['OptionType'])=='questionnaire' || strtolower($option_det['OptionType'])=='question')
    {
        $recursion_limit = 1;
        return question($conn,$client_id,$api_key,$from,$chatId,$option_det['id'],$option_det,$conn_local,$recursion_limit);
    }
    #if(strtolower($option_det['OptionType'])=='menu' || strtolower($option_det['OptionType'])=='main menu')
    else
    {
        $Label = "Menu";
        $select_child_radio = "select * from dialdee_bot_master_meta where client_id='$client_id' and parent_id='$option_id'";
        #writeToLog("child id qry - > $select_child_radio");
        $rsc_child_radio = mysqli_query($conn,$select_child_radio);
        if(mysqli_num_rows($rsc_child_radio)>3)
        {
            $rows = array();
            $item_count = 0;

            $menuData = array(
                "text" => "Menu",
                "quick_replies" => array()
            );

            while($row = mysqli_fetch_assoc($rsc_child_radio))
            {

                $menuData["quick_replies"][] = array(
                    "content_type" => "text",
                    "title" => $row['OptionName'],
                    "payload" => $row['id']
                );

            }

            $rows_json = json_encode($menuData);

            #writeToLog("make-request".$rows_json);
            #$data = make_request($from,$Label,$rows_json);
        }
    
        return array('data'=>$rows_json,'resp'=>$resp);
    }
    
}

function send_large_option($from,$msg)
{
    $msg1 = utf8_encode($msg);
    $recipent = array('id'=>$from,"message"=>$msg1);
    $msg = array("text"=>$msg1);
    $data = json_encode($msg);
    writeToLog("json error ->".json_last_error());
    return $data;
}

function send_msg($data,$api_key,$app_id,$rec_data)
{

    writeToLog("message1".$data);
    writeToLog("recipient1".$rec_data);

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
        CURLOPT_POSTFIELDS =>array("recipient"=>$rec_data,'message'=>$data)
    ));

    $response = curl_exec($curl);

    curl_close($curl);
    //echo $response;
    $err = curl_error($curl);
    writeToLog("req".$data);
    writeToLog("msg_send_resp".$response);

}

function make_recipient($from)
{
    $recipent_id = array('id'=>$from);
    $data = json_encode($recipent_id);
    return $data;
}

function get_url($conn,$client_id)
{
    $sel_from_qry = "SELECT * FROM `dialdee_ecommerce` WHERE client_id='516'";
    $rsc_from_qry = mysqli_query($conn,$sel_from_qry);
    $ecom = mysqli_fetch_assoc($rsc_from_qry);
    // $ecom_token = $ecom['ecom_token'];
    // $client_secret = $ecom['client_secret'];
    $website_url = $ecom['website_url'];
    return $website_url;
}


function get_deals($conn,$client_id,$from,$api_key,$chatId,$app_id,$OptionType)
{
    $shopify = get_shopify($conn,$client_id);
    $discount_list = discount_codes($shopify);
    $srno = 1;
    writeToLog("deals list-".json_encode($discount_list));

     
    if($discount_list)
    {
        foreach($discount_list as $discount_det)
        {
            $discount_code = $discount_det['title'];
            $discount_id = $discount_det['id'];
            $target_selection = $discount_det['target_selection'];
            writeToLog("deals details - $srno ->".json_encode($discount_det)); 
            if(strtolower($target_selection)!='all')
            {

              foreach($discount_det['prerequisite_variant_ids'] as $varient_id)
              {
                  $prod = $shopify->ProductVarient($varient_id)->get();
                  $resp_msg .= make_product($conn,$client_id,$api_key,$from,$chatId,$prod,$discount_code,$discount_id,$app_id);
              }
              foreach($discount_det['entitled_variant_ids'] as $varient_id)
              {
                  $prod = $shopify->ProductVarient($varient_id)->get();
                  $resp_msg .= make_product($conn,$client_id,$api_key,$from,$chatId,$prod,$discount_code,$discount_id,$app_id);
              }
              foreach($discount_det['entitled_product_ids'] as $product_id)
              {
                  $prod = $shopify->Product($product_id)->get();
                  $resp_msg .= make_product($conn,$client_id,$api_key,$from,$chatId,$prod,$discount_code,$discount_id,$app_id);
              }
              foreach($discount_det['prerequisite_product_ids'] as $product_id)
              {
                  $prod = $shopify->Product($product_id)->get();
                  $resp_msg .= make_product($conn,$client_id,$api_key,$from,$chatId,$prod,$discount_code,$discount_id,$app_id);
              }
            }
            else
            {
                get_products_bytype($conn,$client_id,$api_key,$from,$chatId,$option_id,'all',$discount_code,$discount_id,$app_id);
            }
             $srno++;

            $select_priority_radio = "select * from dialdee_bot_master_meta where OptionType='$OptionType'  AND priority > '1'  limit 1";
            $rsc_priority_radio = mysqli_query($conn,$select_priority_radio);
            $current_det = mysqli_fetch_assoc($rsc_priority_radio);
            $option_id = $current_det['id'];
            $det = false;
            if(!empty($current_det))
            {
                $det = true;
            }
            if($det==true)
            {
                $msg = $current_det['OptionName'];
        
                writeToLog("priority ->".$select_priority_radio);
        
                #$det = send_large_option($from,$msg);
                $select_child_radio = "select * from dialdee_bot_master_meta where client_id='$client_id' and parent_id='$option_id'";
                $rsc_child_radio = mysqli_query($conn,$select_child_radio);

                $menuData = array(
                    "text" => $msg,
                    "quick_replies" => array()
                );
    
                while($row = mysqli_fetch_assoc($rsc_child_radio))
                {
    
                    $menuData["quick_replies"][] = array(
                        "content_type" => "text",
                        "title" => $row['OptionName'],
                        "payload" => $row['id']
                    );
    
                }
    
                $rows_json = json_encode($menuData);

                $rec_data = make_recipient($from);
                send_msg($rows_json,$api_key,$app_id,$rec_data);

                #$return = get_childs($conn,$client_id,$from,$api_key,$chatId,$last_option_id,$welcome_msg_det,$conn_local);
            }

            
        }
       
    }
    else
    {
        #$subject = "No Discounts Found";
        #not_found($conn,$client_id,$phone_id,$from,$api_key,$chatId,$subject);
        get_products_bytype($conn,$client_id,$api_key,$from,$chatId,$option_id,'all',$footer,$discount_id,$app_id);


    }
    

}


function get_shopify($conn,$client_id)
{
    $sel_from_qry = "SELECT * FROM `dialdee_ecommerce` where client_id='516' limit 1";
    $rsc_from_qry = mysqli_query($conn,$sel_from_qry);
    $ecom = mysqli_fetch_assoc($rsc_from_qry);
    
    $ecom_token = $ecom['ecom_token'];
    $client_secret = $ecom['client_secret'];
    $website_url = $ecom['website_url'];

    $config = array(
        'ShopUrl' => $website_url,
        'AccessToken' => $client_secret,
    );
    
    
    $shopify = PHPShopify\ShopifySDK::config($config);  
    return $shopify;
}

function make_product($conn,$client_id,$api_key,$from,$chatId,$prod,$discount_code,$discount_id,$app_id)
{
    $title = $prod['title'];
    $src = $prod['image']['src'];
    $mrp = $prod['variants'][0]['compare_at_price'];
    $fmrp = $prod['variants'][0]['price'];
    $product_id = "product_id@{$prod['id']}@{$discount_id}";
    $replys = array();
    $price_item = get_price_item($title,$mrp,$fmrp);
    $footer = $discount_code;

    $attachmentData = array(
        "attachment" => array(
            "type" => "template",
            "payload" => array(
                "template_type" => "generic",
                "elements" => array()
            )
        )
    );
    $slug = str_replace(' ', '-', $title);

    writeToLog("url -> https://voyageinc.in/products/".$slug);

    $attachmentData["attachment"]["payload"]["elements"][] = array(
        "title" => $title,
        "image_url" => $src,
        "subtitle" => $price_item."\n".$footer,
        "default_action" => array(
            "type" => "web_url",
            "url" => "https://voyageinc.in/products/".$slug,
            "webview_height_ratio" => "tall"
        ),
        "buttons" => array(
            array(
                "type" => "web_url",
                "url" => "https://voyageinc.in/products/".$slug,
                "title" => "View Product"
            )        
        )
    );






    // $product_req = make_product_request($from,$footer,$src,$price_item,$buttons);
    writeToLog("product item->".json_encode($attachmentData));
    $product_data  = json_encode($attachmentData);
    $rec_data = make_recipient($from);
    send_msg($product_data,$api_key,$app_id,$rec_data);
    
    $qry_new_msg = "INSERT INTO chat_customermsg SET client_id='$client_id',contact_id='$chatId',
            msg='$src',content_type='file',sender_type='bot',
            created_at=NOW(),created_by='1'"; 
            $rsc_new_msg = mysqli_query($conn,$qry_new_msg);
            $resp_msg_insertid = mysqli_insert_id($conn);
            

    
      
      $resp_msg .="<br/> ".$prod['title'];
      return $resp_msg;
}

function get_price_item($title,$mrp,$fmrp)
{
    return  "MRP: Rs. ~$mrp~

Final Price: Rs. *$fmrp*";
}

function discount_codes($shopify)
{
    return $shopify->PriceRule()->get();
}

function make_reply_button($product_id,$title)
{
    $title1 = substr($title,0,20);
    $button = array('type'=>'web_url','url'=>$product_id,'title'=>$title1);
    return $button;
}

function get_products_bytype($conn,$client_id,$phone_id,$api_key,$from,$chatId,$option_id,$product_type,$footer,$discount_id,$app_id)
{
    $sel_from_qry = "SELECT * FROM `dialdee_ecommerce` WHERE phoneno_id='$phone_id'";
    $rsc_from_qry = mysqli_query($conn,$sel_from_qry);
    $ecom = mysqli_fetch_assoc($rsc_from_qry);
    
    $ecom_token = $ecom['ecom_token'];
    $client_secret = $ecom['client_secret'];
    $website_url = $ecom['website_url'];

    $config = array(
        'ShopUrl' => $website_url,
        'AccessToken' => $client_secret,
    );
    
    
    
    $shopify = PHPShopify\ShopifySDK::config($config);    
    $product_list = $shopify->Product->get();
    
    $resp_msg = $label = "Choose Product";
      $rows = array();
      foreach($product_list as $prod)
      {
        if(strtolower($product_type)!='all')
        {
            if($product_type!=$prod['product_type'])
            {
                continue;
            }
        }
        writeToLog("product details->".json_encode($prod));
        
          $title = $prod['title'];
          $src = $prod['image']['src'];
          $mrp = $prod['variants'][0]['compare_at_price'];
          $fmrp = $prod['variants'][0]['price'];
          $product_id = "product_id@{$prod['id']}@{$discount_id}";
          $replys = array();
          $price_item = get_price_item($title,$mrp,$fmrp);
          $replys[] = make_reply_button($product_id,'Add To Cart');
          $replys[] = make_reply_button('chat to agent','Chat To Agent');
          $buttons = array('buttons'=>$replys);
          $product_req = make_product_request($from,$footer,$src,$price_item,$buttons);
          writeToLog("product list->".json_encode($product_req));
          send_msg($product_req,$api_key);
          writeToLog("product qry=>".$product_req);
          
          
          $qry_new_msg = "INSERT INTO chat_customermsg SET client_id='$client_id',contact_id='$chatId',msg_id='$message_id',option_id='$option_id',
            last_option_id='$last_option_id',optionType='{$last_option_det['OptionType']}',msg='$src',content_type='file',sender_type='customer',
            created_at=NOW(),created_by='1'"; 
            $rsc_new_msg = mysqli_query($conn,$qry_new_msg);
            $resp_msg_insertid = mysqli_insert_id($conn);
            #$update_lastoption_customer = "update chat_customer set last_option_id='$option_id',last_msg_id='$resp_msg_insertid',updated_at=now(),updated_by='1' where id='$chatId'"; 
            #$rsc_lastoption_customer = mysqli_query($conn,$update_lastoption_customer);
            $resp_msg .="<br/> ".$prod['title'];
      }
      
    
    $data = '';
    //$req = send_large_option($from,"Please Select From Above");
    return array('last_option_id'=>0,'data'=>$data,'resp_msg'=>$resp_msg);


}


function ticket_create_request($conn,$client_id,$api_key,$from,$chatId,$conn_local,$option_det,$ticket_type)
{
    $shopify = get_shopify($conn,$client_id);
    $select_order_det = "select * from chat_customer where client_id='$client_id' and id='$chatId' limit 1";
    $rsc_order_det = mysqli_query($conn,$select_order_det);
    $order_det =  mysqli_fetch_assoc($rsc_order_det);
    // $product_id =$order_det['product_id'];
    // $order_id = $order_det['order_id'];
    // $product = $shopify->Product($product_id)->get();
    // $product_name = $product['title'];
    // $order_info = $shopify->Order($order_id)->get();
    // $order_no = $order_info['name'];
    
    $reason = "Bot Chat - $ticket_type From $from";
    $remarks = "Ticket id generated From $from. ";
    $ticket_id = get_ticket($conn_local,$client_id,$api_key,$from,$chatId,$reason,$remarks);
    $format = "{ticket-no}";
    $msg = $option_det['OptionName'];
    $msg = dynamic_text($format,$ticket_id,$msg);
    $req = send_large_option($from,$msg);
    return $req;
    #return array('last_option_id'=>$option_det['id'],'data'=>$req,'resp_msg'=>$req);
}

function dynamic_text($format,$variable,$exchange_msg)
{
    $exchange_msg2 = str_replace("$format","$variable",$exchange_msg);
    return $exchange_msg2;
}

function get_ticket($conn_local,$client_id,$api_key,$from,$chatId,$reason,$remarks)
{
    $qry_last_ticket_no = "select id,SrNo,subject from call_master where RefrenceId='$chatId' limit 1";
    $rsc_last_ticket_no = mysqli_query($conn_local,$qry_last_ticket_no);
    $last_ticket_no = mysqli_fetch_assoc($rsc_last_ticket_no) ;
    
    if(!empty($last_ticket_no))
    {
        $ticket_id = $last_ticket_no['SrNo'];
        $upd_id = $last_ticket_no['id'];
        $upd_last_ticket_no = "update call_master set subject='$new_subject' where id='$upd_id' limit 1";
        $rsc_last_ticket_no = mysqli_query($conn_local,$upd_last_ticket_no);
        return $ticket_id;
    }
    else
    {
        $phone = substr($from,-10);

        $qry_last_ticket_no = "select Max(SrNo) srno from call_master where ClientId='$client_id'";
        $rsc_last_ticket_no = mysqli_query($conn_local,$qry_last_ticket_no);
        $last_ticket_no = mysqli_fetch_assoc($rsc_last_ticket_no) ;
        $SrNo = $last_ticket_no['srno'] + 1;

        $qry_new_ticket = "INSERT INTO call_master SET clientid='$client_id',DeptId='$DeptId',UserType='Bot',RefrenceId='$chatId',
        ContactId='$chatId',SubTagType='Bot',TagStatus='1',SrNo='$SrNo',MSISDN='$phone',
        LeadId='0',Subject='$reason',CallDate=now(),AgentId='0',CallType='Facebook',escalation_no='0'";
        $rsc_new_ticket = mysqli_query($conn_local,$qry_new_ticket);
        $ticket_id = mysqli_insert_id($conn_local);
        writeToLog("ticket_insert_query=>".$qry_new_ticket); 
    }
        
    return $ticket_id;    

}

function chat_transfer($conn,$chatId)
{
    $chat_from_qry = "update `chat_customer` set interaction_type='Facebook',allocate_at=now(),allocate_by='0' WHERE id='$chatId' limit 1";
    #writeToLog("chat transfer query=>".$chat_from_qry);
    $rsc_upd = mysqli_query($conn,$chat_from_qry);  
    $curl = curl_init();

    curl_setopt_array($curl, array(
    
        CURLOPT_URL => 'http://172.12.10.30/api/allocate_bot',
        //CURLOPT_URL => 'http://192.168.10.23/api/save_new_msg/',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1, 
        CURLOPT_CUSTOMREQUEST => 'POST',
        //CURLOPT_POSTFIELDS =>'{"api_key":"ZrnkLYOVlsB0ylrmgYZhtCygAK","clientid":"301","customer_no":"'.$from.'","msg":"'.$text.'","customer_name":"'.$name.'","session_id":"'.$session_id.'"}',
        CURLOPT_POSTFIELDS =>'{"chatId":"'.$chatId.'"}',

        CURLOPT_HTTPHEADER => array(
            'Content-Type: application/json',
        ),
    ));

    $response = curl_exec($curl);

    curl_close($curl);
      
}



?>