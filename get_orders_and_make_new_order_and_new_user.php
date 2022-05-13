<?php

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "wp";


try {

    //create PDO connection
    $dbds = new PDO("mysql:host=".$servername.";charset=utf8mb4;dbname=".$dbname, $username, $password);
    //$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_SILENT);//Suggested to uncomment on production websites
    $dbds->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);//Suggested to comment on production websites
    $dbds->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
    $GLOBALS['dbds'] = $dbds;


} catch(PDOException $e) {
    //show error
    echo '<p class="bg-danger">'.$e->getMessage().'</p>';
    exit;
}
function findemail($str) {
return (!preg_match("/^([a-z0-9\+_\-]+)(\.[a-z0-9\+_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}$/ix", $str)) ? FALSE : TRUE;
}
function find_old_product_id($post_id_new){
    
        $sql_wp_postmeta_post_id_ds_2 = "SELECT * FROM wp_postmeta WHERE post_id = $post_id_new";
        $query2 = $GLOBALS['dbds']->query($sql_wp_postmeta_post_id_ds_2);
        $query2->execute();
        $show2 = $query2->fetch();
        if($show2['meta_key'] == 'old_product_id'){
            return $show2['meta_value'];
           }
        // foreach($show2 as $row2){
        // //    echo $row2['meta_key'];
        //    if($row2['meta_key'] == 'old_product_id'){
        //     return $row2['meta_value'];
        //    }
        // }
}
function find_product_id($meta_id){
    
    $sql_wp_postmeta_post_id_ds_2 = "SELECT * FROM wp_woocommerce_order_itemmeta WHERE meta_id = $meta_id";
    $query2 = $GLOBALS['dbds']->query($sql_wp_postmeta_post_id_ds_2);
    $query2->execute();
    $show2 = $query2->fetch();
    return $show2['ID'];
    
}
$sql = "SELECT ID, post_author, post_date, post_status, post_type FROM wp_posts WHERE post_type = 'shop_order'";
$query = $dbds->query($sql);
$query->execute();
$shop_order_all_results = $query->fetchall();

