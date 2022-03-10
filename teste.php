<?php
function modeloDocumento($urlArquivoOrigem,$urlArquivoDestino,$arrayCampos) {

    /*
     * 
     * AUTOR:   Alexandre Quintal
     * E-MAIL:  alexandrequintal@gmail.com
     * DATA:    10/03/2022
     * 
     * DESCRIÇÃO:
     * Lê um arquivo de modelo e insere valores nos campos pré-definidos, gerando um novo documento.
     * Exemplo de utilização: modelo de contratos, inserindo informações como nome da empresa, CNPJ e endereço. 
     * Exemplo de texto que deve conter dentro do arquivo de modelo: [#numerocontrato#]
     * 
     */
    
    // Variáveis
    $urlArquivoOrigem;
    $urlArquivoDestino;
    $arrayCampos;
    $retorno['status'] = true;
    $retorno['valor'] = '';
    
    // Campos obrigatórios
    if (trim($urlArquivoOrigem) == '') { $retorno['status'] = false; $retorno['valor'] .= 'Nome do arquivo de origem não foi informado.<br />'; }
    if (trim($urlArquivoDestino) == '') { $retorno['status'] = false; $retorno['valor'] .= 'Nome do arquivo de destino não foi informado.<br />'; }
    if (!is_array($arrayCampos)) { $retorno['status'] = false; $retorno['valor'] .= 'Valores não estão em um Array.<br />'; }
    
    if ($retorno['status'] == true) {
    
        // Consulta conteudo do arquivo
        $fp=fopen($urlArquivoOrigem,'r');
        $output=fread($fp,filesize($urlArquivoOrigem));
        fclose($fp);
        // Tags de abertura e fechamento dos campos
        $tagL = '[#';
        $tagR = '#]';
        // Verifica se campos existem e se estrutura do Array está Ok
        foreach ($arrayCampos as $dados) {
            if (!isset($dados['campo'])) { $retorno['status'] = false; $retorno['valor'] .= 'Campo não informado corretamente dentro do Array.<br />'; }
            if (!isset($dados['valor'])) { $retorno['status'] = false; $retorno['valor'] .= 'Valor não informado corretamente dentro do Array.<br />'; }
            if (!isset($dados['valor']) AND trim($dados['campo']) == '') { $retorno['status'] = false; $retorno['valor'] .= 'Nome do campo não informado corretamente dentro do Array.<br />'; }
            if ($retorno['status'] == true) {
                if( strpos(file_get_contents($urlArquivoOrigem),$tagL.$dados['campo'].$tagR) == false) {
                    $retorno['status'] = false;
                    $retorno['valor'] .= $tagL.$dados['campo'].$tagR." NÃO existe no documento.<br />";
                }
            }
        }
        
        if ($retorno['status'] == true) {
        
            // Altera valores
            foreach ($arrayCampos as $dados) {
                $output=str_replace($tagL.$dados['campo'].$tagR,$dados['valor'],$output);
            }
            
            // Grava no arquivo com novo conteudo
            file_put_contents($urlArquivoDestino, $output);
            
            $retorno['valor'] = $urlArquivoDestino;
        
        }
        
    }
    
    return $retorno;
    

}

// Campos a serem alterados
$arrayCampos[] = array("campo" => "numerocontrato", "valor" => 'XXXXXX');
$arrayCampos[] = array("campo" => "cnpjempresa", "valor" => 'YYYYYY');
$arrayCampos[] = array("campo" => "nomeempresa", "valor" => 'ZZZZZZ');

// Chama a função
$retorno = modeloDocumento('teste.rtf','teste2.rtf',$arrayCampos);

// Exibe a saída
if ($retorno['status']) {
    echo('Seu arquivo está pronto: <a href="'.$retorno['valor'].'">[download]</a>');
} else {
    echo('<b>Erro(s) encontrado(s):</b><br />'.$retorno['valor'].'<b>Arquivo não gerado!</b>');
}

?>