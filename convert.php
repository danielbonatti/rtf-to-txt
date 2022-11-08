<?php
    require_once 'src/Scanner.php';
    require_once 'src/Parser.php';
    require_once 'src/Document.php';
    require_once 'src/Node/Node.php';
    require_once 'src/Node/BlockNode.php';
    require_once 'src/Node/CharNode.php';
    require_once 'src/Node/CtrlWordNode.php';
    require_once 'src/Node/ParNode.php';
    require_once 'src/Node/TextNode.php';

    // ======================================

    function extractText(string $text, array $config) {
        $scanner = new RtfParser\Scanner($text);
        $parser = new RtfParser\Parser($scanner);
        $text = '';
        $doc = $parser->parse();
        foreach ($doc->childNodes() as $node) {
            $text .= $node->text();
        }
      
        if ($config['input_encoding'] === 'guess') {
            $config['input_encoding'] = $doc->getEncoding();
            if (is_null($config['input_encoding'])) {
                $config['input_encoding'] = 'utf-8';
            }
        }
        if ($config['input_encoding'] !== $config['output_encoding']) {
            $text = mb_convert_encoding($text, $config['output_encoding'], $config['input_encoding']);
        }
        return $text;
    }

    // ======================================
    
    // Recebe a requisição
    $path = explode('/', $_GET['path']);
    $contents = file_get_contents('db.json');

    $json = json_decode($contents, true);

    $method = $_SERVER['REQUEST_METHOD'];

    header('Content-type: application/json');
    $body = file_get_contents('php://input');

    // ======================================

    // Trata a requisição via POST
    // curl -d '{"conteudo": "teste"}' -X POST 'htpp://127.0.0.1/rtf-to-txt/convert.php'
    if ($method === 'POST'){
        $jsonBody = json_decode($body, true);
        echo extractText($jsonBody['conteudo'],array("input_encoding" => "ASCII","output_enconding" => "UTF-16"));
    }
?>