if($shop_order_all_results != null){

    foreach($shop_order_all_results as $shop_order_dsresult_row){
       
        $_SESSION["post_id"] = $shop_order_dsresult_row['ID'];
        
        $order_id = $shop_order_dsresult_row['ID'];
        $sql_get_order_info_billing_email = "SELECT * FROM wp_postmeta WHERE post_id = $order_id";
        $query_sql_get_orders_info = $dbds->query($sql_get_order_info_billing_email);
        $query_sql_get_orders_info->execute();
        $orders_info = $query_sql_get_orders_info->fetchall();
        foreach($orders_info as $row){
            if($row['meta_key'] == '_billing_first_name'){
                $customer_name = $row['meta_value'];
            }
            if($row['meta_key'] == '_billing_last_name'){
                $customer_family = $row['meta_value'];
            }
            if($row['meta_key'] == '_billing_email'){
                $customer_email = $row['meta_value'];
            }
            if($row['meta_key'] == '_billing_company'){
                $customer_company = $row['meta_value'];
            }
            if($row['meta_key'] == '_billing_address_1'){
                $customer_billing_address_1 = $row['meta_value'];
            }
            if($row['meta_key'] == '_billing_postcode'){
                $customer_billing_postcode = $row['meta_value'];
            }
            if($row['meta_key'] == '_billing_city'){
                $customer_billing_city = $row['meta_value'];
            }
            if($row['meta_key'] == '_billing_phone'){
                $customer_billing_phone = $row['meta_value'];
            }
            }
        // add users to file
        $customers_list_file = 'Kunder.csv';
        $searchfor = $customer_email;
        header('Content-Type: text/plain');
        $contents = file_get_contents($customers_list_file);
        $pattern = preg_quote($searchfor, '/');
        $pattern = "/^.*$pattern.*\$/m";
        if(preg_match_all($pattern, $contents, $matches)){
        }
        else{
        $my_user_id = '0000'.$order_id;


        $filename = 'Kunder.csv';
        $handle = fopen($filename, "a");
        if ($handle === false) {
            die('Error opening the file ' . $filename);
        }
        $remove_space_companty = str_replace(' ', '', $customer_company);
        $remove_space_customer_billing_address = str_replace(',', '-', $customer_billing_address_1);
        $remove_space_customer_billing_address2 = str_replace(' ', '-', $remove_space_customer_billing_address);

        $data[0] = "$my_user_id;$customer_name;$customer_family;$remove_space_companty;$remove_space_customer_billing_address2;$customer_billing_postcode;$customer_billing_city;$customer_billing_phone;$customer_email";
        fputcsv($handle, $data);
        fclose($handle);

        }
               
        $order_id = $shop_order_dsresult_row['ID'];
        // echo $order_id."<br>";  
        $my_user_id = '0000'.$order_id;
        $new_order_id = '1_'.$order_id;
        $my_order_file_name = '1_'.$order_id.'.txt';

              $sql_wp_wc_order_product_lookup = "SELECT DISTINCT order_id FROM wp_wc_order_product_lookup WHERE order_id = $order_id";
        $query_wp_wc_order_product_lookup = $dbds->query($sql_wp_wc_order_product_lookup);
       $query_wp_wc_order_product_lookup->execute();
       $showquery_wp_wc_order_product_lookup = $query_wp_wc_order_product_lookup->fetchall();

       foreach($showquery_wp_wc_order_product_lookup as $row_order_info){

        
        echo $row_order_info['order_id']."<br>";
        // Make order file
       
        // $ds_product_old_id = $show_old_product_id['meta_value'];
        $my_user_id = '0000'.$order_id;
        $new_order_id = '1_'.$order_id;
        // $old_productt_id = find_old_product_id($row_woi['order_id']);
        $my_order_file_name = 'orders/arkiv/0000_'.$order_id.'.txt';
        $file_content = '"O","'.$new_order_id.'","'.$my_user_id.'","'.date("Y-m-d").'",""';
        // $file_content .= "\n".'"V",'.$new_order_id.',"'.$my_order_file_name.'","'.$ds_product_old_id.'".,3';
        if (!file_exists($my_order_file_name)) {
            $file = fopen($my_order_file_name,"w");
            echo fwrite($file,"$file_content");
            fclose($file); 
        }else{
            $add_all_product_sql = "SELECT * FROM wp_wc_order_product_lookup WHERE order_id = $order_id";
            $query_add_all_product_sql = $dbds->query($add_all_product_sql);
           $query_add_all_product_sql->execute();
           $show_query_add_all_product_sql = $query_add_all_product_sql->fetchall();
           foreach($show_query_add_all_product_sql as $row_add_products){
            $prodict_id = $row_add_products['product_id'];

            $sql_get_old_pid = "SELECT * FROM wp_postmeta WHERE post_id = $prodict_id AND meta_key = 'old_product_id'";
            $query_get_old_pid = $dbds->query($sql_get_old_pid);
            $query_get_old_pid->execute();
            $show_old_prodict_id = $query_get_old_pid->fetch();


            $x_pid = $show_old_prodict_id['meta_value'];
            $product_qty = $row_add_products['product_qty'];
            $file_content = "\n".'"V","0000_1111_'.$order_id.'","'.$x_pid.'",'.$product_qty;

            //search  for content
            $file = $my_order_file_name;
            $searchfor = $file_content;
            header('Content-Type: text/plain');
            $contents = file_get_contents($file);
            $pattern = preg_quote($searchfor, '/');
            $pattern = "/^.*$pattern.*\$/m";
            if(preg_match_all($pattern, $contents, $matches)){
                // echo "Found matches:\n";
                // echo implode("\n", $matches[0]);
             }
             else{
                $file = fopen($my_order_file_name,"a");
                echo fwrite($file,"$file_content");
                fclose($file); 
             }

                
           }
        }
       


       }

    }
}
