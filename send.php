
<?php

require_once __DIR__ . '/vendor/autoload.php';
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
use Iodev\Whois\Factory;
use Iodev\Whois\Exceptions\ConnectionException;
use Iodev\Whois\Exceptions\ServerMismatchException;
use Iodev\Whois\Exceptions\WhoisException;

$domains = ["edinaldoribeiro.com.br", "epics.com.br", "agencialed.com.br"];

$connection = new AMQPStreamConnection('localhost', 5672, 'guest', 'guest');
$channel = $connection->channel();

$channel->queue_declare('hello', false, false, false, false);

foreach ($domains as $domain) {
    
    $whois = Factory::get()->createWhois();

    try {
        $info = $whois->loadDomainInfo($domain);

        if (!$info) {
            print "Null if domain available";
            exit;
        }

        $data = 'Domínio: ' . $info->domainName . "\n" .
                'Proprietário: ' . $info->owner . "\n" .
                'status: ' . $info->states[array_key_last($info->states)] . "\n" .
                'Criado em: ' . date("d/m/Y", $info->creationDate) . "\n" . 
                'Expira em: ' . date("d/m/Y", $info->expirationDate) . "\n" .
                'Disponível: ' . $whois->isDomainAvailable($domain) ? "Sim" : "Não" . "\n" . "\n";

    } catch (ConnectionException $e) {
        print "Disconectado ou tempo limite de coexão atingido.";
    } catch (ServerMismatchException $e) {
        print "TLD server (for $domain) not found in current server hosts";
    } catch (WhoisException $e) {
        print "Whois retornou erro: '{$e->getMessage()}'";
    }

    $msg = new AMQPMessage($data);
    $channel->basic_publish($msg, '', 'hello');

}

$channel->close();
$connection->close();
?>