<?php
//Sistema de Integração entre Hotmart e Mautic

// Insira as informações vinda do Mautic
$Mautic_Base_Site = "URL do Mautic";
$Mautic_Form_Name_Buy = "FORM de Compra";
$Mautic_Form_ID_Buy = "ID Form Compra";
$Mautic_Form_Name_Ref = "FORM de Reembolso";
$Mautic_Form_ID_Ref = "ID Form Reembolso";

// Insira as informações vinda do Hotmart
$HotmartToken = "TOKEN HOTMART";
$HotmartIdProd = "ID Produto Hotmart";
$HotmartIdProdTest = "1439";//1439 é o ID do produto usado para teste de API pelo Hotmart

// Informações recebidas via post do Hotmart
$HotmartName = $_POST['first_name'];
$HotmartMail = $_POST['email'];
$HotmartProd = $_POST['prod'];
$HotmartHottok = $_POST['hottok'];
$HotmartStatus = $_POST['status'];

foreach ($_POST as $key => $value){
    $$key = $value;
}
//Validação das informações e tomada da ação para cada status recebido
if ($HotmartHottok != 0 && $HotmartHottok != null && $_POST && $HotmartHottok == $HotmartToken && ($HotmartProd == $HotmartIdProd || $HotmartProd == $HotmartIdProdTest)) {
    switch ($HotmartStatus) {
        case 'started':
            Submit_Form_Mautic_Hotmart_Buy();
            break;
        case 'approved':
            Submit_Form_Mautic_Hotmart_Buy();
            break;
        case 'refunded':
            Submit_Form_Mautic_Hotmart_Ref();
            break;
        case 'canceled':
            Submit_Form_Mautic_Hotmart_Ref();
            break;
        default:
            echo "Erro - Status Desconhecido";
            break;
    }
}

// Erro - API
else {
    echo "Erro - Acesso Não Liberado";
}

// Funções para submiter o preenchimento dos formularios do Mautic
    function Submit_Form_Mautic_Hotmart_Buy() {
        global $HotmartMail, $HotmartName,  $HotmartProd, $Mautic_Form_ID_Buy, $Mautic_Form_Name_Buy, $Mautic_Base_Site, $Mautic_Form_ID_Buy;
        $content = http_build_query(array(
            'mauticform[email]' => $HotmartMail,
            'mauticform[nome]' => $HotmartName,
            'mauticform[idprod]' => $HotmartProd,
            'mauticform[formId]' => $Mautic_Form_ID_Buy,
            'mauticform[return]' => '',
            'mauticform[formName]' => $Mautic_Form_Name_Buy,
        ));
        $context = stream_context_create(array(
            'http' => array(
                'method'  => 'POST',
                'content' => $content,
            )
        ));
        $result = file_get_contents($Mautic_Base_Site . "form/submit?formId=" . $Mautic_Form_ID_Buy, null, $context);
    }

    function Submit_Form_Mautic_Hotmart_Ref() {
        global $HotmartMail, $HotmartName,  $HotmartProd, $Mautic_Form_ID_Ref, $Mautic_Form_Name_Ref, $Mautic_Base_Site, $Mautic_Form_ID_Ref;
        $content = http_build_query(array(
            'mauticform[email]' => $HotmartMail,
            'mauticform[nome]' => $HotmartName,
            'mauticform[idprod]' => $HotmartProd,
            'mauticform[formId]' => $Mautic_Form_ID_Ref,
            'mauticform[return]' => '',
            'mauticform[formName]' => $Mautic_Form_Name_Ref,
        ));
        $context = stream_context_create(array(
            'http' => array(
                'method'  => 'POST',
                'content' => $content,
            )
        ));
        $result = file_get_contents($Mautic_Base_Site . "form/submit?formId=" . $Mautic_Form_ID_Ref, null, $context);  
    }
?>
    