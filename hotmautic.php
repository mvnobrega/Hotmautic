<?php
//Sistema de Integração entre Hotmart e Mautic

// Insira as informações vinda do Mautic
$mautic_base_site               = "URL do Mautic";

$mautic_form_name_buy           = "FORM de Compra";
$mautic_form_id_buy             = "ID FORM de Compra";

$mautic_form_name_ref           = "FORM de Reembolso";
$mautic_form_id_ref             = "ID FORM de Reembolso";

$mautic_form_name_billedprinted = "FORM de Boleto Impresso";
$mautic_form_id_billedprinted   = "ID FORM de Boleto Impresso";

// Insira as informações vinda do Hotmart
$hotmart_token      = "TOKEN HOTMART";
$hotmart_id_prod    = "ID Produto Hotmart";
$hotmart_id_test    = "1439";//1439 é o ID do produto usado para teste de API pelo Hotmart

// Informações recebidas via post do Hotmart
$hotmart_first_name = $_POST['first_name'];
$hotmart_last_name  = $_POST['last_name'];
$hotmart_mail       = $_POST['email'];
$hotmart_prod       = $_POST['prod'];

foreach ($_POST as $key => $value) {
  $$key = $value;
}

// Log de requisição para análise
log_message('*** Requisição Hotmautic ***');
log_message("Nome: $hotmart_first_name");
log_message("Sobrenome: $hotmart_last_name");
log_message("E-mail: $hotmart_mail");
log_message("Produto: $hotmart_prod");
log_message("Status: $status");

//Validação das informações e tomada da ação para cada status recebido
if ($hottok == $hotmart_token && ($hotmart_prod == $hotmart_id_prod) || ($hotmart_prod == $hotmart_id_test)) {
  switch ($status) {
    case 'billet_printed':
      post_form($mautic_base_site,
                $mautic_form_name_billedprinted,
                $mautic_form_id_billedprinted,
                $hotmart_mail,
                $hotmart_first_name,
                $hotmart_last_name,
                $hotmart_prod);
      break;

    case 'approved':
      post_form($mautic_base_site,
                $mautic_form_name_buy,
                $mautic_form_id_buy,
                $hotmart_mail,
                $hotmart_first_name,
                $hotmart_last_name,
                $hotmart_prod);
      break;

    case 'refunded':
    case 'chargeback':
    case 'dispute':
      post_form($mautic_base_site,
                $mautic_form_name_ref,
                $mautic_form_id_ref,
                $hotmart_mail,
                $hotmart_first_name,
                $hotmart_last_name,
                $hotmart_prod);
      break;

    default:
      log_message("Status não tratado: $status");
      break;
  }
}
else
{
  log_message("Chamada inválida - Acesso não liberado");
}

function post_form($base_url, $form_name, $form_id, $email, $first_name, $last_name, $prod) {
  $content = http_build_query(array('mauticform[email]'     => $email,
                                    'mauticform[nome]'      => $first_name,
                                    'mauticform[sobrenome]' => $last_name,
                                    'mauticform[idprod]'    => $prod,
                                    'mauticform[formId]'    => $form_id,
                                    'mauticform[return]'    => '',
                                    'mauticform[formName]'  => $form_name,));

  $context = stream_context_create(array('http' => array('method' => 'POST',
                                                         'content' => $content,)));

  $url = $base_url . "form/submit?formId=" . $form_id;

  log_message("URL de formulário: $url");

  $result = file_get_contents($url, null, $context);
}

function log_message($message) {
  $log_file = './hotmautic.log';

  $current_date = date('r');
  $log_messsage = "$current_date - $message\n";

  echo($log_messsage);
  file_put_contents($log_file, $log_messsage, FILE_APPEND);
}

?>

